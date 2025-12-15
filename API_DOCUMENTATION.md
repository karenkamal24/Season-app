# Digital Directory API Documentation

## نظرة عامة / Overview

هذا التوثيق يشرح جميع الـ API endpoints المتاحة للـ Digital Directory System.

This documentation explains all available API endpoints for the Digital Directory System.

---

## Base URL

```
{{url}}/api
```

---

## Headers المطلوبة / Required Headers

### Accept-Language
- **مطلوب**: نعم / **Required**: Yes
- **القيم المتاحة / Available Values**: `ar` (عربي) أو `en` (English)
- **الافتراضي / Default**: `en`
- **الوصف / Description**: يحدد اللغة المراد عرض المحتوى بها

### Accept-Country (لـ Category Apps فقط)
- **مطلوب**: نعم (لـ Category Apps) / **Required**: Yes (for Category Apps only)
- **القيم المتاحة / Available Values**: كود الدولة (مثل: `UAE`, `KSA`, `EGY`)
- **الوصف / Description**: يحدد الدولة المراد جلب التطبيقات منها

---

## Categories APIs

### 1. جلب جميع التصنيفات / Get All Categories

**Endpoint:**
```
GET /api/categories
```

**Headers:**
```
Accept-Language: ar
```

**Query Parameters:**
لا يوجد / None

**Response Success (200):**
```json
{
    "status": 200,
    "message": "تم جلب التصنيفات بنجاح.",
    "meta": null,
    "data": [
        {
            "id": 1,
            "name": "الطعام",
            "icon": "http://127.0.0.1:8000/storage/categories/icons/food.png",
            "is_active": true
        },
        {
            "id": 2,
            "name": "الإقامة والفنادق",
            "icon": "http://127.0.0.1:8000/storage/categories/icons/hotels.png",
            "is_active": true
        }
    ]
}
```

**Response Fields:**
- `id` (integer): معرف التصنيف
- `name` (string): اسم التصنيف حسب اللغة المحددة في Header
- `icon` (string|null): رابط أيقونة التصنيف
- `is_active` (boolean): حالة التصنيف (نشط/غير نشط)

**ملاحظات / Notes:**
- يتم جلب التصنيفات النشطة فقط (`is_active = true`)
- الاسم يعتمد على `Accept-Language` header
- الترتيب حسب تاريخ الإنشاء (الأحدث أولاً)


**ملاحظات / Notes:**
- إذا كان التصنيف غير نشط، سيتم إرجاع 404
- إذا كان التصنيف غير موجود، سيتم إرجاع 404

---

## Category Apps APIs

### 3. جلب جميع تطبيقات التصنيفات / Get All Category Apps

**Endpoint:**
```
GET /api/digital-directory/category-apps
```

**Headers:**
```
Accept-Language: ar
Accept-Country: UAE
```

**Query Parameters:**
- `category_id` (integer, **required**): معرف التصنيف المراد جلب تطبيقاته

**Example Request:**
```
GET /api/digital-directory/category-apps?category_id=4
Headers:
  Accept-Language: ar
  Accept-Country: UAE
```

**Response Success (200):**
```json
{
    "status": 200,
    "message": "تم جلب تطبيقات التصنيفات بنجاح.",
    "meta": null,
    "data": [
        {
            "id": 1,
            "name": "مصر",
            "icon": "http://127.0.0.1:8000/storage/category_apps/icons/app-icon.jpg",
            "url": "https://example.com/app",
            "is_active": true
        },
        {
            "id": 2,
            "name": "تطبيق آخر",
            "icon": "http://127.0.0.1:8000/storage/category_apps/icons/another-app.png",
            "url": "https://example.com/another-app",
            "is_active": true
        }
    ]
}
```

**Response Fields:**
- `id` (integer): معرف التطبيق
- `name` (string): اسم التطبيق حسب اللغة المحددة في Header
- `icon` (string|null): رابط أيقونة التطبيق
- `url` (string|null): رابط التطبيق أو الخدمة
- `is_active` (boolean): حالة التطبيق (نشط/غير نشط)

**Response Error (400) - Missing Country Header:**
```json
{
    "status": 400,
    "message": "رأس Accept-Country مطلوب.",
    "meta": null,
    "data": []
}
```

**Response Error (400) - Missing Category ID:**
```json
{
    "status": 400,
    "message": "معرف التصنيف مطلوب كمعامل في الاستعلام.",
    "meta": null,
    "data": []
}
```

**Response Error (200) - Empty Result:**
```json
{
    "status": 200,
    "message": "تم جلب تطبيقات التصنيفات بنجاح.",
    "meta": null,
    "data": []
}
```

**ملاحظات / Notes:**
- `Accept-Country` header **مطلوب** (required)
- `category_id` query parameter **مطلوب** (required)
- يتم جلب التطبيقات النشطة فقط (`is_active = true`)
- يتم فلترة التطبيقات حسب:
  - الدولة المحددة في `Accept-Country` header
  - التصنيف المحدد في `category_id` parameter
- إذا كان كود الدولة غير صحيح، سيتم إرجاع قائمة فارغة
- الترتيب حسب الاسم العربي (A-Z)

---

### 4. جلب تطبيق واحد / Get Single Category App

**Endpoint:**
```
GET /api/digital-directory/category-apps/{id}
```

**Headers:**
```
Accept-Language: ar
Accept-Country: UAE
```

**URL Parameters:**
- `id` (integer, required): معرف التطبيق

**Example Request:**
```
GET /api/digital-directory/category-apps/1
Headers:
  Accept-Language: ar
  Accept-Country: UAE
```

**Response Success (200):**
```json
{
    "status": 200,
    "message": "تم جلب تطبيق التصنيف بنجاح.",
    "meta": null,
    "data": {
        "id": 1,
        "name": "مصر",
        "icon": "http://127.0.0.1:8000/storage/category_apps/icons/app-icon.jpg",
        "url": "https://example.com/app",
        "is_active": true
    }
}
```

**Response Error (400) - Missing Country Header:**
```json
{
    "status": 400,
    "message": "رأس Accept-Country مطلوب.",
    "meta": null,
    "data": []
}
```

**Response Error (404) - Not Found:**
```json
{
    "status": 404,
    "message": "لم يتم العثور على تطبيق التصنيف.",
    "meta": null,
    "data": []
}
```

**ملاحظات / Notes:**
- `Accept-Country` header **مطلوب** (required)
- إذا كان التطبيق غير نشط، سيتم إرجاع 404
- إذا كان التطبيق غير موجود، سيتم إرجاع 404
- إذا كان التطبيق لا ينتمي للدولة المحددة، سيتم إرجاع 404
- إذا كان كود الدولة غير صحيح، سيتم إرجاع 404

---

## أمثلة الاستخدام / Usage Examples

### مثال 1: جلب جميع التصنيفات بالعربية
```bash
curl -X GET "http://127.0.0.1:8000/api/categories" \
  -H "Accept-Language: ar"
```

### مثال 2: جلب تصنيف محدد بالإنجليزية
```bash
curl -X GET "http://127.0.0.1:8000/api/categories/1" \
  -H "Accept-Language: en"
```

### مثال 3: جلب تطبيقات تصنيف معين لدولة معينة
```bash
curl -X GET "http://127.0.0.1:8000/api/digital-directory/category-apps?category_id=4" \
  -H "Accept-Language: ar" \
  -H "Accept-Country: UAE"
```

### مثال 4: جلب تطبيق محدد
```bash
curl -X GET "http://127.0.0.1:8000/api/digital-directory/category-apps/1" \
  -H "Accept-Language: ar" \
  -H "Accept-Country: UAE"
```

---

## أكواد الدول المتاحة / Available Country Codes

أمثلة على أكواد الدول:
- `UAE` - الإمارات العربية المتحدة
- `KSA` - المملكة العربية السعودية
- `EGY` - مصر
- وغيرها من الدول المتاحة في قاعدة البيانات

---

## أكواد الأخطاء / Error Codes

| Code | المعنى / Meaning | الوصف / Description |
|------|------------------|---------------------|
| 200 | Success | الطلب نجح |
| 400 | Bad Request | بيانات الطلب غير صحيحة (مثل: missing headers أو parameters) |
| 404 | Not Found | المورد المطلوب غير موجود |

---

## ملاحظات مهمة / Important Notes

1. **اللغة / Language**: 
   - استخدم `Accept-Language: ar` للعربية
   - استخدم `Accept-Language: en` للإنجليزية
   - الاسم في الـ response يعتمد على هذه القيمة

2. **الدولة / Country**:
   - `Accept-Country` header مطلوب فقط لـ Category Apps APIs
   - يجب استخدام كود الدولة (مثل: `UAE`) وليس الاسم الكامل

3. **الفلترة / Filtering**:
   - Category Apps يتم فلترتها حسب الدولة والتصنيف
   - يتم جلب السجلات النشطة فقط (`is_active = true`)

4. **الصور / Images**:
   - الصور تُحفظ في `storage/app/public/`
   - يتم الوصول إليها عبر `/storage/` path
   - إذا لم تكن الصورة موجودة، سيتم إرجاع `null`

5. **الترتيب / Sorting**:
   - Categories: حسب تاريخ الإنشاء (الأحدث أولاً)
   - Category Apps: حسب الاسم العربي (A-Z)

---

## الدعم / Support

للمزيد من المساعدة، يرجى التواصل مع فريق التطوير.

For more help, please contact the development team.

