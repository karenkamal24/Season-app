<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\GeographicalCategory;
use App\Models\GeographicalSubCategory;

class GeographicalCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name_ar' => 'المطاعم والمقاهي',
                'name_en' => 'Restaurants & Cafes',
                'icon' => null,
                'is_active' => true,
                'sub_categories' => [
                    ['name_ar' => 'مطاعم عربية', 'name_en' => 'Arabic Restaurants'],
                    ['name_ar' => 'مطاعم أجنبية', 'name_en' => 'International Restaurants'],
                    ['name_ar' => 'مقاهي', 'name_en' => 'Cafes'],
                    ['name_ar' => 'مطاعم سريعة', 'name_en' => 'Fast Food'],
                    ['name_ar' => 'مطاعم حلويات', 'name_en' => 'Dessert Shops'],
                ],
            ],
            [
                'name_ar' => 'الفنادق والإقامة',
                'name_en' => 'Hotels & Accommodation',
                'icon' => null,
                'is_active' => true,
                'sub_categories' => [
                    ['name_ar' => 'فنادق 5 نجوم', 'name_en' => '5 Star Hotels'],
                    ['name_ar' => 'فنادق 4 نجوم', 'name_en' => '4 Star Hotels'],
                    ['name_ar' => 'شقق فندقية', 'name_en' => 'Hotel Apartments'],
                    ['name_ar' => 'منتجعات', 'name_en' => 'Resorts'],
                    ['name_ar' => 'شقق للإيجار', 'name_en' => 'Rental Apartments'],
                ],
            ],
            [
                'name_ar' => 'المواصلات',
                'name_en' => 'Transportation',
                'icon' => null,
                'is_active' => true,
                'sub_categories' => [
                    ['name_ar' => 'تأجير سيارات', 'name_en' => 'Car Rental'],
                    ['name_ar' => 'تاكسي', 'name_en' => 'Taxi Services'],
                    ['name_ar' => 'حافلات', 'name_en' => 'Bus Services'],
                    ['name_ar' => 'مطار', 'name_en' => 'Airport Services'],
                    ['name_ar' => 'خدمات الشحن', 'name_en' => 'Shipping Services'],
                ],
            ],
            [
                'name_ar' => 'التسوق',
                'name_en' => 'Shopping',
                'icon' => null,
                'is_active' => true,
                'sub_categories' => [
                    ['name_ar' => 'مراكز تجارية', 'name_en' => 'Shopping Malls'],
                    ['name_ar' => 'متاجر ملابس', 'name_en' => 'Clothing Stores'],
                    ['name_ar' => 'متاجر إلكترونيات', 'name_en' => 'Electronics Stores'],
                    ['name_ar' => 'سوبر ماركت', 'name_en' => 'Supermarkets'],
                    ['name_ar' => 'أسواق شعبية', 'name_en' => 'Traditional Markets'],
                ],
            ],
            [
                'name_ar' => 'الصحة والطوارئ',
                'name_en' => 'Health & Emergency',
                'icon' => null,
                'is_active' => true,
                'sub_categories' => [
                    ['name_ar' => 'مستشفيات', 'name_en' => 'Hospitals'],
                    ['name_ar' => 'عيادات', 'name_en' => 'Clinics'],
                    ['name_ar' => 'صيدليات', 'name_en' => 'Pharmacies'],
                    ['name_ar' => 'خدمات طوارئ', 'name_en' => 'Emergency Services'],
                    ['name_ar' => 'مختبرات طبية', 'name_en' => 'Medical Labs'],
                ],
            ],
            [
                'name_ar' => 'الخدمات الحكومية',
                'name_en' => 'Government Services',
                'icon' => null,
                'is_active' => true,
                'sub_categories' => [
                    ['name_ar' => 'سفارات', 'name_en' => 'Embassies'],
                    ['name_ar' => 'قنصليات', 'name_en' => 'Consulates'],
                    ['name_ar' => 'دوائر حكومية', 'name_en' => 'Government Offices'],
                    ['name_ar' => 'خدمات جوازات', 'name_en' => 'Passport Services'],
                    ['name_ar' => 'خدمات تأشيرات', 'name_en' => 'Visa Services'],
                ],
            ],
            [
                'name_ar' => 'الترفيه والسياحة',
                'name_en' => 'Entertainment & Tourism',
                'icon' => null,
                'is_active' => true,
                'sub_categories' => [
                    ['name_ar' => 'متاحف', 'name_en' => 'Museums'],
                    ['name_ar' => 'حدائق', 'name_en' => 'Parks'],
                    ['name_ar' => 'شواطئ', 'name_en' => 'Beaches'],
                    ['name_ar' => 'معالم سياحية', 'name_en' => 'Tourist Attractions'],
                    ['name_ar' => 'أنشطة ترفيهية', 'name_en' => 'Recreational Activities'],
                ],
            ],
            [
                'name_ar' => 'التعليم',
                'name_en' => 'Education',
                'icon' => null,
                'is_active' => true,
                'sub_categories' => [
                    ['name_ar' => 'جامعات', 'name_en' => 'Universities'],
                    ['name_ar' => 'مدارس', 'name_en' => 'Schools'],
                    ['name_ar' => 'معاهد', 'name_en' => 'Institutes'],
                    ['name_ar' => 'مراكز تدريب', 'name_en' => 'Training Centers'],
                    ['name_ar' => 'مكتبات', 'name_en' => 'Libraries'],
                ],
            ],
            [
                'name_ar' => 'الخدمات المالية',
                'name_en' => 'Financial Services',
                'icon' => null,
                'is_active' => true,
                'sub_categories' => [
                    ['name_ar' => 'بنوك', 'name_en' => 'Banks'],
                    ['name_ar' => 'صرافات آلية', 'name_en' => 'ATMs'],
                    ['name_ar' => 'مكاتب صرافة', 'name_en' => 'Currency Exchange'],
                    ['name_ar' => 'شركات تأمين', 'name_en' => 'Insurance Companies'],
                ],
            ],
            [
                'name_ar' => 'الخدمات العامة',
                'name_en' => 'General Services',
                'icon' => null,
                'is_active' => true,
                'sub_categories' => [
                    ['name_ar' => 'مغاسل', 'name_en' => 'Laundries'],
                    ['name_ar' => 'محلات تصليح', 'name_en' => 'Repair Shops'],
                    ['name_ar' => 'خدمات تنظيف', 'name_en' => 'Cleaning Services'],
                    ['name_ar' => 'خدمات توصيل', 'name_en' => 'Delivery Services'],
                ],
            ],
        ];

        foreach ($categories as $categoryData) {
            $subCategories = $categoryData['sub_categories'] ?? [];
            unset($categoryData['sub_categories']);

            $category = GeographicalCategory::updateOrCreate(
                [
                    'name_ar' => $categoryData['name_ar'],
                ],
                [
                    'name_en' => $categoryData['name_en'],
                    'icon' => $categoryData['icon'],
                    'is_active' => $categoryData['is_active'],
                ]
            );

            // Create sub categories for this category
            foreach ($subCategories as $subCategoryData) {
                GeographicalSubCategory::updateOrCreate(
                    [
                        'geographical_category_id' => $category->id,
                        'name_ar' => $subCategoryData['name_ar'],
                    ],
                    [
                        'name_en' => $subCategoryData['name_en'],
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
