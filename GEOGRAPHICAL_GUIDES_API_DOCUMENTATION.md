# 📍 Geographical Guides API Documentation

## نظرة عامة / Overview

نظام الدليل الجغرافي يسمح للمستخدمين بإضافة خدمات ومعلومات جغرافية مع إمكانية الفلترة والبحث.

The Geographical Guide system allows users to add geographical services and information with filtering and search capabilities.

---

## 🔐 Authentication

### Endpoints التي تحتاج Authentication:
- `POST /api/geographical-guides` - إنشاء دليل جغرافي جديد

### Endpoints العامة (Public):
- `GET /api/geographical-guides` - جلب الأدلة الجغرافية مع الفلترة

---

## 📋 Endpoints

### 1. إنشاء دليل جغرافي جديد / Create New Geographical Guide

**Endpoint:** `POST /api/geographical-guides`

**Authentication:** Required (Bearer Token)

**Headers:**
```
Authorization: Bearer {token}
Accept-Language: ar | en
Content-Type: multipart/form-data
```

**Request Body (Form Data):**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `geographical_category_id` | integer | ✅ Yes | معرف التصنيف الجغرافي |
| `geographical_sub_category_id` | integer | ❌ No | معرف التصنيف الفرعي |
| `service_name` | string | ✅ Yes | اسم الخدمة (max: 255) |
| `description` | string | ❌ No | الوصف (max: 1000) |
| `phone_1` | string | ❌ No | رقم الهاتف الأول (max: 20) |
| `phone_2` | string | ❌ No | رقم الهاتف الثاني (max: 20) |
| `country_id` | integer | ✅ Yes | معرف الدولة |
| `city_id` | integer | ✅ Yes | معرف المدينة |
| `address` | string | ❌ No | العنوان (max: 500) |
| `latitude` | decimal | ❌ No | خط العرض (-90 to 90) |
| `longitude` | decimal | ❌ No | خط الطول (-180 to 180) |
| `website` | string | ❌ No | الموقع الإلكتروني (URL) |
| `commercial_register` | file | ❌ No | السجل التجاري (PDF, JPG, JPEG, PNG, max: 5MB) |
| `status` | string | ❌ No | الحالة (pending, approved, rejected) - Default: pending |

**Example Request (Postman):**
```
POST {{url}}/api/geographical-guides
Headers:
  Authorization: Bearer {your_token}
  Accept-Language: ar

Body (form-data):
  geographical_category_id: 1
  geographical_sub_category_id: 1
  service_name: مطعم الشام
  description: مطعم يقدم الأكلات الشامية الأصيلة
  phone_1: +966501234567
  phone_2: +966501234568
  country_id: 1
  city_id: 1
  address: شارع الملك فهد، الرياض
  latitude: 24.7136
  longitude: 46.6753
  website: https://example.com
  commercial_register: [File Upload]
```

**Success Response (201 Created):**
```json
{
    "status": 201,
    "message": "تم إنشاء الدليل الجغرافي بنجاح.",
    "meta": null,
    "data": {
        "id": 1,
        "user": {
            "id": 1,
            "name": "Ahmed Ali",
            "email": "ahmed@example.com"
        },
        "category": {
            "id": 1,
            "name_ar": "المطاعم والمقاهي",
            "name_en": "Restaurants & Cafes",
            "name": "المطاعم والمقاهي",
            "icon": null
        },
        "sub_category": {
            "id": 1,
            "name_ar": "مطاعم عربية",
            "name_en": "Arabic Restaurants",
            "name": "مطاعم عربية"
        },
        "service_name": "مطعم الشام",
        "description": "مطعم يقدم الأكلات الشامية الأصيلة",
        "phone_1": "+966501234567",
        "phone_2": "+966501234568",
        "country": {
            "id": 1,
            "name_ar": "السعودية",
            "name_en": "Saudi Arabia",
            "name": "السعودية",
            "code": "KSA"
        },
        "city": {
            "id": 1,
            "name_ar": "الرياض",
            "name_en": "Riyadh",
            "name": "الرياض"
        },
        "address": "شارع الملك فهد، الرياض",
        "latitude": "24.71360000",
        "longitude": "46.67530000",
        "website": "https://example.com",
        "commercial_register": "http://example.com/storage/geographical_guides/commercial_registers/abc123.pdf",
        "is_active": true,
        "status": "قيد المراجعة",
        "created_at": "2025-12-15 23:55:37",
        "updated_at": "2025-12-15 23:55:37"
    }
}
```

**Notes:**
- `status` في الـ response يعرض الترجمة حسب `Accept-Language` header:
  - `ar` → "قيد المراجعة" / "موافق عليها" / "مرفوضة"
  - `en` → "Pending" / "Approved" / "Rejected"
- عند إنشاء دليل جغرافي جديد، يتم تحديث `is_seller` للمستخدم إلى `true` تلقائياً
- الملفات المرفوعة يتم حفظها في `storage/app/public/geographical_guides/commercial_registers/`

---

### 2. جلب الأدلة الجغرافية / Get Geographical Guides

**Endpoint:** `GET /api/geographical-guides`

**Authentication:** Not Required (Public)

**Headers:**
```
Accept-Language: ar | en
```

**Query Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `city_id` | integer | ❌ No | فلترة حسب المدينة |
| `geographical_category_id` | integer | ❌ No | فلترة حسب التصنيف |
| `geographical_sub_category_id` | integer | ❌ No | فلترة حسب التصنيف الفرعي |

**Important Notes:**
- ✅ يتم جلب فقط الأدلة التي `status = 'approved'` و `is_active = true`
- ✅ يمكن استخدام الفلاتر معاً أو منفصلة
- ✅ جميع الحقول اختيارية

**Example Requests:**

1. **جلب جميع الأدلة الموافق عليها:**
```
GET {{url}}/api/geographical-guides
Headers:
  Accept-Language: ar
```

2. **فلترة حسب المدينة:**
```
GET {{url}}/api/geographical-guides?city_id=1
Headers:
  Accept-Language: ar
```

3. **فلترة حسب التصنيف:**
```
GET {{url}}/api/geographical-guides?geographical_category_id=1
Headers:
  Accept-Language: en
```

4. **فلترة متعددة:**
```
GET {{url}}/api/geographical-guides?city_id=1&geographical_category_id=1&geographical_sub_category_id=1
Headers:
  Accept-Language: ar
```

**Success Response (200 OK):**
```json
{
    "status": 200,
    "message": "تم جلب الأدلة الجغرافية بنجاح.",
    "meta": null,
    "data": [
        {
            "id": 1,
            "user": {
                "id": 1,
                "name": "Ahmed Ali",
                "email": "ahmed@example.com"
            },
            "category": {
                "id": 1,
                "name_ar": "المطاعم والمقاهي",
                "name_en": "Restaurants & Cafes",
                "name": "المطاعم والمقاهي",
                "icon": null
            },
            "sub_category": {
                "id": 1,
                "name_ar": "مطاعم عربية",
                "name_en": "Arabic Restaurants",
                "name": "مطاعم عربية"
            },
            "service_name": "مطعم الشام",
            "description": "مطعم يقدم الأكلات الشامية الأصيلة",
            "phone_1": "+966501234567",
            "phone_2": "+966501234568",
            "country": {
                "id": 1,
                "name_ar": "السعودية",
                "name_en": "Saudi Arabia",
                "name": "السعودية",
                "code": "KSA"
            },
            "city": {
                "id": 1,
                "name_ar": "الرياض",
                "name_en": "Riyadh",
                "name": "الرياض"
            },
            "address": "شارع الملك فهد، الرياض",
            "latitude": "24.71360000",
            "longitude": "46.67530000",
            "website": "https://example.com",
            "commercial_register": "http://example.com/storage/geographical_guides/commercial_registers/abc123.pdf",
            "is_active": true,
            "status": "موافق عليها",
            "created_at": "2025-12-15 23:55:37",
            "updated_at": "2025-12-15 23:55:37"
        }
    ]
}
```

---

## 📊 Status Values

### Status في قاعدة البيانات:
- `pending` - قيد المراجعة
- `approved` - موافق عليها
- `rejected` - مرفوضة

### Status في Response (حسب Accept-Language):

**Arabic (Accept-Language: ar):**
- `pending` → "قيد المراجعة"
- `approved` → "موافق عليها"
- `rejected` → "مرفوضة"

**English (Accept-Language: en):**
- `pending` → "Pending"
- `approved` → "Approved"
- `rejected` → "Rejected"

---

## 🔍 Filtering Logic

### GET /api/geographical-guides

**Default Filters (Applied Automatically):**
- ✅ `is_active = true`
- ✅ `status = 'approved'`

**Optional Filters (Query Parameters):**
- `city_id` - فلترة حسب المدينة
- `geographical_category_id` - فلترة حسب التصنيف
- `geographical_sub_category_id` - فلترة حسب التصنيف الفرعي

**Example Filter Combinations:**
```
# جميع الأدلة الموافق عليها في مدينة معينة
?city_id=1

# جميع الأدلة في تصنيف معين
?geographical_category_id=1

# أدلة محددة في تصنيف فرعي
?geographical_category_id=1&geographical_sub_category_id=1

# أدلة في مدينة وتصنيف معين
?city_id=1&geographical_category_id=1
```

---

## ⚠️ Error Responses

### 422 Validation Error:
```json
{
    "status": 422,
    "message": "التصنيف مطلوب",
    "meta": null,
    "data": []
}
```

### 401 Unauthorized:
```json
{
    "status": 401,
    "message": "Unauthenticated.",
    "meta": null,
    "data": []
}
```

### 404 Not Found:
```json
{
    "status": 404,
    "message": "Resource not found",
    "meta": null,
    "data": []
}
```

---

## 📝 Validation Rules

### POST /api/geographical-guides

| Field | Rules |
|-------|-------|
| `geographical_category_id` | required, exists:geographical_categories,id |
| `geographical_sub_category_id` | nullable, exists:geographical_sub_categories,id |
| `service_name` | required, string, max:255 |
| `description` | nullable, string, max:1000 |
| `phone_1` | nullable, string, max:20 |
| `phone_2` | nullable, string, max:20 |
| `country_id` | required, exists:countries,id |
| `city_id` | required, exists:cities,id |
| `address` | nullable, string, max:500 |
| `latitude` | nullable, numeric, between:-90,90 |
| `longitude` | nullable, numeric, between:-180,180 |
| `website` | nullable, url, max:255 |
| `commercial_register` | nullable, file, mimes:pdf,jpg,jpeg,png, max:5120 |

### GET /api/geographical-guides

| Parameter | Rules |
|-----------|-------|
| `city_id` | nullable, exists:cities,id |
| `geographical_category_id` | nullable, exists:geographical_categories,id |
| `geographical_sub_category_id` | nullable, exists:geographical_sub_categories,id |

---

## 🎯 Use Cases

### 1. إضافة مطعم جديد:
```bash
POST /api/geographical-guides
- geographical_category_id: 1 (المطاعم والمقاهي)
- geographical_sub_category_id: 1 (مطاعم عربية)
- service_name: مطعم الشام
- country_id: 1
- city_id: 1
```

### 2. البحث عن مطاعم في مدينة معينة:
```bash
GET /api/geographical-guides?city_id=1&geographical_category_id=1
```

### 3. البحث عن خدمات في تصنيف فرعي:
```bash
GET /api/geographical-guides?geographical_category_id=1&geographical_sub_category_id=1
```

---

## 🔗 Related Endpoints

### Categories:
- للحصول على التصنيفات الجغرافية، استخدم endpoints التصنيفات الموجودة في النظام

### Countries & Cities:
- للحصول على الدول والمدن، استخدم endpoints الدول والمدن الموجودة في النظام

---

## 📌 Important Notes

1. **Status Management:**
   - عند إنشاء دليل جديد، الحالة الافتراضية هي `pending`
   - فقط الأدلة التي `status = 'approved'` تظهر في endpoint الجلب
   - يمكن للمسؤولين تغيير الحالة من لوحة التحكم (Filament)

2. **File Upload:**
   - الملفات المرفوعة يتم حفظها في `storage/app/public/geographical_guides/commercial_registers/`
   - تأكد من وجود `storage:link` (run: `php artisan storage:link`)
   - الصيغ المدعومة: PDF, JPG, JPEG, PNG
   - الحد الأقصى للحجم: 5 ميجابايت

3. **Language Support:**
   - جميع النصوص في الـ response تتغير حسب `Accept-Language` header
   - القيم المدعومة: `ar`, `en`
   - القيمة الافتراضية: `en`

4. **User Status:**
   - عند إنشاء دليل جغرافي، يتم تحديث `is_seller` للمستخدم إلى `true` تلقائياً

---

## 🧪 Testing Examples

### Postman Collection:

**1. Create Geographical Guide:**
```
POST {{url}}/api/geographical-guides
Method: POST
Headers:
  Authorization: Bearer {{token}}
  Accept-Language: ar
Body (form-data):
  geographical_category_id: 1
  geographical_sub_category_id: 1
  service_name: مطعم الشام
  description: مطعم يقدم الأكلات الشامية
  phone_1: +966501234567
  country_id: 1
  city_id: 1
  address: شارع الملك فهد
  latitude: 24.7136
  longitude: 46.6753
  website: https://example.com
  commercial_register: [Select File]
```

**2. Get All Approved Guides:**
```
GET {{url}}/api/geographical-guides
Method: GET
Headers:
  Accept-Language: ar
```

**3. Filter by City:**
```
GET {{url}}/api/geographical-guides?city_id=1
Method: GET
Headers:
  Accept-Language: en
```

**4. Filter by Category:**
```
GET {{url}}/api/geographical-guides?geographical_category_id=1
Method: GET
Headers:
  Accept-Language: ar
```

**5. Multiple Filters:**
```
GET {{url}}/api/geographical-guides?city_id=1&geographical_category_id=1&geographical_sub_category_id=1
Method: GET
Headers:
  Accept-Language: ar
```

---

## 📞 Support

للمساعدة أو الاستفسارات، يرجى التواصل مع فريق الدعم الفني.

For support or inquiries, please contact the technical support team.

---

**Last Updated:** December 15, 2025
**Version:** 1.0.0



