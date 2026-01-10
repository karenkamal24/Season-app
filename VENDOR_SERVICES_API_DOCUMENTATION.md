# Vendor Services API Documentation

## نظرة عامة
هذا التوثيق يشرح جميع الـ endpoints المتعلقة بخدمات المزودين (Vendor Services) في النظام.

---

## Base URL
```
{{url}}/api/vendor-services
```

---

## Public Endpoints (بدون Authentication)

### 1. Get All Approved Vendor Services
جلب جميع الخدمات المعتمدة (Approved) في النظام.

**Endpoint:**
```
GET /api/vendor-services
```

**Headers:**
- `Accept: application/json` (مطلوب)
- `Accept-Language: ar|en` (اختياري - القيمة الافتراضية: ar)
- `Accept-Country: SAU|EGY|UAE|...` (مطلوب - كود البلد)

**Query Parameters:**
- `service_type_id` (اختياري) - فلترة حسب نوع الخدمة

**Example Request:**
```bash
curl -X GET "http://your-domain.com/api/vendor-services?service_type_id=1" \
  -H "Accept: application/json" \
  -H "Accept-Language: ar" \
  -H "Accept-Country: SAU"
```

**Response (200 OK):**
```json
{
  "status": 200,
  "message": "تم جلب خدمات المزودين بنجاح",
  "meta": null,
  "data": [
    {
      "id": 1,
      "service_type": "نظافة",
      "name": "My Cleaning Service",
      "description": "We provide deep cleaning services.",
      "contact_number": "+20127386271",
      "address": "Cairo, Egypt",
      "latitude": "30.033",
      "longitude": "31.233",
      "images": [
        "http://127.0.0.1:8000/storage/vendor_services/images/image1.jpg",
        "http://127.0.0.1:8000/storage/vendor_services/images/image2.jpg"
      ],
      "status": "تمت الموافقة"
    }
  ]
}
```

**Error Responses:**
- `400 Bad Request` - إذا كان `Accept-Country` header مفقود أو غير صحيح
- `200 OK` مع `data: []` - إذا لم توجد خدمات معتمدة

---

### 2. Get One Approved Vendor Service
جلب خدمة واحدة معتمدة (Approved).

**Endpoint:**
```
GET /api/vendor-services/{id}
```

**Headers:**
- `Accept: application/json` (مطلوب)
- `Accept-Language: ar|en` (اختياري)
- `Accept-Country: SAU|EGY|UAE|...` (اختياري - للفلترة حسب البلد)

**URL Parameters:**
- `id` (مطلوب) - ID الخدمة

**Example Request:**
```bash
curl -X GET "http://your-domain.com/api/vendor-services/1" \
  -H "Accept: application/json" \
  -H "Accept-Language: ar" \
  -H "Accept-Country: SAU"
```

**Response (200 OK):**
```json
{
  "status": 200,
  "message": "تم جلب تفاصيل خدمة المزود بنجاح",
  "meta": null,
  "data": {
    "id": 1,
    "service_type": "نظافة",
    "name": "My Cleaning Service",
    "description": "We provide deep cleaning services.",
    "contact_number": "+20127386271",
    "address": "Cairo, Egypt",
    "latitude": "30.033",
    "longitude": "31.233",
    "images": [
      "http://127.0.0.1:8000/storage/vendor_services/images/image1.jpg"
    ],
    "status": "تمت الموافقة"
  }
}
```

**Error Responses:**
- `404 Not Found` - إذا لم توجد الخدمة أو لم تكن معتمدة

---


## Authenticated Endpoints (يحتاج Authentication)

### 4. Get My Vendor Services
جلب جميع خدمات المستخدم الحالي.

**Endpoint:**
```
GET /api/vendor-services/my-services
```

**Authentication:**
- `Authorization: Bearer {token}` (مطلوب)

**Headers:**
- `Accept: application/json` (مطلوب)
- `Accept-Language: ar|en` (اختياري)

**Example Request:**
```bash
curl -X GET "http://your-domain.com/api/vendor-services/my-services" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json" \
  -H "Accept-Language: ar"
```

**Response (200 OK):**
```json
{
  "status": 200,
  "message": "تم جلب خدمات المزودين بنجاح",
  "meta": null,
  "data": [
    {
      "id": 1,
      "name": "My Cleaning Service",
      "status": "تمت الموافقة"
    }
  ]
}
```

---

### 5. Get One My Vendor Service
جلب خدمة واحدة للمستخدم الحالي.

**Endpoint:**
```
GET /api/vendor-services/my-services/{id}
```

**Authentication:**
- `Authorization: Bearer {token}` (مطلوب)

**Headers:**
- `Accept: application/json` (مطلوب)
- `Accept-Language: ar|en` (اختياري)

**URL Parameters:**
- `id` (مطلوب) - ID الخدمة

**Example Request:**
```bash
curl -X GET "http://your-domain.com/api/vendor-services/my-services/1" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json" \
  -H "Accept-Language: ar"
```

**Response (200 OK):**
```json
{
  "status": 200,
  "message": "تم جلب تفاصيل خدمة المزود بنجاح",
  "meta": null,
  "data": {
    "id": 1,
    "service_type": "نظافة",
    "name": "My Cleaning Service",
    "description": "We provide deep cleaning services.",
    "contact_number": "+20127386271",
    "address": "Cairo, Egypt",
    "latitude": "30.033",
    "longitude": "31.233",
    "country": {
      "id": 1,
      "name_en": "Saudi Arabia",
      "name_ar": "المملكة العربية السعودية",
      "code": "SAU"
    },
    "commercial_register": "http://...",
    "images": [...],
    "status": "تمت الموافقة",
    "created_at": "2025-12-11 14:30:00",
    "updated_at": "2025-12-11 14:30:00"
  }
}
```

**Error Responses:**
- `404 Not Found` - إذا لم توجد الخدمة أو لم تكن ملك المستخدم

---

### 6. Create Vendor Service
إنشاء خدمة مزود جديدة.

**Endpoint:**
```
POST /api/vendor-services
```

**Authentication:**
- `Authorization: Bearer {token}` (مطلوب)

**Headers:**
- `Accept: application/json` (مطلوب)
- `Content-Type: multipart/form-data` (مطلوب)
- `Accept-Language: ar|en` (اختياري)

**Body (form-data):**
- `service_type_id` (مطلوب) - ID نوع الخدمة
- `name` (مطلوب) - اسم الخدمة
- `description` (اختياري) - وصف الخدمة
- `contact_number` (اختياري) - رقم الاتصال
- `address` (اختياري) - العنوان
- `latitude` (اختياري) - خط العرض
- `longitude` (اختياري) - خط الطول
- `country_id` (اختياري) - ID البلد
- `commercial_register` (اختياري) - ملف السجل التجاري (file)
- `images[]` (اختياري) - صور الخدمة (files array)

**Example Request:**
```bash
curl -X POST "http://your-domain.com/api/vendor-services" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json" \
  -H "Accept-Language: ar" \
  -F "service_type_id=1" \
  -F "name=خدمة النقل السريع" \
  -F "description=نقدم خدمات نقل سريعة وآمنة" \
  -F "contact_number=+966501234567" \
  -F "address=الرياض، حي العليا" \
  -F "latitude=24.7136" \
  -F "longitude=46.6753" \
  -F "country_id=1" \
  -F "commercial_register=@/path/to/file.pdf" \
  -F "images[]=@/path/to/image1.jpg" \
  -F "images[]=@/path/to/image2.jpg"
```

**Response (201 Created):**
```json
{
  "status": 201,
  "message": "تم إنشاء خدمة المزود بنجاح",
  "meta": null,
  "data": {
    "id": 1,
    "service_type": "نقل",
    "name": "خدمة النقل السريع",
    "description": "نقدم خدمات نقل سريعة وآمنة",
    "contact_number": "+966501234567",
    "address": "الرياض، حي العليا",
    "latitude": "24.7136",
    "longitude": "46.6753",
    "country": {
      "id": 1,
      "name_en": "Saudi Arabia",
      "name_ar": "المملكة العربية السعودية",
      "code": "SAU"
    },
    "commercial_register": "http://...",
    "images": [...],
    "status": "قيد المراجعة",
    "created_at": "2025-12-11 14:30:00",
    "updated_at": "2025-12-11 14:30:00"
  }
}
```

**Error Responses:**
- `403 Forbidden` - إذا تجاوز المستخدم الحد الأقصى لعدد الخدمات
- `422 Unprocessable Entity` - أخطاء في التحقق من البيانات

---

### 7. Update Vendor Service
تحديث خدمة مزود موجودة.

**Endpoint:**
```
PUT /api/vendor-services/{id}
```

**Authentication:**
- `Authorization: Bearer {token}` (مطلوب)

**Headers:**
- `Accept: application/json` (مطلوب)
- `Content-Type: multipart/form-data` (مطلوب)
- `Accept-Language: ar|en` (اختياري)

**URL Parameters:**
- `id` (مطلوب) - ID الخدمة

**Body (form-data):**
- `service_type_id` (اختياري) - ID نوع الخدمة
- `name` (اختياري) - اسم الخدمة
- `description` (اختياري) - وصف الخدمة
- `contact_number` (اختياري) - رقم الاتصال
- `address` (اختياري) - العنوان
- `latitude` (اختياري) - خط العرض
- `longitude` (اختياري) - خط الطول
- `country_id` (اختياري) - ID البلد
- `commercial_register` (اختياري) - ملف السجل التجاري (file)
- `images[]` (اختياري) - صور الخدمة (files array)

**Example Request:**
```bash
curl -X PUT "http://your-domain.com/api/vendor-services/1" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json" \
  -H "Accept-Language: ar" \
  -F "name=خدمة النقل المحدثة" \
  -F "description=وصف محدث"
```

**Response (200 OK):**
```json
{
  "status": 200,
  "message": "تم تحديث خدمة المزود بنجاح",
  "meta": null,
  "data": {
    "id": 1,
    "service_type": "نقل",
    "name": "خدمة النقل المحدثة",
    ...
  }
}
```

**Error Responses:**
- `404 Not Found` - إذا لم توجد الخدمة أو لم تكن ملك المستخدم
- `422 Unprocessable Entity` - أخطاء في التحقق من البيانات

**ملاحظة:** إذا كانت الخدمة معتمدة (`approved`) وتم تحديثها، سيتم تغيير الحالة إلى `pending` للمراجعة مرة أخرى.

---

### 8. Disable Vendor Service
تعطيل خدمة مزود (Soft Delete).

**Endpoint:**
```
DELETE /api/vendor-services/{id}
```

**Authentication:**
- `Authorization: Bearer {token}` (مطلوب)

**Headers:**
- `Accept: application/json` (مطلوب)
- `Accept-Language: ar|en` (اختياري)

**URL Parameters:**
- `id` (مطلوب) - ID الخدمة

**Example Request:**
```bash
curl -X DELETE "http://your-domain.com/api/vendor-services/1" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json" \
  -H "Accept-Language: ar"
```

**Response (200 OK):**
```json
{
  "status": 200,
  "message": "تم تعطيل خدمة المزود بنجاح",
  "meta": null,
  "data": null
}
```

**Error Responses:**
- `404 Not Found` - إذا لم توجد الخدمة أو لم تكن ملك المستخدم
- `403 Forbidden` - إذا كانت الخدمة معطلة بالفعل

---

### 9. Enable Vendor Service
تفعيل خدمة مزود معطلة.

**Endpoint:**
```
POST /api/vendor-services/{id}/enable
```

**Authentication:**
- `Authorization: Bearer {token}` (مطلوب)

**Headers:**
- `Accept: application/json` (مطلوب)
- `Accept-Language: ar|en` (اختياري)

**URL Parameters:**
- `id` (مطلوب) - ID الخدمة

**Example Request:**
```bash
curl -X POST "http://your-domain.com/api/vendor-services/1/enable" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json" \
  -H "Accept-Language: ar"
```

**Response (200 OK):**
```json
{
  "status": 200,
  "message": "تم تفعيل خدمة المزود بنجاح",
  "meta": null,
  "data": {
    "id": 1,
    "name": "My Cleaning Service",
    "status": "قيد المراجعة",
    ...
  }
}
```

**Error Responses:**
- `404 Not Found` - إذا لم توجد الخدمة أو لم تكن ملك المستخدم
- `403 Forbidden` - إذا كانت الخدمة غير معطلة

---

### 10. Force Delete Vendor Service
حذف خدمة مزود نهائياً (Hard Delete).

**Endpoint:**
```
DELETE /api/vendor-services/{id}/forceDelete
```

**Authentication:**
- `Authorization: Bearer {token}` (مطلوب)

**Headers:**
- `Accept: application/json` (مطلوب)
- `Accept-Language: ar|en` (اختياري)

**URL Parameters:**
- `id` (مطلوب) - ID الخدمة

**Example Request:**
```bash
curl -X DELETE "http://your-domain.com/api/vendor-services/1/forceDelete" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json" \
  -H "Accept-Language: ar"
```

**Response (200 OK):**
```json
{
  "status": 200,
  "message": "تم حذف خدمة المزود نهائياً",
  "meta": null,
  "data": null
}
```

**Error Responses:**
- `404 Not Found` - إذا لم توجد الخدمة أو لم تكن ملك المستخدم

**ملاحظة:** هذا الـ endpoint يحذف الخدمة نهائياً من قاعدة البيانات مع جميع الصور والملفات المرتبطة بها.

---

## Service Types Endpoint

### 11. Get Service Types
جلب جميع أنواع الخدمات المتاحة.

**Endpoint:**
```
GET /api/service-types
```

**Headers:**
- `Accept: application/json` (مطلوب)
- `Accept-Language: ar|en` (اختياري)

**Example Request:**
```bash
curl -X GET "http://your-domain.com/api/service-types" \
  -H "Accept: application/json" \
  -H "Accept-Language: ar"
```

**Response (200 OK):**
```json
{
  "status": 200,
  "message": "تم جلب أنواع الخدمات بنجاح",
  "meta": null,
  "data": [
    {
      "id": 1,
      "name": "نظافة",
      "is_active": true
    },
    {
      "id": 2,
      "name": "نقل",
      "is_active": true
    }
  ]
}
```

---

## Status Values

الحالات المتاحة للخدمات:

- `pending` - قيد المراجعة
- `approved` - تمت الموافقة
- `rejected` - مرفوضة
- `disabled` - معطلة

---

## Country Codes

أمثلة على أكواد البلدان المدعومة:

- `SAU` - المملكة العربية السعودية
- `EGY` - مصر
- `UAE` - الإمارات العربية المتحدة
- `JOR` - الأردن
- `KWT` - الكويت
- `QAT` - قطر
- `BHR` - البحرين
- `OMN` - عمان

---

## Error Codes

| Code | Description |
|------|-------------|
| 200 | Success |
| 201 | Created |
| 400 | Bad Request |
| 401 | Unauthorized |
| 403 | Forbidden |
| 404 | Not Found |
| 422 | Validation Error |
| 500 | Server Error |

---

## Notes

1. جميع الـ endpoints العامة (`GET /api/vendor-services` و `GET /api/vendor-services/{id}`) تتطلب `Accept-Country` header.
2. إذا كان `Accept-Country` header مفقود أو غير صحيح، سيتم إرجاع array فارغ.
3. جميع الخدمات المعروضة في الـ endpoints العامة تكون بحالة `approved` فقط.
4. عند تحديث خدمة معتمدة، يتم تغيير حالتها إلى `pending` للمراجعة مرة أخرى.
5. الحد الأقصى لعدد الخدمات لكل مستخدم يتم تحديده من خلال إعدادات النظام.

---

## Postman Collection

يمكنك استيراد الـ Postman Collection من الملف:
- `Vendor_Services_API.postman_collection.json` (إن وجد)

---

## Support

للمساعدة والدعم، يرجى التواصل مع فريق التطوير.
