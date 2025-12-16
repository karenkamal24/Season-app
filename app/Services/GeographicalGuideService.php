<?php

namespace App\Services;

use App\Models\GeographicalGuide;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Helpers\LangHelper;
use App\Helpers\LanguageHelper;

class GeographicalGuideService
{
    /**
     * Store new geographical guide
     */
    public function store(Request $request): GeographicalGuide
    {
        $user = Auth::user();

        // Validate city belongs to country
        $city = \App\Models\City::find($request->city_id);
        if ($city && $city->country_id != $request->country_id) {
            throw new HttpException(422, LanguageHelper::isArabic()
                ? 'المدينة المحددة لا تنتمي إلى الدولة المحددة'
                : 'Selected city does not belong to the selected country'
            );
        }

        // Validate sub category belongs to category
        if ($request->geographical_sub_category_id) {
            $subCategory = \App\Models\GeographicalSubCategory::find($request->geographical_sub_category_id);
            if ($subCategory && $subCategory->geographical_category_id != $request->geographical_category_id) {
                throw new HttpException(422, LanguageHelper::isArabic()
                    ? 'التصنيف الفرعي المحدد لا ينتمي إلى التصنيف المحدد'
                    : 'Selected sub category does not belong to the selected category'
                );
            }
        }

        // Handle commercial register file upload
        $commercialRegisterPath = null;
        if ($request->hasFile('commercial_register')) {
            try {
                $file = $request->file('commercial_register');
                if ($file && $file->isValid()) {
                    $commercialRegisterPath = $file->store('geographical_guides/commercial_registers', 'public');
                }
            } catch (\Exception $e) {
                // Silently fail - don't break the request if file upload fails
            }
        }

        $geographicalGuide = GeographicalGuide::create([
            'user_id' => $user->id,
            'geographical_category_id' => $request->geographical_category_id,
            'geographical_sub_category_id' => $request->geographical_sub_category_id,
            'service_name' => $request->service_name,
            'description' => $request->description,
            'phone_1' => $request->phone_1,
            'phone_2' => $request->phone_2,
            'country_id' => $request->country_id,
            'city_id' => $request->city_id,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'website' => $request->website,
            'commercial_register' => $commercialRegisterPath,
            'is_active' => true,
            'status' => $request->status ?? 'pending',
        ]);

        // Update user is_seller to true
        $user->update(['is_seller' => true]);

        return $geographicalGuide->load(['user', 'category', 'subCategory', 'country', 'city']);
    }

    /**
     * Get all geographical guides with filters
     */
    public function index(Request $request)
    {
        $query = GeographicalGuide::with(['user', 'category', 'subCategory', 'country', 'city'])
            ->where('is_active', true)
            ->where('status', 'approved'); // Only show approved guides

        // Filter by city_id if provided
        if ($request->has('city_id') && $request->city_id) {
            $query->where('city_id', $request->city_id);
        }

        // Filter by geographical_category_id if provided
        if ($request->has('geographical_category_id') && $request->geographical_category_id) {
            $query->where('geographical_category_id', $request->geographical_category_id);
        }

        // Filter by geographical_sub_category_id if provided
        if ($request->has('geographical_sub_category_id') && $request->geographical_sub_category_id) {
            $query->where('geographical_sub_category_id', $request->geographical_sub_category_id);
        }

        return $query->latest()->get();
    }
}

