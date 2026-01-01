# 📘 Smart Packing Assistant - Complete API Documentation

## 📌 Base Information

**Base URL:** `https://your-domain.com/api`

**Authentication:** Bearer Token (Laravel Sanctum)

**Content-Type:** `application/json`

**Accept-Language:** `ar` (Arabic) or `en` (English)

---

## 🔐 Authentication

جميع الـ endpoints تحتاج إلى Authentication Token:

```http
Authorization: Bearer {your_access_token}
```

**How to get token:**
```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "token": "1|xxxxxxxxxxxxxxxxxxxxx",
    "user": { ... }
  }
}
```

---

## 📦 API Endpoints Overview

| # | Method | Endpoint | Description |
|---|--------|----------|-------------|
| 1 | GET | `/smart-bags` | Get all user bags |
| 2 | POST | `/smart-bags` | Create new bag |
| 3 | GET | `/smart-bags/{bagId}` | Get bag details |
| 4 | PUT | `/smart-bags/{bagId}` | Update bag |
| 5 | DELETE | `/smart-bags/{bagId}` | Delete bag |
| 6 | POST | `/smart-bags/{bagId}/items` | Add item to bag |
| 7 | PUT | `/smart-bags/{bagId}/items/{itemId}` | Update item |
| 8 | DELETE | `/smart-bags/{bagId}/items/{itemId}` | Delete item |
| 9 | POST | `/smart-bags/{bagId}/items/{itemId}/toggle-packed` | Toggle item packed status |
| 10 | POST | `/smart-bags/{bagId}/analyze` | Analyze bag with AI |
| 11 | GET | `/smart-bags/{bagId}/analysis/latest` | Get latest analysis |
| 12 | GET | `/smart-bags/{bagId}/analysis/history` | Get analysis history |
| 13 | GET | `/smart-bags/{bagId}/smart-alert` | Get smart alert |

---

# 📋 Detailed API Documentation

---

## 1️⃣ Get All Bags

**Description:** احصل على جميع حقائب المستخدم مع إمكانية الفلترة والترتيب

### Request

```http
GET /api/smart-bags
Authorization: Bearer {token}
Accept-Language: ar
```

### Query Parameters

| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `status` | string | No | Filter by bag status | `in_progress` |
| `trip_type` | string | No | Filter by trip type | `عمل` |
| `upcoming` | boolean | No | Show only upcoming trips | `true` |
| `sort_by` | string | No | Sort field | `departure_date` |
| `sort_order` | string | No | Sort direction (asc/desc) | `desc` |
| `per_page` | integer | No | Items per page (default: 15) | `20` |
| `page` | integer | No | Page number | `1` |

**Status Values:**
- `draft` - مسودة
- `in_progress` - قيد التجهيز
- `completed` - مكتملة
- `cancelled` - ملغاة

**Trip Type Values:**
- `عمل` - Business
- `سياحة` - Tourism
- `عائلية` - Family
- `علاج` - Medical

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Bags retrieved successfully",
  "message_ar": "تم جلب الحقائب بنجاح",
  "data": [
    {
      "id": 1,
      "name": "حقيبة رحلة دبي",
      "trip_type": "عمل",
      "trip_type_en": "Business",
      "duration": 4,
      "destination": "دبي",
      "departure_date": "2024-12-25",
      "max_weight": 20.00,
      "total_weight": 18.50,
      "weight_percentage": 92.50,
      "remaining_weight": 1.50,
      "is_overweight": false,
      "days_until_departure": 5,
      "status": "in_progress",
      "status_en": "In Progress",
      "preferences": {
        "style": "minimalist",
        "priorities": ["weight", "essentials"]
      },
      "is_analyzed": true,
      "last_analyzed_at": "2024-12-20T10:30:00Z",
      "items_count": 12,
      "created_at": "2024-12-15T08:00:00Z",
      "updated_at": "2024-12-20T10:30:00Z"
    }
  ],
  "pagination": {
    "total": 50,
    "per_page": 15,
    "current_page": 1,
    "last_page": 4
  }
}
```

### Example Request with Filters

```bash
curl -X GET "https://your-domain.com/api/smart-bags?status=in_progress&trip_type=عمل&upcoming=true&sort_by=departure_date&sort_order=asc&per_page=20" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept-Language: ar"
```

---

## 2️⃣ Create New Bag

**Description:** إنشاء حقيبة سفر جديدة مع إمكانية إضافة الأغراض مباشرة

### Request

```http
POST /api/smart-bags
Authorization: Bearer {token}
Content-Type: application/json
Accept-Language: ar
```

### Request Body

```json
{
  "name": "حقيبة رحلة دبي",
  "trip_type": "عمل",
  "duration": 4,
  "destination": "دبي",
  "departure_date": "2024-12-25",
  "max_weight": 20,
  "status": "draft",
  "preferences": {
    "style": "minimalist",
    "priorities": ["weight", "essentials"]
  },
  "items": [
    {
      "name": "بدلة رسمية",
      "weight": 2.4,
      "category": "ملابس",
      "essential": true,
      "packed": false,
      "quantity": 2,
      "notes": "بدلة سوداء ورمادية"
    },
    {
      "name": "لابتوب",
      "weight": 2.3,
      "category": "إلكترونيات",
      "essential": true,
      "packed": false,
      "quantity": 1
    }
  ]
}
```

### Request Body Fields

| Field | Type | Required | Description | Example |
|-------|------|----------|-------------|---------|
| `name` | string | ✅ Yes | اسم الحقيبة | "حقيبة رحلة دبي" |
| `trip_type` | enum | ✅ Yes | نوع الرحلة | "عمل", "سياحة", "عائلية", "علاج" |
| `duration` | integer | ✅ Yes | مدة الرحلة (أيام) | 4 |
| `destination` | string | ✅ Yes | وجهة السفر | "دبي" |
| `departure_date` | date | ✅ Yes | تاريخ المغادرة (Y-m-d) | "2024-12-25" |
| `max_weight` | decimal | ✅ Yes | الحد الأقصى للوزن (كجم) | 20 |
| `status` | enum | No | حالة الحقيبة | "draft" (default) |
| `preferences` | object | No | تفضيلات المستخدم | {...} |
| `items` | array | No | الأغراض المبدئية | [...] |

**Item Fields:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `name` | string | ✅ Yes | اسم الغرض |
| `weight` | decimal | ✅ Yes | الوزن (كجم) |
| `category` | enum | ✅ Yes | الفئة (ملابس، أحذية، إلكترونيات، أدوية وعناية، مستندات، أخرى) |
| `essential` | boolean | No | هل الغرض ضروري؟ |
| `packed` | boolean | No | هل تم تحزيمه؟ |
| `quantity` | integer | No | الكمية (default: 1) |
| `notes` | string | No | ملاحظات |

### Success Response (201 Created)

```json
{
  "success": true,
  "message": "Bag created successfully",
  "message_ar": "تم إنشاء الحقيبة بنجاح",
  "data": {
    "id": 1,
    "name": "حقيبة رحلة دبي",
    "trip_type": "عمل",
    "trip_type_en": "Business",
    "duration": 4,
    "destination": "دبي",
    "departure_date": "2024-12-25",
    "max_weight": 20.00,
    "total_weight": 4.70,
    "weight_percentage": 23.50,
    "remaining_weight": 15.30,
    "is_overweight": false,
    "days_until_departure": 5,
    "status": "draft",
    "status_en": "Draft",
    "preferences": {
      "style": "minimalist",
      "priorities": ["weight", "essentials"]
    },
    "is_analyzed": false,
    "last_analyzed_at": null,
    "items": [
      {
        "id": 1,
        "name": "بدلة رسمية",
        "weight": 2.40,
        "total_weight": 4.80,
        "category": "ملابس",
        "category_en": "Clothing",
        "essential": true,
        "packed": false,
        "quantity": 2,
        "notes": "بدلة سوداء ورمادية",
        "created_at": "2024-12-20T10:30:00Z",
        "updated_at": "2024-12-20T10:30:00Z"
      },
      {
        "id": 2,
        "name": "لابتوب",
        "weight": 2.30,
        "total_weight": 2.30,
        "category": "إلكترونيات",
        "category_en": "Electronics",
        "essential": true,
        "packed": false,
        "quantity": 1,
        "notes": null,
        "created_at": "2024-12-20T10:30:00Z",
        "updated_at": "2024-12-20T10:30:00Z"
      }
    ],
    "latest_analysis": null,
    "created_at": "2024-12-20T10:30:00Z",
    "updated_at": "2024-12-20T10:30:00Z"
  }
}
```

### Validation Errors (422 Unprocessable Entity)

```json
{
  "status": 400,
  "message": "اسم الحقيبة مطلوب",
  "meta": null,
  "data": []
}
```

**Common Validation Errors:**
- "اسم الحقيبة مطلوب" - name is required
- "نوع الرحلة مطلوب" - trip_type is required
- "تاريخ المغادرة يجب أن يكون اليوم أو في المستقبل" - departure_date must be today or future
- "الحد الأقصى للوزن يجب أن يكون أكبر من صفر" - max_weight must be greater than zero

### Example Request

```bash
curl -X POST "https://your-domain.com/api/smart-bags" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept-Language: ar" \
  -d '{
    "name": "حقيبة رحلة دبي",
    "trip_type": "عمل",
    "duration": 4,
    "destination": "دبي",
    "departure_date": "2024-12-25",
    "max_weight": 20
  }'
```

---

## 3️⃣ Get Bag Details

**Description:** احصل على تفاصيل حقيبة معينة مع جميع الأغراض والتحليل الأخير

### Request

```http
GET /api/smart-bags/{bagId}
Authorization: Bearer {token}
Accept-Language: ar
```

### URL Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `bagId` | integer | معرف الحقيبة |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Bag retrieved successfully",
  "message_ar": "تم جلب الحقيبة بنجاح",
  "data": {
    "id": 1,
    "name": "حقيبة رحلة دبي",
    "trip_type": "عمل",
    "trip_type_en": "Business",
    "duration": 4,
    "destination": "دبي",
    "departure_date": "2024-12-25",
    "max_weight": 20.00,
    "total_weight": 18.50,
    "weight_percentage": 92.50,
    "remaining_weight": 1.50,
    "is_overweight": false,
    "days_until_departure": 5,
    "status": "in_progress",
    "status_en": "In Progress",
    "preferences": {
      "style": "minimalist",
      "priorities": ["weight", "essentials"]
    },
    "is_analyzed": true,
    "last_analyzed_at": "2024-12-20T10:30:00Z",
    "items_count": 12,
    "items": [
      {
        "id": 1,
        "name": "بدلة رسمية",
        "weight": 2.40,
        "total_weight": 4.80,
        "category": "ملابس",
        "category_en": "Clothing",
        "essential": true,
        "packed": true,
        "quantity": 2,
        "notes": null,
        "created_at": "2024-12-20T08:00:00Z",
        "updated_at": "2024-12-20T09:00:00Z"
      },
      {
        "id": 2,
        "name": "قمصان",
        "weight": 0.30,
        "total_weight": 1.50,
        "category": "ملابس",
        "category_en": "Clothing",
        "essential": true,
        "packed": true,
        "quantity": 5,
        "notes": null,
        "created_at": "2024-12-20T08:05:00Z",
        "updated_at": "2024-12-20T09:05:00Z"
      }
    ],
    "latest_analysis": {
      "id": 1,
      "analysis_id": "analysis_xyz123",
      "bag_id": 1,
      "missing_items": [
        {
          "id": "missing_1",
          "name": "شاحن موبايل إضافي",
          "weight": 0.2,
          "reason": "رحلة عمل 4 أيام تحتاج احتياطي للطوارئ",
          "priority": "high",
          "category": "إلكترونيات"
        }
      ],
      "missing_items_count": 1,
      "extra_items": [],
      "extra_items_count": 0,
      "weight_optimization": {
        "current_weight": 18.50,
        "suggested_weight": 17.80,
        "weight_saved": 0.70,
        "impact_level": "low",
        "percentage_saved": 3.78
      },
      "weight_saved": 0.70,
      "additional_suggestions": [
        {
          "id": "sugg_1",
          "category": "organization",
          "title": "تنظيم الحقيبة",
          "description": "ضع الملابس الرسمية في أكياس تفريغ الهواء",
          "priority": "medium"
        }
      ],
      "suggestions_count": 1,
      "smart_alert": {
        "alert_id": "alert_1",
        "time_remaining": "5 أيام",
        "time_remaining_minutes": 7200,
        "message": "تبقى 5 أيام على الرحلة",
        "action": "راجع الأغراض الضرورية",
        "severity": "medium",
        "icon": "clock"
      },
      "has_high_priority_alerts": true,
      "high_priority_missing_items": [
        {
          "id": "missing_1",
          "name": "شاحن موبايل إضافي",
          "weight": 0.2,
          "reason": "رحلة عمل 4 أيام تحتاج احتياطي للطوارئ",
          "priority": "high",
          "category": "إلكترونيات"
        }
      ],
      "confidence_score": 0.92,
      "processing_time_ms": 1250,
      "ai_model": "gemini-2.0-flash-exp",
      "metadata": {
        "analyzed_at": "2024-12-20T10:30:00Z",
        "ai_model": "gemini-2.0-flash-exp",
        "processing_time_ms": 1250
      },
      "created_at": "2024-12-20T10:30:00Z",
      "updated_at": "2024-12-20T10:30:00Z"
    },
    "created_at": "2024-12-15T08:00:00Z",
    "updated_at": "2024-12-20T10:30:00Z"
  }
}
```

### Error Response (404 Not Found)

```json
{
  "status": 404,
  "message": "bag not found"
}
```

### Example Request

```bash
curl -X GET "https://your-domain.com/api/smart-bags/1" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept-Language: ar"
```

---

## 4️⃣ Update Bag

**Description:** تحديث معلومات حقيبة موجودة

### Request

```http
PUT /api/smart-bags/{bagId}
Authorization: Bearer {token}
Content-Type: application/json
Accept-Language: ar
```

### Request Body

جميع الحقول اختيارية (optional) - أرسل فقط ما تريد تحديثه:

```json
{
  "name": "حقيبة رحلة دبي المحدثة",
  "duration": 5,
  "destination": "دبي والشارقة",
  "status": "in_progress",
  "max_weight": 23
}
```

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Bag updated successfully",
  "message_ar": "تم تحديث الحقيبة بنجاح",
  "data": {
    "id": 1,
    "name": "حقيبة رحلة دبي المحدثة",
    "duration": 5,
    "destination": "دبي والشارقة",
    "status": "in_progress",
    "max_weight": 23.00,
    ...
  }
}
```

### Example Request

```bash
curl -X PUT "https://your-domain.com/api/smart-bags/1" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept-Language: ar" \
  -d '{
    "status": "in_progress",
    "max_weight": 23
  }'
```

---

## 5️⃣ Delete Bag

**Description:** حذف حقيبة (soft delete - يمكن استرجاعها)

### Request

```http
DELETE /api/smart-bags/{bagId}
Authorization: Bearer {token}
Accept-Language: ar
```

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Bag deleted successfully",
  "message_ar": "تم حذف الحقيبة بنجاح"
}
```

### Example Request

```bash
curl -X DELETE "https://your-domain.com/api/smart-bags/1" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept-Language: ar"
```

---

## 6️⃣ Add Item to Bag

**Description:** إضافة غرض جديد إلى الحقيبة

### Request

```http
POST /api/smart-bags/{bagId}/items
Authorization: Bearer {token}
Content-Type: application/json
Accept-Language: ar
```

### Request Body

```json
{
  "name": "شاحن لابتوب",
  "weight": 0.5,
  "category": "إلكترونيات",
  "essential": true,
  "packed": false,
  "quantity": 1,
  "notes": "تذكر الشاحن الاحتياطي"
}
```

### Request Body Fields

| Field | Type | Required | Description | Values |
|-------|------|----------|-------------|--------|
| `name` | string | ✅ Yes | اسم الغرض | - |
| `weight` | decimal | ✅ Yes | الوزن (كجم) | 0-999.99 |
| `category` | enum | ✅ Yes | الفئة | ملابس، أحذية، إلكترونيات، أدوية وعناية، مستندات، أخرى |
| `essential` | boolean | No | هل الغرض ضروري؟ | true/false |
| `packed` | boolean | No | هل تم تحزيمه؟ | true/false |
| `quantity` | integer | No | الكمية | 1-999 |
| `notes` | string | No | ملاحظات | max 1000 chars |

### Success Response (201 Created)

```json
{
  "success": true,
  "message": "Item added successfully",
  "message_ar": "تم إضافة الغرض بنجاح",
  "data": {
    "id": 13,
    "name": "شاحن لابتوب",
    "weight": 0.50,
    "total_weight": 0.50,
    "category": "إلكترونيات",
    "category_en": "Electronics",
    "essential": true,
    "packed": false,
    "quantity": 1,
    "notes": "تذكر الشاحن الاحتياطي",
    "created_at": "2024-12-20T11:00:00Z",
    "updated_at": "2024-12-20T11:00:00Z"
  }
}
```

**Note:** عند إضافة غرض، يتم تحديث `total_weight` للحقيبة تلقائياً

### Example Request

```bash
curl -X POST "https://your-domain.com/api/smart-bags/1/items" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept-Language: ar" \
  -d '{
    "name": "شاحن لابتوب",
    "weight": 0.5,
    "category": "إلكترونيات",
    "essential": true
  }'
```

---

## 7️⃣ Update Item

**Description:** تحديث غرض موجود في الحقيبة

### Request

```http
PUT /api/smart-bags/{bagId}/items/{itemId}
Authorization: Bearer {token}
Content-Type: application/json
Accept-Language: ar
```

### Request Body

جميع الحقول اختيارية:

```json
{
  "name": "شاحن لابتوب USB-C",
  "weight": 0.45,
  "packed": true,
  "notes": "شاحن أصلي 65W"
}
```

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Item updated successfully",
  "message_ar": "تم تحديث الغرض بنجاح",
  "data": {
    "id": 13,
    "name": "شاحن لابتوب USB-C",
    "weight": 0.45,
    "total_weight": 0.45,
    "category": "إلكترونيات",
    "category_en": "Electronics",
    "essential": true,
    "packed": true,
    "quantity": 1,
    "notes": "شاحن أصلي 65W",
    "created_at": "2024-12-20T11:00:00Z",
    "updated_at": "2024-12-20T11:15:00Z"
  }
}
```

### Example Request

```bash
curl -X PUT "https://your-domain.com/api/smart-bags/1/items/13" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept-Language: ar" \
  -d '{
    "packed": true,
    "notes": "شاحن أصلي 65W"
  }'
```

---

## 8️⃣ Delete Item

**Description:** حذف غرض من الحقيبة

### Request

```http
DELETE /api/smart-bags/{bagId}/items/{itemId}
Authorization: Bearer {token}
Accept-Language: ar
```

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Item deleted successfully",
  "message_ar": "تم حذف الغرض بنجاح"
}
```

**Note:** عند حذف غرض، يتم تحديث `total_weight` للحقيبة تلقائياً

### Example Request

```bash
curl -X DELETE "https://your-domain.com/api/smart-bags/1/items/13" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept-Language: ar"
```

---

## 9️⃣ Toggle Item Packed Status

**Description:** تبديل حالة التحزيم لغرض (من محزوم إلى غير محزوم والعكس)

### Request

```http
POST /api/smart-bags/{bagId}/items/{itemId}/toggle-packed
Authorization: Bearer {token}
Accept-Language: ar
```

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Item packed status updated",
  "message_ar": "تم تحديث حالة التحزيم",
  "data": {
    "id": 1,
    "name": "بدلة رسمية",
    "weight": 2.40,
    "total_weight": 4.80,
    "category": "ملابس",
    "category_en": "Clothing",
    "essential": true,
    "packed": true,
    "quantity": 2,
    "notes": null,
    "created_at": "2024-12-20T08:00:00Z",
    "updated_at": "2024-12-20T11:30:00Z"
  }
}
```

**Usage:** استخدم هذا الـ endpoint عند تحزيم الغرض بدلاً من استخدام Update Item

### Example Request

```bash
curl -X POST "https://your-domain.com/api/smart-bags/1/items/1/toggle-packed" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept-Language: ar"
```

---

## 🔟 Analyze Bag with AI ⭐

**Description:** تحليل محتويات الحقيبة باستخدام الذكاء الاصطناعي (Gemini AI)

### Request

```http
POST /api/smart-bags/{bagId}/analyze
Authorization: Bearer {token}
Content-Type: application/json
Accept-Language: ar
```

### Request Body

جميع الحقول اختيارية:

```json
{
  "preferences": {
    "style": "minimalist",
    "priorities": ["weight", "essentials"]
  },
  "force_reanalysis": false
}
```

### Request Body Fields

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `preferences` | object | No | تفضيلات التحليل |
| `preferences.style` | string | No | نمط السفر (minimalist, standard, luxury) |
| `preferences.priorities` | array | No | الأولويات (weight, essentials, comfort) |
| `force_reanalysis` | boolean | No | إعادة التحليل حتى لو تم التحليل مؤخراً |

### Success Response (201 Created)

```json
{
  "success": true,
  "message": "Bag analyzed successfully",
  "message_ar": "تم تحليل الحقيبة بنجاح",
  "data": {
    "id": 1,
    "analysis_id": "analysis_abc123_1703073000",
    "bag_id": 1,
    "missing_items": [
      {
        "id": "missing_1",
        "name": "شاحن موبايل إضافي",
        "weight": 0.2,
        "reason": "رحلة عمل 4 أيام تحتاج احتياطي للطوارئ",
        "priority": "high",
        "category": "إلكترونيات"
      },
      {
        "id": "missing_2",
        "name": "مسكنات ألم",
        "weight": 0.1,
        "reason": "ضروري في حقيبة الأدوية للطوارئ",
        "priority": "medium",
        "category": "أدوية وعناية"
      },
      {
        "id": "missing_3",
        "name": "بطاقات عمل (10 قطع)",
        "weight": 0.05,
        "reason": "رحلة عمل تحتاج بطاقات للتواصل",
        "priority": "medium",
        "category": "أخرى"
      }
    ],
    "missing_items_count": 3,
    "extra_items": [
      {
        "id": "extra_1",
        "item_id_in_bag": "item_4",
        "name": "حذاء رياضي",
        "reason": "رحلة عمل رسمية لا تحتاج حذاء رياضي، الحذاء الرسمي كافي",
        "weight_saved": 1.1
      }
    ],
    "extra_items_count": 1,
    "weight_optimization": {
      "current_weight": 18.5,
      "suggested_weight": 14.6,
      "weight_saved": 3.9,
      "impact_level": "high",
      "percentage_saved": 21.1,
      "suggestions": [
        {
          "type": "reduction",
          "description": "حذف الأغراض الزائدة",
          "weight_impact": 3.9
        },
        {
          "type": "redistribution",
          "description": "نقل الإلكترونيات للحقيبة اليدوية",
          "benefit": "حماية أفضل + سهولة وصول في المطار"
        },
        {
          "type": "alternative",
          "description": "استخدام ملابس قابلة للطي بدلاً من الثقيلة",
          "weight_impact": 0.5
        }
      ]
    },
    "weight_saved": 3.9,
    "additional_suggestions": [
      {
        "id": "sugg_1",
        "category": "organization",
        "title": "تنظيم الحقيبة",
        "description": "ضع الملابس الرسمية في أكياس تفريغ الهواء لتوفير مساحة",
        "priority": "medium"
      },
      {
        "id": "sugg_2",
        "category": "security",
        "title": "الأمان",
        "description": "احتفظ بنسخة من جواز السفر والوثائق في حقيبة يدوية منفصلة",
        "priority": "high"
      },
      {
        "id": "sugg_3",
        "category": "convenience",
        "title": "راحة السفر",
        "description": "ضع أغراض اليوم الأول في الأعلى لسهولة الوصول",
        "priority": "low"
      }
    ],
    "suggestions_count": 3,
    "smart_alert": {
      "alert_id": "alert_123",
      "time_remaining": "5 أيام",
      "time_remaining_minutes": 7200,
      "message": "تبقى 5 أيام على الرحلة وحقيبة الأدوية غير مكتملة",
      "action": "راجع الأدوية الأساسية والوثائق المطلوبة",
      "severity": "high",
      "icon": "clock"
    },
    "has_high_priority_alerts": true,
    "high_priority_missing_items": [
      {
        "id": "missing_1",
        "name": "شاحن موبايل إضافي",
        "weight": 0.2,
        "reason": "رحلة عمل 4 أيام تحتاج احتياطي للطوارئ",
        "priority": "high",
        "category": "إلكترونيات"
      }
    ],
    "confidence_score": 0.92,
    "processing_time_ms": 1250,
    "ai_model": "gemini-2.0-flash-exp",
    "metadata": {
      "analyzed_at": "2024-12-20T12:00:00Z",
      "ai_model": "gemini-2.0-flash-exp",
      "processing_time_ms": 1250,
      "finish_reason": "STOP",
      "confidence_score": 0.92
    },
    "created_at": "2024-12-20T12:00:00Z",
    "updated_at": "2024-12-20T12:00:00Z"
  }
}
```

### Error Responses

**Empty Bag (422 Unprocessable Entity):**
```json
{
  "success": false,
  "message": "Cannot analyze empty bag. Please add items first.",
  "message_ar": "لا يمكن تحليل حقيبة فارغة. الرجاء إضافة أغراض أولاً."
}
```

**Recently Analyzed (422 Unprocessable Entity):**
```json
{
  "success": false,
  "message": "Bag was analyzed recently. Use force_reanalysis=true to reanalyze.",
  "message_ar": "تم تحليل الحقيبة مؤخراً. استخدم force_reanalysis=true لإعادة التحليل.",
  "last_analyzed_at": "2024-12-20T10:30:00Z"
}
```

### Example Request

```bash
curl -X POST "https://your-domain.com/api/smart-bags/1/analyze" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept-Language: ar" \
  -d '{
    "preferences": {
      "style": "minimalist",
      "priorities": ["weight", "essentials"]
    }
  }'
```

---

## 1️⃣1️⃣ Get Latest Analysis

**Description:** احصل على آخر تحليل للحقيبة

### Request

```http
GET /api/smart-bags/{bagId}/analysis/latest
Authorization: Bearer {token}
Accept-Language: ar
```

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Latest analysis retrieved successfully",
  "message_ar": "تم جلب آخر تحليل بنجاح",
  "data": {
    "id": 1,
    "analysis_id": "analysis_abc123",
    "bag_id": 1,
    "missing_items": [...],
    "extra_items": [...],
    "weight_optimization": {...},
    "additional_suggestions": [...],
    "smart_alert": {...},
    "confidence_score": 0.92,
    "processing_time_ms": 1250,
    "ai_model": "gemini-2.0-flash-exp",
    "created_at": "2024-12-20T12:00:00Z",
    "updated_at": "2024-12-20T12:00:00Z"
  }
}
```

### Error Response (404 Not Found)

```json
{
  "success": false,
  "message": "No analysis found for this bag",
  "message_ar": "لا يوجد تحليل لهذه الحقيبة"
}
```

### Example Request

```bash
curl -X GET "https://your-domain.com/api/smart-bags/1/analysis/latest" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept-Language: ar"
```

---

## 1️⃣2️⃣ Get Analysis History

**Description:** احصل على سجل جميع التحليلات للحقيبة

### Request

```http
GET /api/smart-bags/{bagId}/analysis/history
Authorization: Bearer {token}
Accept-Language: ar
```

### Query Parameters

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `page` | integer | 1 | رقم الصفحة |
| `per_page` | integer | 10 | عدد العناصر لكل صفحة |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Analysis history retrieved successfully",
  "message_ar": "تم جلب سجل التحليلات بنجاح",
  "data": [
    {
      "id": 3,
      "analysis_id": "analysis_xyz789",
      "bag_id": 1,
      "missing_items_count": 2,
      "extra_items_count": 1,
      "weight_saved": 2.5,
      "confidence_score": 0.95,
      "created_at": "2024-12-20T15:00:00Z"
    },
    {
      "id": 2,
      "analysis_id": "analysis_def456",
      "bag_id": 1,
      "missing_items_count": 3,
      "extra_items_count": 0,
      "weight_saved": 1.2,
      "confidence_score": 0.88,
      "created_at": "2024-12-19T10:00:00Z"
    },
    {
      "id": 1,
      "analysis_id": "analysis_abc123",
      "bag_id": 1,
      "missing_items_count": 5,
      "extra_items_count": 2,
      "weight_saved": 3.9,
      "confidence_score": 0.92,
      "created_at": "2024-12-18T08:00:00Z"
    }
  ],
  "pagination": {
    "total": 3,
    "per_page": 10,
    "current_page": 1,
    "last_page": 1
  }
}
```

### Example Request

```bash
curl -X GET "https://your-domain.com/api/smart-bags/1/analysis/history?page=1&per_page=10" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept-Language: ar"
```

---

## 1️⃣3️⃣ Get Smart Alert

**Description:** احصل على التنبيه الذكي الحالي للحقيبة

### Request

```http
GET /api/smart-bags/{bagId}/smart-alert
Authorization: Bearer {token}
Accept-Language: ar
```

### Success Response - With Alerts (200 OK)

```json
{
  "success": true,
  "message": "Smart alert retrieved successfully",
  "message_ar": "تم جلب التنبيه الذكي بنجاح",
  "data": {
    "alert_id": "alert_1703073123_1",
    "bag_id": 1,
    "hours_remaining": 120,
    "time_remaining": "5 أيام",
    "issues": [
      {
        "category": "medicines",
        "message": "حقيبة الأدوية غير مكتملة",
        "message_en": "Medicine bag is incomplete",
        "action": "راجع الأدوية الأساسية",
        "action_en": "Review essential medicines",
        "severity": "high"
      },
      {
        "category": "documents",
        "message": "لا توجد وثائق عمل في الحقيبة",
        "message_en": "No work documents in bag",
        "action": "راجع المستندات المطلوبة للاجتماعات",
        "action_en": "Review required documents for meetings",
        "severity": "high"
      },
      {
        "category": "unpacked",
        "message": "يوجد 3 أغراض ضرورية غير محزومة",
        "message_en": "3 essential items are not packed",
        "action": "راجع الأغراض الضرورية وقم بتحزيمها",
        "action_en": "Review essential items and pack them",
        "severity": "high"
      }
    ],
    "message": "تبقى 5 أيام على الرحلة وحقيبة الأدوية غير مكتملة",
    "severity": "high",
    "created_at": "2024-12-20T12:00:00Z"
  }
}
```

### Success Response - No Alerts (200 OK)

```json
{
  "success": true,
  "message": "No alerts for this bag",
  "message_ar": "لا توجد تنبيهات لهذه الحقيبة",
  "data": null
}
```

### Alert Severity Levels

- **high** - تنبيه عاجل (أقل من 24 ساعة أو مشاكل خطيرة)
- **medium** - تنبيه متوسط (24-72 ساعة)
- **low** - تنبيه عادي (أكثر من 72 ساعة)

### Alert Categories

- **medicines** - نقص في الأدوية
- **documents** - نقص في المستندات (رحلات العمل)
- **weight** - تجاوز الوزن المسموح
- **unpacked** - أغراض ضرورية غير محزومة

### Example Request

```bash
curl -X GET "https://your-domain.com/api/smart-bags/1/smart-alert" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept-Language: ar"
```

---

# 🔒 Error Handling

## Standard Error Response

جميع الـ errors تتبع هذه البنية:

```json
{
  "success": false,
  "message": "Error message in English",
  "message_ar": "رسالة الخطأ بالعربي",
  "error": "Detailed error information (in production, this might be hidden)"
}
```

## HTTP Status Codes

| Code | Description | When |
|------|-------------|------|
| 200 | OK | نجح الطلب |
| 201 | Created | تم إنشاء مورد جديد |
| 400 | Bad Request | بيانات غير صالحة |
| 401 | Unauthorized | غير مصرح (token غير صحيح) |
| 403 | Forbidden | ممنوع (ليس لديك صلاحية) |
| 404 | Not Found | المورد غير موجود |
| 422 | Unprocessable Entity | خطأ في التحقق من البيانات |
| 500 | Internal Server Error | خطأ في الخادم |

## Common Errors

### 401 Unauthorized

```json
{
  "status": 401,
  "message": "Unauthenticated"
}
```

**Solution:** أضف Bearer Token صحيح في Header

### 404 Not Found

```json
{
  "status": 404,
  "message": "bag not found"
}
```

**Solution:** تأكد من صحة الـ ID أو أن المورد موجود

### 422 Validation Error

```json
{
  "status": 400,
  "message": "اسم الحقيبة مطلوب",
  "meta": null,
  "data": []
}
```

**Solution:** راجع بيانات الطلب وتأكد من إرسال جميع الحقول المطلوبة

### 500 Server Error

```json
{
  "success": false,
  "message": "Failed to analyze bag",
  "message_ar": "فشل في تحليل الحقيبة",
  "error": "Gemini API request failed: ..."
}
```

**Solution:** تحقق من:
- Gemini API Key صحيح
- الاتصال بالإنترنت
- حد استخدام Gemini API

---

# 📊 Rate Limiting

**Default Limits:**
- 60 requests per minute per IP
- 1000 requests per hour per user

**Headers:**
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 57
X-RateLimit-Reset: 1703073123
```

**429 Too Many Requests:**
```json
{
  "status": 429,
  "message": "Too many requests"
}
```

---

# 🔄 Pagination

## Request

```http
GET /api/smart-bags?page=2&per_page=20
```

## Response

```json
{
  "data": [...],
  "pagination": {
    "total": 50,
    "per_page": 20,
    "current_page": 2,
    "last_page": 3,
    "from": 21,
    "to": 40
  }
}
```

---

# 🌐 Multi-Language

## Setting Language

استخدم `Accept-Language` header:

```http
Accept-Language: ar    # للعربية
Accept-Language: en    # للإنجليزية
```

## Response Language

جميع الرسائل تأتي بلغتين:

```json
{
  "message": "Bag created successfully",
  "message_ar": "تم إنشاء الحقيبة بنجاح"
}
```

---

# 🧪 Testing Examples

## Postman Collection

### Environment Variables

```json
{
  "base_url": "https://your-domain.com/api",
  "token": "1|xxxxxxxxxxxxx",
  "bag_id": "1",
  "item_id": "1"
}
```

### Complete Flow Test

```javascript
// 1. Create Bag
POST {{base_url}}/smart-bags
Headers: Authorization: Bearer {{token}}
Body: {
  "name": "Test Bag",
  "trip_type": "سياحة",
  "duration": 3,
  "destination": "Cairo",
  "departure_date": "2024-12-30",
  "max_weight": 20
}

// 2. Add Item
POST {{base_url}}/smart-bags/{{bag_id}}/items
Body: {
  "name": "Shirt",
  "weight": 0.3,
  "category": "ملابس",
  "essential": true
}

// 3. Analyze
POST {{base_url}}/smart-bags/{{bag_id}}/analyze

// 4. Get Alert
GET {{base_url}}/smart-bags/{{bag_id}}/smart-alert

// 5. Toggle Packed
POST {{base_url}}/smart-bags/{{bag_id}}/items/{{item_id}}/toggle-packed
```

---

# 📝 Best Practices

## 1. Use Proper Headers

```http
Authorization: Bearer {token}
Content-Type: application/json
Accept-Language: ar
Accept: application/json
```

## 2. Handle Errors Gracefully

```javascript
try {
  const response = await fetch('/api/smart-bags', {
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json',
      'Accept-Language': 'ar'
    }
  });
  
  const data = await response.json();
  
  if (!response.ok) {
    // Handle error
    console.error(data.message_ar || data.message);
  }
  
  // Success
  console.log(data.data);
  
} catch (error) {
  console.error('Network error:', error);
}
```

## 3. Optimize API Calls

- استخدم filters بدلاً من جلب كل البيانات
- استخدم pagination
- احفظ الـ analysis results في cache
- لا تعمل re-analysis كل دقيقة

## 4. Weight Calculation

الـ `total_weight` للحقيبة يتم حسابه تلقائياً:
- عند إضافة item
- عند تحديث item weight
- عند حذف item

لا حاجة لإرساله يدوياً.

---

# 🚀 Performance Tips

1. **Eager Loading**
   - الـ API يستخدم `with(['items', 'latestAnalysis'])` تلقائياً
   - لا تحتاج لعمل requests منفصلة

2. **Caching**
   - احفظ analysis results
   - Cache لمدة 24 ساعة
   - Force reanalysis عند الحاجة فقط

3. **Batch Operations**
   - أرسل items مع create bag بدلاً من requests منفصلة
   - استخدم bulk operations إذا كانت متاحة

---

# 🔔 Webhooks (Future)

قريباً سيتم إضافة webhooks للأحداث التالية:

- `bag.created`
- `bag.analyzed`
- `bag.alert.high`
- `bag.overweight`

---

# 📞 Support

للدعم أو الاستفسارات:
- Email: support@your-domain.com
- API Version: 1.0
- Last Updated: 2024-12-20

---

**Happy Coding! 🎒✈️**

