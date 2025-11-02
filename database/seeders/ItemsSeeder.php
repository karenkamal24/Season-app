<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all categories
        $boardingCategory = \App\Models\ItemCategory::where('name_en', 'Boarding')->first();
        $fundsCategory = \App\Models\ItemCategory::where('name_en', 'Funds')->first();
        $personalCategory = \App\Models\ItemCategory::where('name_en', 'Personal Essentials')->first();
        $entertainmentCategory = \App\Models\ItemCategory::where('name_en', 'Entertainment')->first();
        $electronicsCategory = \App\Models\ItemCategory::where('name_en', 'Electronics')->first();
        $clothingCategory = \App\Models\ItemCategory::where('name_en', 'Clothing')->first();
        $toiletriesCategory = \App\Models\ItemCategory::where('name_en', 'Toiletries')->first();
        $accessoriesCategory = \App\Models\ItemCategory::where('name_en', 'Accessories')->first();
        $firstAidCategory = \App\Models\ItemCategory::where('name_en', 'First Aids')->first();

        if (!$boardingCategory || !$fundsCategory || !$personalCategory || !$entertainmentCategory ||
            !$electronicsCategory || !$clothingCategory || !$toiletriesCategory || !$accessoriesCategory || !$firstAidCategory) {
            $this->command->error('Categories not found! Please run ItemCategoriesSeeder first.');
            return;
        }

        // 1. Boarding Items
        $boardingItems = [
            ['name_en' => 'Airline Check-In', 'name_ar' => 'تسجيل الوصول للطيران', 'default_weight' => 10.0, 'icon' => 'plane', 'sort_order' => 1],
            ['name_en' => 'Boarding Pass', 'name_ar' => 'تذكرة الصعود', 'default_weight' => 10.0, 'icon' => 'ticket', 'sort_order' => 2],
            ['name_en' => 'Passport / Visa / ID', 'name_ar' => 'جواز سفر / تأشيرة / هوية', 'default_weight' => 10.0, 'icon' => 'identification', 'sort_order' => 3],
        ];

        foreach ($boardingItems as $item) {
            \App\Models\Item::create(array_merge($item, ['category_id' => $boardingCategory->id]));
        }

        // 2. Funds Items
        $fundsItems = [
            ['name_en' => 'Cash', 'name_ar' => 'نقد', 'default_weight' => 9.0, 'icon' => 'banknotes', 'sort_order' => 1],
            ['name_en' => 'Credit Card', 'name_ar' => 'بطاقة ائتمان', 'default_weight' => 9.0, 'icon' => 'credit-card', 'sort_order' => 2],
        ];

        foreach ($fundsItems as $item) {
            \App\Models\Item::create(array_merge($item, ['category_id' => $fundsCategory->id]));
        }

        // 3. Personal Essentials Items
        $personalItems = [
            ['name_en' => 'Water Bottle', 'name_ar' => 'زجاجة ماء', 'default_weight' => 8.0, 'icon' => 'beaker', 'sort_order' => 1],
            ['name_en' => 'Travel Pillow', 'name_ar' => 'وسادة سفر', 'default_weight' => 7.0, 'icon' => 'moon', 'sort_order' => 2],
            ['name_en' => 'Blanket', 'name_ar' => 'بطانية', 'default_weight' => 7.0, 'icon' => 'square-3-stack-3d', 'sort_order' => 3],
            ['name_en' => 'Hand Lotion', 'name_ar' => 'كريم يد', 'default_weight' => 6.0, 'icon' => 'hand-raised', 'sort_order' => 4],
            ['name_en' => 'Face Lotion', 'name_ar' => 'كريم وجه', 'default_weight' => 6.0, 'icon' => 'face-smile', 'sort_order' => 5],
            ['name_en' => 'Medicines', 'name_ar' => 'أدوية', 'default_weight' => 9.0, 'icon' => 'pills', 'sort_order' => 6],
            ['name_en' => 'Snacks', 'name_ar' => 'وجبات خفيفة', 'default_weight' => 7.0, 'icon' => 'cake', 'sort_order' => 7],
            ['name_en' => 'Hand Sanitizer', 'name_ar' => 'معقم يد', 'default_weight' => 8.0, 'icon' => 'sparkles', 'sort_order' => 8],
            ['name_en' => 'Face Mask', 'name_ar' => 'قناع وجه', 'default_weight' => 8.0, 'icon' => 'face-mask', 'sort_order' => 9],
        ];

        foreach ($personalItems as $item) {
            \App\Models\Item::create(array_merge($item, ['category_id' => $personalCategory->id]));
        }

        // 4. Entertainment Items
        $entertainmentItems = [
            ['name_en' => 'Books', 'name_ar' => 'كتب', 'default_weight' => 4.0, 'icon' => 'book-open', 'sort_order' => 1],
            ['name_en' => 'Gaming', 'name_ar' => 'ألعاب', 'default_weight' => 4.0, 'icon' => 'gamepad', 'sort_order' => 2],
            ['name_en' => 'Streaming Shows', 'name_ar' => 'مسلسلات', 'default_weight' => 4.0, 'icon' => 'tv', 'sort_order' => 3],
            ['name_en' => 'Podcast', 'name_ar' => 'بودكاست', 'default_weight' => 4.0, 'icon' => 'microphone', 'sort_order' => 4],
        ];

        foreach ($entertainmentItems as $item) {
            \App\Models\Item::create(array_merge($item, ['category_id' => $entertainmentCategory->id]));
        }

        // 5. Electronics Items
        $electronicsItems = [
            ['name_en' => 'Cell Phone', 'name_ar' => 'هاتف محمول', 'default_weight' => 8.0, 'icon' => 'device-phone-mobile', 'sort_order' => 1],
            ['name_en' => 'SIM Card', 'name_ar' => 'شريحة SIM', 'default_weight' => 7.0, 'icon' => 'chip', 'sort_order' => 2],
            ['name_en' => 'Cell Phone Charger', 'name_ar' => 'شاحن هاتف', 'default_weight' => 7.0, 'icon' => 'bolt', 'sort_order' => 3],
            ['name_en' => 'Laptop', 'name_ar' => 'حاسوب محمول', 'default_weight' => 8.0, 'icon' => 'computer-desktop', 'sort_order' => 4],
            ['name_en' => 'Laptop Charger', 'name_ar' => 'شاحن حاسوب', 'default_weight' => 7.0, 'icon' => 'bolt', 'sort_order' => 5],
            ['name_en' => 'Tablet', 'name_ar' => 'تابلت', 'default_weight' => 7.0, 'icon' => 'device-tablet', 'sort_order' => 6],
            ['name_en' => 'Tablet Charger', 'name_ar' => 'شاحن تابلت', 'default_weight' => 6.0, 'icon' => 'bolt', 'sort_order' => 7],
            ['name_en' => 'Watch', 'name_ar' => 'ساعة', 'default_weight' => 6.0, 'icon' => 'clock', 'sort_order' => 8],
            ['name_en' => 'Watch Charger', 'name_ar' => 'شاحن ساعة', 'default_weight' => 5.0, 'icon' => 'bolt', 'sort_order' => 9],
            ['name_en' => 'Portable Battery', 'name_ar' => 'بطارية محمولة', 'default_weight' => 7.0, 'icon' => 'battery-100', 'sort_order' => 10],
            ['name_en' => 'Camera', 'name_ar' => 'كاميرا', 'default_weight' => 7.0, 'icon' => 'camera', 'sort_order' => 11],
            ['name_en' => 'Camera Charger', 'name_ar' => 'شاحن كاميرا', 'default_weight' => 6.0, 'icon' => 'bolt', 'sort_order' => 12],
            ['name_en' => 'Camera Battery', 'name_ar' => 'بطارية كاميرا', 'default_weight' => 6.0, 'icon' => 'battery-50', 'sort_order' => 13],
            ['name_en' => 'Memory Card', 'name_ar' => 'كارت ذاكرة', 'default_weight' => 6.0, 'icon' => 'chip', 'sort_order' => 14],
            ['name_en' => 'International Plug Adaptor', 'name_ar' => 'محول كهرباء دولي', 'default_weight' => 7.0, 'icon' => 'plug', 'sort_order' => 15],
            ['name_en' => 'Headphones', 'name_ar' => 'سماعات', 'default_weight' => 7.0, 'icon' => 'headphones', 'sort_order' => 16],
            ['name_en' => 'Gaming Console', 'name_ar' => 'جهاز ألعاب', 'default_weight' => 6.0, 'icon' => 'gamepad', 'sort_order' => 17],
            ['name_en' => 'Other Cable Connectors', 'name_ar' => 'كيبلات إضافية', 'default_weight' => 5.0, 'icon' => 'link', 'sort_order' => 18],
        ];

        foreach ($electronicsItems as $item) {
            \App\Models\Item::create(array_merge($item, ['category_id' => $electronicsCategory->id]));
        }

        // 6. Clothing Items
        $clothingItems = [
            ['name_en' => 'Underwear', 'name_ar' => 'ملابس داخلية', 'default_weight' => 7.0, 'icon' => 'archive-box', 'sort_order' => 1],
            ['name_en' => 'Bras', 'name_ar' => 'حمالات صدر', 'default_weight' => 7.0, 'icon' => 'shirt', 'sort_order' => 2],
            ['name_en' => 'Socks', 'name_ar' => 'جوارب', 'default_weight' => 7.0, 'icon' => 'squares-2x2', 'sort_order' => 3],
            ['name_en' => 'T-Shirt', 'name_ar' => 'تي شيرت', 'default_weight' => 6.0, 'icon' => 'shirt', 'sort_order' => 4],
            ['name_en' => 'Sweater / Sweatshirt', 'name_ar' => 'سترة / سويشيرت', 'default_weight' => 6.0, 'icon' => 'shirt', 'sort_order' => 5],
            ['name_en' => 'Hoodie', 'name_ar' => 'هودي', 'default_weight' => 6.0, 'icon' => 'shirt', 'sort_order' => 6],
            ['name_en' => 'Shirt', 'name_ar' => 'قميص', 'default_weight' => 6.0, 'icon' => 'shirt', 'sort_order' => 7],
            ['name_en' => 'Jacket / Coat', 'name_ar' => 'جاكيت / معطف', 'default_weight' => 7.0, 'icon' => 'coat-hanger', 'sort_order' => 8],
            ['name_en' => 'Pants', 'name_ar' => 'بنطلون', 'default_weight' => 6.0, 'icon' => 'adjustments-horizontal', 'sort_order' => 9],
            ['name_en' => 'Shorts', 'name_ar' => 'شورت', 'default_weight' => 6.0, 'icon' => 'squares-2x2', 'sort_order' => 10],
            ['name_en' => 'Swimsuits / Swimshorts', 'name_ar' => 'ملابس سباحة', 'default_weight' => 6.0, 'icon' => 'sun', 'sort_order' => 11],
            ['name_en' => 'Sneakers', 'name_ar' => 'حذاء رياضي', 'default_weight' => 7.0, 'icon' => 'shoe-print', 'sort_order' => 12],
            ['name_en' => 'Dress Shoes', 'name_ar' => 'حذاء رسمي', 'default_weight' => 6.0, 'icon' => 'shoe-print', 'sort_order' => 13],
            ['name_en' => 'Snow Boots', 'name_ar' => 'حذاء ثلج', 'default_weight' => 6.0, 'icon' => 'shoe-print', 'sort_order' => 14],
            ['name_en' => 'Sandals', 'name_ar' => 'صنادل', 'default_weight' => 5.0, 'icon' => 'shoe-print', 'sort_order' => 15],
        ];

        foreach ($clothingItems as $item) {
            \App\Models\Item::create(array_merge($item, ['category_id' => $clothingCategory->id]));
        }

        // 7. Toiletries Items
        $toiletriesItems = [
            ['name_en' => 'Toothbrush', 'name_ar' => 'فرشاة أسنان', 'default_weight' => 6.0, 'icon' => 'tooth', 'sort_order' => 1],
            ['name_en' => 'Toothpaste', 'name_ar' => 'معجون أسنان', 'default_weight' => 6.0, 'icon' => 'tube', 'sort_order' => 2],
            ['name_en' => 'Mouthwash', 'name_ar' => 'غسول فم', 'default_weight' => 5.0, 'icon' => 'beaker', 'sort_order' => 3],
            ['name_en' => 'Dental Floss', 'name_ar' => 'خيط أسنان', 'default_weight' => 5.0, 'icon' => 'link', 'sort_order' => 4],
            ['name_en' => 'Shampoo', 'name_ar' => 'شامبو', 'default_weight' => 6.0, 'icon' => 'sparkles', 'sort_order' => 5],
            ['name_en' => 'Bodywash / Soap', 'name_ar' => 'جل استحمام / صابون', 'default_weight' => 6.0, 'icon' => 'sparkles', 'sort_order' => 6],
            ['name_en' => 'Deodorant', 'name_ar' => 'مزيل عرق', 'default_weight' => 6.0, 'icon' => 'sparkles', 'sort_order' => 7],
            ['name_en' => 'Skincare Face Masks', 'name_ar' => 'أقنعة وجه', 'default_weight' => 4.0, 'icon' => 'face-smile', 'sort_order' => 8],
            ['name_en' => 'Face Lotion', 'name_ar' => 'كريم وجه', 'default_weight' => 6.0, 'icon' => 'face-smile', 'sort_order' => 9],
            ['name_en' => 'Sunscreen', 'name_ar' => 'واقي شمس', 'default_weight' => 6.0, 'icon' => 'sun', 'sort_order' => 10],
            ['name_en' => 'Moisturizer', 'name_ar' => 'مرطب', 'default_weight' => 5.0, 'icon' => 'sparkles', 'sort_order' => 11],
            ['name_en' => 'Shaving Supplies', 'name_ar' => 'أدوات حلاقة', 'default_weight' => 5.0, 'icon' => 'scissors', 'sort_order' => 12],
            ['name_en' => 'Nail Clippers', 'name_ar' => 'مقص أظافر', 'default_weight' => 4.0, 'icon' => 'scissors', 'sort_order' => 13],
            ['name_en' => 'Hair Products', 'name_ar' => 'منتجات شعر', 'default_weight' => 5.0, 'icon' => 'sparkles', 'sort_order' => 14],
            ['name_en' => 'Hair Brush', 'name_ar' => 'فرشاة شعر', 'default_weight' => 5.0, 'icon' => 'sparkles', 'sort_order' => 15],
            ['name_en' => 'Glasses', 'name_ar' => 'نظارة', 'default_weight' => 7.0, 'icon' => 'eye', 'sort_order' => 16],
            ['name_en' => 'Sunglasses', 'name_ar' => 'نظارة شمسية', 'default_weight' => 5.0, 'icon' => 'sun', 'sort_order' => 17],
            ['name_en' => 'Perfume / Cologne', 'name_ar' => 'عطر / كولونيا', 'default_weight' => 4.0, 'icon' => 'sparkles', 'sort_order' => 18],
            ['name_en' => 'Condoms', 'name_ar' => 'واقي ذكري', 'default_weight' => 3.0, 'icon' => 'shield-check', 'sort_order' => 19],
            ['name_en' => 'Period Products', 'name_ar' => 'منتجات الدورة الشهرية', 'default_weight' => 6.0, 'icon' => 'plus-circle', 'sort_order' => 20],
            ['name_en' => 'Birth Control', 'name_ar' => 'وسائل منع الحمل', 'default_weight' => 7.0, 'icon' => 'shield-check', 'sort_order' => 21],
        ];

        foreach ($toiletriesItems as $item) {
            \App\Models\Item::create(array_merge($item, ['category_id' => $toiletriesCategory->id]));
        }

        // 8. Accessories Items
        $accessoriesItems = [
            ['name_en' => 'Hats', 'name_ar' => 'قبعات', 'default_weight' => 5.0, 'icon' => 'tag', 'sort_order' => 1],
            ['name_en' => 'Beanie', 'name_ar' => 'بيني', 'default_weight' => 5.0, 'icon' => 'tag', 'sort_order' => 2],
            ['name_en' => 'Scarf', 'name_ar' => 'وشاح', 'default_weight' => 5.0, 'icon' => 'tag', 'sort_order' => 3],
            ['name_en' => 'Belts', 'name_ar' => 'أحزمة', 'default_weight' => 5.0, 'icon' => 'tag', 'sort_order' => 4],
            ['name_en' => 'Ties', 'name_ar' => 'ربطات عنق', 'default_weight' => 4.0, 'icon' => 'tag', 'sort_order' => 5],
            ['name_en' => 'Laundry Kit', 'name_ar' => 'عدة غسيل', 'default_weight' => 4.0, 'icon' => 'sparkles', 'sort_order' => 6],
            ['name_en' => 'Laundry Bag', 'name_ar' => 'كيس غسيل', 'default_weight' => 4.0, 'icon' => 'archive-box', 'sort_order' => 7],
            ['name_en' => 'Umbrella', 'name_ar' => 'مظلة', 'default_weight' => 5.0, 'icon' => 'cloud', 'sort_order' => 8],
            ['name_en' => 'Backpack', 'name_ar' => 'حقيبة ظهر', 'default_weight' => 6.0, 'icon' => 'briefcase', 'sort_order' => 9],
            ['name_en' => 'Tote Bag', 'name_ar' => 'حقيبة يد', 'default_weight' => 5.0, 'icon' => 'briefcase', 'sort_order' => 10],
            ['name_en' => 'Purse', 'name_ar' => 'محفظة نسائية', 'default_weight' => 5.0, 'icon' => 'wallet', 'sort_order' => 11],
            ['name_en' => 'Sling Bag', 'name_ar' => 'حقيبة كتف', 'default_weight' => 5.0, 'icon' => 'briefcase', 'sort_order' => 12],
        ];

        foreach ($accessoriesItems as $item) {
            \App\Models\Item::create(array_merge($item, ['category_id' => $accessoriesCategory->id]));
        }

        // 9. First Aids Items
        $firstAidItems = [
            ['name_en' => 'Bandages', 'name_ar' => 'ضمادات', 'default_weight' => 9.0, 'icon' => 'plus-circle', 'sort_order' => 1],
            ['name_en' => 'Pain Relievers', 'name_ar' => 'مسكنات ألم', 'default_weight' => 9.0, 'icon' => 'pills', 'sort_order' => 2],
            ['name_en' => 'Cold / Flu Medicines', 'name_ar' => 'أدوية نزلات البرد', 'default_weight' => 9.0, 'icon' => 'pills', 'sort_order' => 3],
            ['name_en' => 'Allergy Medicines', 'name_ar' => 'أدوية حساسية', 'default_weight' => 9.0, 'icon' => 'pills', 'sort_order' => 4],
            ['name_en' => 'Hand Sanitizer', 'name_ar' => 'معقم يد', 'default_weight' => 8.0, 'icon' => 'sparkles', 'sort_order' => 5],
            ['name_en' => 'Disinfecting Wipes', 'name_ar' => 'مناديل معقمة', 'default_weight' => 8.0, 'icon' => 'squares-2x2', 'sort_order' => 6],
            ['name_en' => 'Face Mask', 'name_ar' => 'قناع وجه', 'default_weight' => 8.0, 'icon' => 'face-mask', 'sort_order' => 7],
        ];

        foreach ($firstAidItems as $item) {
            \App\Models\Item::create(array_merge($item, ['category_id' => $firstAidCategory->id]));
        }

        $this->command->info('All items have been seeded successfully!');
    }
}
