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
            'status' => 'pending', // Always set to pending for trader submissions - admin must approve
        ]);

        // Update user is_seller to true (mark user as trader/seller)
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

        // Filter by country code from Accept-Country header if provided
        $countryCode = $request->header('Accept-Country');
        if ($countryCode) {
            $countryCode = strtoupper($countryCode);
            $query->whereHas('country', function ($q) use ($countryCode) {
                $q->where('code', $countryCode);
            });
        }

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

    /**
     * Get all geographical guides for the authenticated user
     * Returns all guides regardless of status (pending, approved, rejected)
     */
    public function getMyServices()
    {
        $userId = Auth::id();

        if (!$userId) {
            throw new HttpException(401, LanguageHelper::isArabic()
                ? 'يجب تسجيل الدخول أولاً'
                : 'Unauthenticated. Please login first'
            );
        }

        return GeographicalGuide::where('user_id', $userId)
            ->with(['user', 'category', 'subCategory', 'country', 'city'])
            ->latest()
            ->get();
    }

    /**
     * Get single geographical guide by ID
     * If user is authenticated and viewing their own guide, can view any status
     * Otherwise, only approved guides are visible
     */
    public function show($id): GeographicalGuide
    {
        $guide = GeographicalGuide::with(['user', 'category', 'subCategory', 'country', 'city'])->find($id);

        if (!$guide) {
            throw new HttpException(404, LanguageHelper::isArabic()
                ? 'الدليل الجغرافي غير موجود'
                : 'Geographical guide not found'
            );
        }

        // If user is authenticated and viewing their own guide, allow any status
        if (Auth::check() && $guide->user_id === Auth::id()) {
            return $guide;
        }

        // Otherwise, only show approved guides
        if (!$guide->is_active || $guide->status !== 'approved') {
            throw new HttpException(404, LanguageHelper::isArabic()
                ? 'الدليل الجغرافي غير موجود'
                : 'Geographical guide not found'
            );
        }

        return $guide;
    }

    /**
     * Update geographical guide
     */
    public function update(Request $request, $id): GeographicalGuide
    {
        $user = Auth::user();
        $guide = GeographicalGuide::where('user_id', $user->id)->find($id);

        if (!$guide) {
            throw new HttpException(404, LanguageHelper::isArabic()
                ? 'الدليل الجغرافي غير موجود'
                : 'Geographical guide not found'
            );
        }

        // Validate city belongs to country if both are being updated
        if ($request->has('city_id') && $request->has('country_id')) {
            $city = \App\Models\City::find($request->city_id);
            if ($city && $city->country_id != $request->country_id) {
                throw new HttpException(422, LanguageHelper::isArabic()
                    ? 'المدينة المحددة لا تنتمي إلى الدولة المحددة'
                    : 'Selected city does not belong to the selected country'
                );
            }
        } elseif ($request->has('city_id')) {
            // If only city is updated, check against existing country
            $city = \App\Models\City::find($request->city_id);
            if ($city && $city->country_id != $guide->country_id) {
                throw new HttpException(422, LanguageHelper::isArabic()
                    ? 'المدينة المحددة لا تنتمي إلى الدولة الحالية'
                    : 'Selected city does not belong to the current country'
                );
            }
        } elseif ($request->has('country_id')) {
            // If only country is updated, check if city belongs to new country
            $city = \App\Models\City::find($guide->city_id);
            if ($city && $city->country_id != $request->country_id) {
                throw new HttpException(422, LanguageHelper::isArabic()
                    ? 'المدينة الحالية لا تنتمي إلى الدولة المحددة'
                    : 'Current city does not belong to the selected country'
                );
            }
        }

        // Validate sub category belongs to category
        if ($request->has('geographical_sub_category_id') && $request->geographical_sub_category_id) {
            $categoryId = $request->geographical_category_id ?? $guide->geographical_category_id;
            $subCategory = \App\Models\GeographicalSubCategory::find($request->geographical_sub_category_id);
            if ($subCategory && $subCategory->geographical_category_id != $categoryId) {
                throw new HttpException(422, LanguageHelper::isArabic()
                    ? 'التصنيف الفرعي المحدد لا ينتمي إلى التصنيف المحدد'
                    : 'Selected sub category does not belong to the selected category'
                );
            }
        }

        // Handle commercial register file upload
        $data = $request->validated();

        if ($request->hasFile('commercial_register')) {
            try {
                // Delete old file if exists
                if ($guide->commercial_register && Storage::disk('public')->exists($guide->commercial_register)) {
                    Storage::disk('public')->delete($guide->commercial_register);
                }

                $file = $request->file('commercial_register');
                if ($file && $file->isValid()) {
                    $data['commercial_register'] = $file->store('geographical_guides/commercial_registers', 'public');
                }
            } catch (\Exception $e) {
                // Silently fail - don't break the request if file upload fails
            }
        }

        // If guide is approved and being edited, set status back to pending for admin review
        if ($guide->status === 'approved') {
            $data['status'] = 'pending';
        }

        $guide->update($data);

        return $guide->load(['user', 'category', 'subCategory', 'country', 'city']);
    }

    /**
     * Delete geographical guide
     */
    public function delete($id): void
    {
        $guide = GeographicalGuide::where('user_id', Auth::id())->find($id);

        if (!$guide) {
            throw new HttpException(404, LanguageHelper::isArabic()
                ? 'الدليل الجغرافي غير موجود'
                : 'Geographical guide not found'
            );
        }

        // Delete commercial register file if exists
        if ($guide->commercial_register && Storage::disk('public')->exists($guide->commercial_register)) {
            Storage::disk('public')->delete($guide->commercial_register);
        }

        // Delete the guide
        $guide->delete();
    }
}

