# دليل اختبار Banner API في Postman

## المشكلة: 404 Not Found

### السبب:
الـ API يعمل بشكل صحيح، لكن لا يوجد بانر في قاعدة البيانات للبلد واللغة المطلوبة.

### الحل:

#### 1. تشغيل Seeder للبلدان:
```bash
php artisan db:seed --class=CountriesSeeder
```

هذا سيضيف البلدان التالية:
- KSA (السعودية)
- UAE (الإمارات)
- EGY (مصر)

#### 2. إضافة بانر من Filament:
1. اذهب إلى `/admin/banners`
2. اضغط "إضافة بانر"
3. اختر:
   - البلد: الإمارات (UAE)
   - اللغة: الإنجليزية (en)
   - ارفع صورة
   - تأكد أن "نشط" مفعل
4. احفظ

#### 3. اختبار في Postman:

**Request:**
```
Method: GET
URL: http://localhost:8000/api/banners
Headers:
  Accept-Country: UAE
  Accept-Language: en
```

**Response المتوقع (200):**
```json
{
  "status": 200,
  "message": "Banner retrieved successfully.",
  "data": {
    "id": 1,
    "image": "http://localhost:8000/storage/banners/...",
    "country": {
      "id": 2,
      "code": "UAE",
      "name": "United Arab Emirates"
    },
    "language": "en",
    "is_active": true
  }
}
```

## أمثلة للاختبار:

### 1. بانر للإمارات بالعربية:
```
Accept-Country: UAE
Accept-Language: ar
```

### 2. بانر للسعودية بالإنجليزية:
```
Accept-Country: KSA
Accept-Language: en
```

### 3. بانر لمصر بالعربية:
```
Accept-Country: EGY
Accept-Language: ar
```

## ملاحظات مهمة:

1. **الأكواد المدعومة:**
   - `KSA`, `SAU`, `SA` → السعودية
   - `UAE`, `ARE`, `AE` → الإمارات
   - `EGY`, `EG` → مصر

2. **اللغات المدعومة:**
   - `ar` (العربية)
   - `en` (الإنجليزية)

3. **الشروط:**
   - البانر يجب أن يكون `is_active = true`
   - يجب أن يكون البلد موجود في جدول `countries`
   - يجب أن يكون هناك بانر للبلد واللغة المحددة

## خطوات التحقق:

### 1. التحقق من البلدان:
```bash
php artisan tinker
>>> \App\Models\Country::all();
```

### 2. التحقق من البانرات:
```bash
php artisan tinker
>>> \App\Models\Banner::with('country')->get();
```

### 3. إضافة بانر من Tinker (للاختبار):
```php
$country = \App\Models\Country::where('code', 'UAE')->first();
\App\Models\Banner::create([
    'country_id' => $country->id,
    'language' => 'en',
    'image' => 'banners/test.jpg',
    'is_active' => true,
]);
```

