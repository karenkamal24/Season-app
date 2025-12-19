<?php

namespace App\Services;

use App\Models\GeographicalGuide;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
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

        // Handle commercial register upload
        $commercialRegisterPath = null;
        if ($request->hasFile('commercial_register')) {
            $file = $request->file('commercial_register');
            if ($file && $file->isValid()) {
                $commercialRegisterPath = $file->store(
                    'geographical_guides/commercial_registers',
                    'public'
                );
            }
        }

        $guide = GeographicalGuide::create([
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
            'status' => 'pending',
        ]);

        $user->update(['is_seller' => true]);

        return $guide->load(['user', 'category', 'subCategory', 'country', 'city']);
    }

    /**
     * Update geographical guide ✅ (FIXED)
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

        /* ---------------- Validation ---------------- */

        // City & Country validation
        if ($request->filled('city_id') && $request->filled('country_id')) {
            $city = \App\Models\City::find($request->city_id);
            if ($city && $city->country_id != $request->country_id) {
                throw new HttpException(422, LanguageHelper::isArabic()
                    ? 'المدينة المحددة لا تنتمي إلى الدولة المحددة'
                    : 'Selected city does not belong to the selected country'
                );
            }
        }

        if ($request->filled('geographical_sub_category_id')) {
            $categoryId = $request->geographical_category_id ?? $guide->geographical_category_id;
            $subCategory = \App\Models\GeographicalSubCategory::find($request->geographical_sub_category_id);

            if ($subCategory && $subCategory->geographical_category_id != $categoryId) {
                throw new HttpException(422, LanguageHelper::isArabic()
                    ? 'التصنيف الفرعي المحدد لا ينتمي إلى التصنيف المحدد'
                    : 'Selected sub category does not belong to the selected category'
                );
            }
        }

        /* ---------------- Collect Data (FIX) ---------------- */

        $allowedFields = [
            'geographical_category_id',
            'geographical_sub_category_id',
            'service_name',
            'description',
            'phone_1',
            'phone_2',
            'country_id',
            'city_id',
            'address',
            'latitude',
            'longitude',
            'website',
        ];

        $data = [];

        foreach ($allowedFields as $field) {
            if ($request->filled($field)) {
                $data[$field] = $request->input($field);
            }
        }

        /* ---------------- Validation Rules ---------------- */

        if (!empty($data)) {
            $rules = (new \App\Http\Requests\GeographicalGuide\UpdateGeographicalGuideRequest())->rules();
            $validator = Validator::make($data, $rules);

            if ($validator->fails()) {
                throw new HttpException(422, $validator->errors()->first());
            }
        }

        /* ---------------- File Upload ---------------- */

        if ($request->hasFile('commercial_register')) {
            if ($guide->commercial_register && Storage::disk('public')->exists($guide->commercial_register)) {
                Storage::disk('public')->delete($guide->commercial_register);
            }

            $file = $request->file('commercial_register');
            if ($file && $file->isValid()) {
                $data['commercial_register'] = $file->store(
                    'geographical_guides/commercial_registers',
                    'public'
                );
            }
        }

        // If rejected → back to pending
        if ($guide->status === 'rejected') {
            $data['status'] = 'pending';
        }

        /* ---------------- Save ---------------- */

        if (empty($data)) {
            throw new HttpException(422, LanguageHelper::isArabic()
                ? 'لا يوجد بيانات للتحديث'
                : 'No data provided to update'
            );
        }

        $guide->update($data);

        return $guide->refresh()->load([
            'user',
            'category',
            'subCategory',
            'country',
            'city'
        ]);
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

        if ($guide->commercial_register && Storage::disk('public')->exists($guide->commercial_register)) {
            Storage::disk('public')->delete($guide->commercial_register);
        }

        $guide->delete();
    }
}
