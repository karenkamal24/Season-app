# Banner API Documentation - دليل API البانرات

## نظرة عامة / Overview

Banner API يسمح بجلب البانر النشط حسب اللغة فقط (تم إزالة البلد).

The Banner API allows fetching the active banner by language only (country has been removed).

---

## Endpoints

### 1. Get Active Banner - جلب البانر النشط

**Endpoint:** `GET /api/banners`

**Description:** 
يحصل على البانر النشط حسب اللغة المحددة في header.

Gets the active banner based on the language specified in the header.

**Headers:**
```
Accept-Language: ar|en  (optional, default: ar)
```

**Authentication:** 
لا يتطلب مصادقة / No authentication required

**Request Example:**
```bash
GET /api/banners
Headers:
  Accept-Language: ar
```

**cURL Example:**
```bash
curl -X GET "http://localhost:8000/api/banners" \
  -H "Accept-Language: ar"
```

**Response Structure:**
```json
{
  "status": 200,
  "message": "Banner retrieved successfully. | تم جلب البانر بنجاح.",
  "meta": null,
  "data": {
    "id": 1,
    "image": "http://localhost:8000/storage/banners/banner.jpg",
    "link": "https://example.com",
    "language": "ar",
    "is_active": true,
    "created_at": "2025-12-07 08:00:00",
    "updated_at": "2025-12-07 08:00:00"
  }
}
```

---

## Response Codes

| Status Code | Description | الوصف |
|------------|-------------|-------|
| 200 | Success - Banner found | نجاح - البانر موجود |
| 404 | Not Found - No active banner for the language | غير موجود - لا يوجد بانر نشط للغة المحددة |

---

## Response Examples

### Success Response (200)

**Request:**
```
GET /api/banners
Accept-Language: ar
```

**Response:**
```json
{
  "status": 200,
  "message": "تم جلب البانر بنجاح.",
  "meta": null,
  "data": {
    "id": 1,
    "image": "http://localhost:8000/storage/banners/arabic-banner.png",
    "link": "https://example.com/promo",
    "language": "ar",
    "is_active": true,
    "created_at": "2025-12-07 08:00:00",
    "updated_at": "2025-12-07 10:30:00"
  }
}
```

### Not Found Response (404)

**Request:**
```
GET /api/banners
Accept-Language: fr
```

**Response:**
```json
{
  "status": 404,
  "message": "البانر غير موجود.",
  "meta": null,
  "data": null
}
```

---

## Supported Languages

| Code | Language | اللغة |
|------|----------|-------|
| `ar` | Arabic | العربية |
| `en` | English | الإنجليزية |

**Note:** 
- اللغة الافتراضية هي `ar` إذا لم يتم تحديد header.
- Default language is `ar` if header is not specified.
- فقط اللغات `ar` و `en` مدعومة.
- Only `ar` and `en` languages are supported.

-
