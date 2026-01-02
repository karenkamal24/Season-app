# 📚 توثيق API - Smart Bags (الحقائب الذكية)

## نظرة عامة

توفر API الحقيبة الذكية نظامًا متكاملًا لإدارة الحقائب للسفر مع تحليل ذكي باستخدام الذكاء الاصطناعي. يتضمن النظام إدارة الحقائب، الأغراض، التحليل الذكي، والتنبيهات.

**Base URL:** `/api/smart-bags`  
**Authentication:** جميع endpoints تتطلب `Bearer Token` (Sanctum)

---

## جدول المحتويات

1. [Item Categories (فئات الأغراض)](#1-item-categories-فئات-الأغراض)
2. [Smart Bags Management (إدارة الحقائب الذكية)](#2-smart-bags-management-إدارة-الحقائب-الذكية)
3. [Bag Items Management (إدارة أغراض الحقيبة)](#3-bag-items-management-إدارة-أغراض-الحقيبة)
4. [AI Analysis (التحليل بالذكاء الاصطناعي)](#4-ai-analysis-التحليل-بالذكاء-الاصطناعي)
5. [Smart Alert (التنبيه الذكي)](#5-smart-alert-التنبيه-الذكي)
6. [Data Models (نماذج البيانات)](#6-data-models-نماذج-البيانات)
7. [Error Handling (معالجة الأخطاء)](#7-error-handling-معالجة-الأخطاء)

---

## 1. Item Categories (فئات الأغراض)

### 1.1 الحصول على جميع الفئات

**Endpoint:** `GET /api/item-categories`

**Description:** جلب جميع فئات الأغراض النشطة

**Authentication:** ❌ Public (غير مطلوب)

**Headers:**
```
Accept-Language: ar|en (اختياري، الافتراضي: ar)
```

**Query Parameters:** لا يوجد

**Response 200:**
```json
{
  "success": true,
  "message": "Item categories retrieved successfully",
  "message_ar": "تم جلب فئات الأغراض بنجاح",
  "data": [
    {
      "id": 1,
      "name": "إلكترونيات",
      "name_ar": "إلكترونيات",
      "name_en": "Electronics",
      "icon": "phone",
      "icon_color": "#3B82F6",
      "sort_order": 1
    },
    {
      "id": 2,
      "name": "ملابس",
      "name_ar": "ملابس",
      "name_en": "Clothing",
      "icon": "shirt",
      "icon_color": "#EF4444",
      "sort_order": 2
    }
  ]
}
```

**Fields Description:**
- `id`: معرف الفئة (عدد صحيح)
- `name`: اسم الفئة حسب اللغة المطلوبة
- `name_ar`: اسم الفئة بالعربية
- `name_en`: اسم الفئة بالإنجليزية
- `icon`: أيقونة الفئة (نص)
- `icon_color`: لون الأيقونة (hex color)
- `sort_order`: ترتيب الفئة (عدد صحيح)

---

### 1.2 الحصول على فئة واحدة

**Endpoint:** `GET /api/item-categories/{id}`

**Description:** جلب تفاصيل فئة واحدة

**Authentication:** ❌ Public (غير مطلوب)

**Headers:**
```
Accept-Language: ar|en (اختياري، الافتراضي: ar)
```

**URL Parameters:**
- `id` (required): معرف الفئة (عدد صحيح)

**Response 200:**
```json
{
  "success": true,
  "message": "Item category retrieved successfully",
  "message_ar": "تم جلب الفئة بنجاح",
  "data": {
    "id": 1,
    "name": "إلكترونيات",
    "name_ar": "إلكترونيات",
    "name_en": "Electronics",
    "icon": "phone",
    "icon_color": "#3B82F6",
    "sort_order": 1
  }
}
```

**Response 404:**
```json
{
  "success": false,
  "message": "Category not found",
  "message_ar": "الفئة غير موجودة"
}
```

---

## 2. Smart Bags Management (إدارة الحقائب الذكية)

### 2.1 الحصول على جميع الحقائب

**Endpoint:** `GET /api/smart-bags`

**Description:** جلب جميع حقائب المستخدم مع إمكانية التصفية والترتيب

**Authentication:** ✅ Required (Bearer Token)

**Query Parameters:**
- `status` (optional): حالة الحقيبة - `draft`, `in_progress`, `completed`, `cancelled`
- `trip_type` (optional): نوع الرحلة - `عمل`, `سياحة`, `عائلية`, `علاج`
- `upcoming` (optional): `true` للحصول على الرحلات القادمة فقط
- `sort_by` (optional): حقل الترتيب - `departure_date`, `created_at`, `name` (الافتراضي: `departure_date`)
- `sort_order` (optional): اتجاه الترتيب - `asc`, `desc` (الافتراضي: `asc`)
- `per_page` (optional): عدد النتائج في الصفحة (الافتراضي: 15)

**Example Request:**
```
GET /api/smart-bags?status=in_progress&trip_type=سياحة&upcoming=true&sort_by=departure_date&sort_order=asc
```

**Response 200:**
```json
{
  "success": true,
  "message": "Bags retrieved successfully",
  "message_ar": "تم جلب الحقائب بنجاح",
  "data": [
    {
      "id": 1,
      "name": "رحلة دبي",
      "trip_type": "سياحة",
      "trip_type_en": "Tourism",
      "duration": 5,
      "destination": "دبي",
      "departure_date": "2024-12-25",
      "max_weight": 23.00,
      "total_weight": 18.50,
      "weight_percentage": 80.43,
      "remaining_weight": 4.50,
      "is_overweight": false,
      "days_until_departure": 15,
      "status": "in_progress",
      "status_en": "In Progress",
      "preferences": {
        "style": "standard",
        "priorities": ["comfort", "utility"]
      },
      "is_analyzed": true,
      "last_analyzed_at": "2024-12-10T10:30:00Z",
      "items_count": 12,
      "items": [...],
      "latest_analysis": {...},
      "created_at": "2024-12-01T08:00:00Z",
      "updated_at": "2024-12-10T10:30:00Z"
    }
  ],
  "pagination": {
    "total": 10,
    "per_page": 15,
    "current_page": 1,
    "last_page": 1
  }
}
```

**Fields Description:**
- `id`: معرف الحقيبة (UUID أو عدد صحيح)
- `name`: اسم الحقيبة (نص)
- `trip_type`: نوع الرحلة (عمل، سياحة، عائلية، علاج)
- `trip_type_en`: نوع الرحلة بالإنجليزية
- `duration`: مدة الرحلة بالأيام (عدد صحيح، 1-365)
- `destination`: الوجهة (نص)
- `departure_date`: تاريخ المغادرة (تاريخ YYYY-MM-DD)
- `max_weight`: الحد الأقصى للوزن بالكيلوجرام (عدد عشري)
- `total_weight`: الوزن الإجمالي الحالي (عدد عشري)
- `weight_percentage`: نسبة الوزن المستخدمة (عدد عشري 0-100)
- `remaining_weight`: الوزن المتبقي (عدد عشري)
- `is_overweight`: هل الحقيبة تجاوزت الحد الأقصى (boolean)
- `days_until_departure`: عدد الأيام حتى المغادرة (عدد صحيح)
- `status`: حالة الحقيبة (draft, in_progress, completed, cancelled)
- `status_en`: حالة الحقيبة بالإنجليزية
- `preferences`: تفضيلات المستخدم (object)
- `is_analyzed`: هل تم تحليل الحقيبة (boolean)
- `last_analyzed_at`: تاريخ آخر تحليل (ISO 8601)
- `items_count`: عدد الأغراض (عدد صحيح)
- `items`: قائمة الأغراض (array of objects)
- `latest_analysis`: آخر تحليل (object)

---

### 2.2 إنشاء حقيبة جديدة

**Endpoint:** `POST /api/smart-bags`

**Description:** إنشاء حقيبة ذكية جديدة

**Authentication:** ✅ Required (Bearer Token)

**Request Body:**
```json
{
  "name": "رحلة دبي",
  "trip_type": "سياحة",
  "duration": 5,
  "destination": "دبي",
  "departure_date": "2024-12-25",
  "max_weight": 23.00,
  "status": "draft",
  "preferences": {
    "style": "standard",
    "priorities": ["comfort", "utility"]
  },
  "items": [
    {
      "name": "شاحن هاتف",
      "weight": 0.2,
      "item_category_id": 1,
      "essential": true,
      "packed": false,
      "quantity": 1,
      "notes": "شاحن سريع"
    }
  ]
}
```

**Validation Rules:**
- `name` (required, string, max:255): اسم الحقيبة
- `trip_type` (required, enum): نوع الرحلة - `عمل`, `سياحة`, `عائلية`, `علاج`
- `duration` (required, integer, min:1, max:365): مدة الرحلة بالأيام
- `destination` (required, string, max:255): الوجهة
- `departure_date` (required, date, after_or_equal:today): تاريخ المغادرة
- `max_weight` (required, numeric, min:0, max:999.99): الحد الأقصى للوزن
- `status` (optional, enum): حالة الحقيبة - `draft`, `in_progress`, `completed`, `cancelled` (الافتراضي: `draft`)
- `preferences` (optional, object): تفضيلات المستخدم
  - `preferences.style` (optional, string): نمط التفضيل
  - `preferences.priorities` (optional, array): الأولويات
- `items` (optional, array): قائمة الأغراض (يمكن إضافتها لاحقًا)
  - `items.*.name` (required, string, max:255): اسم الغرض
  - `items.*.weight` (required, numeric, min:0, max:999.99): وزن الغرض
  - `items.*.item_category_id` (required, integer, exists:item_categories,id): معرف فئة الغرض
  - `items.*.essential` (optional, boolean): هل الغرض ضروري (الافتراضي: false)
  - `items.*.packed` (optional, boolean): هل تم تحزيم الغرض (الافتراضي: false)
  - `items.*.quantity` (optional, integer, min:1): الكمية (الافتراضي: 1)
  - `items.*.notes` (optional, string): ملاحظات

**Response 201:**
```json
{
  "success": true,
  "message": "Bag created successfully",
  "message_ar": "تم إنشاء الحقيبة بنجاح",
  "data": {
    "id": 1,
    "name": "رحلة دبي",
    "trip_type": "سياحة",
    "trip_type_en": "Tourism",
    "duration": 5,
    "destination": "دبي",
    "departure_date": "2024-12-25",
    "max_weight": 23.00,
    "total_weight": 0.20,
    "weight_percentage": 0.87,
    "remaining_weight": 22.80,
    "is_overweight": false,
    "days_until_departure": 15,
    "status": "draft",
    "status_en": "Draft",
    "preferences": {...},
    "is_analyzed": false,
    "last_analyzed_at": null,
    "items_count": 1,
    "items": [...],
    "latest_analysis": null,
    "created_at": "2024-12-10T10:00:00Z",
    "updated_at": "2024-12-10T10:00:00Z"
  }
}
```

**Response 422 (Validation Error):**
```json
{
  "success": false,
  "message": "The given data was invalid.",
  "message_ar": "البيانات المدخلة غير صحيحة",
  "errors": {
    "name": ["اسم الحقيبة مطلوب"],
    "departure_date": ["تاريخ المغادرة يجب أن يكون اليوم أو في المستقبل"]
  }
}
```

---

### 2.3 الحصول على حقيبة واحدة

**Endpoint:** `GET /api/smart-bags/{bagId}`

**Description:** جلب تفاصيل حقيبة واحدة مع جميع أغراضها وآخر تحليل

**Authentication:** ✅ Required (Bearer Token)

**URL Parameters:**
- `bagId` (required): معرف الحقيبة (UUID أو عدد صحيح)

**Response 200:**
```json
{
  "success": true,
  "message": "Bag retrieved successfully",
  "message_ar": "تم جلب الحقيبة بنجاح",
  "data": {
    "id": 1,
    "name": "رحلة دبي",
    "trip_type": "سياحة",
    "trip_type_en": "Tourism",
    "duration": 5,
    "destination": "دبي",
    "departure_date": "2024-12-25",
    "max_weight": 23.00,
    "total_weight": 18.50,
    "weight_percentage": 80.43,
    "remaining_weight": 4.50,
    "is_overweight": false,
    "days_until_departure": 15,
    "status": "in_progress",
    "status_en": "In Progress",
    "preferences": {...},
    "is_analyzed": true,
    "last_analyzed_at": "2024-12-10T10:30:00Z",
    "items_count": 12,
    "items": [
      {
        "id": 1,
        "name": "شاحن هاتف",
        "weight": 0.20,
        "total_weight": 0.20,
        "item_category_id": 1,
        "category": {
          "id": 1,
          "name": "إلكترونيات",
          "name_ar": "إلكترونيات",
          "name_en": "Electronics",
          "icon": "phone",
          "icon_color": "#3B82F6"
        },
        "essential": true,
        "packed": false,
        "quantity": 1,
        "notes": "شاحن سريع",
        "created_at": "2024-12-10T10:00:00Z",
        "updated_at": "2024-12-10T10:00:00Z"
      }
    ],
    "latest_analysis": {...},
    "created_at": "2024-12-01T08:00:00Z",
    "updated_at": "2024-12-10T10:30:00Z"
  }
}
```

**Response 404:**
```json
{
  "success": false,
  "message": "Bag not found",
  "message_ar": "الحقيبة غير موجودة"
}
```

---

### 2.4 تحديث حقيبة

**Endpoint:** `PUT /api/smart-bags/{bagId}`

**Description:** تحديث بيانات حقيبة موجودة

**Authentication:** ✅ Required (Bearer Token)

**URL Parameters:**
- `bagId` (required): معرف الحقيبة (UUID أو عدد صحيح)

**Request Body:** (جميع الحقول اختيارية)
```json
{
  "name": "رحلة دبي - محدث",
  "trip_type": "عائلية",
  "duration": 7,
  "destination": "دبي، الإمارات",
  "departure_date": "2024-12-26",
  "max_weight": 30.00,
  "status": "in_progress",
  "preferences": {
    "style": "luxury",
    "priorities": ["comfort", "luxury", "utility"]
  }
}
```

**Validation Rules:** (نفس قواعد الإنشاء ولكن جميع الحقول اختيارية باستخدام `sometimes`)

**Response 200:**
```json
{
  "success": true,
  "message": "Bag updated successfully",
  "message_ar": "تم تحديث الحقيبة بنجاح",
  "data": {
    "id": 1,
    "name": "رحلة دبي - محدث",
    "trip_type": "عائلية",
    "trip_type_en": "Family",
    ...
  }
}
```

**Response 404:**
```json
{
  "success": false,
  "message": "Bag not found",
  "message_ar": "الحقيبة غير موجودة"
}
```

---

### 2.5 حذف حقيبة

**Endpoint:** `DELETE /api/smart-bags/{bagId}`

**Description:** حذف حقيبة (Soft Delete)

**Authentication:** ✅ Required (Bearer Token)

**URL Parameters:**
- `bagId` (required): معرف الحقيبة (UUID أو عدد صحيح)

**Response 200:**
```json
{
  "success": true,
  "message": "Bag deleted successfully",
  "message_ar": "تم حذف الحقيبة بنجاح"
}
```

**Response 404:**
```json
{
  "success": false,
  "message": "Bag not found",
  "message_ar": "الحقيبة غير موجودة"
}
```

---

## 3. Bag Items Management (إدارة أغراض الحقيبة)

### 3.1 إضافة غرض إلى الحقيبة

**Endpoint:** `POST /api/smart-bags/{bagId}/items`

**Description:** إضافة غرض جديد إلى حقيبة موجودة

**Authentication:** ✅ Required (Bearer Token)

**URL Parameters:**
- `bagId` (required): معرف الحقيبة (UUID أو عدد صحيح)

**Request Body:**
```json
{
  "name": "شاحن لابتوب",
  "weight": 0.5,
  "item_category_id": 1,
  "essential": true,
  "packed": false,
  "quantity": 1,
  "notes": "شاحن MacBook"
}
```

**Validation Rules:**
- `name` (required, string, max:255): اسم الغرض
- `weight` (required, numeric, min:0, max:999.99): وزن الغرض بالكيلوجرام
- `item_category_id` (required, integer, exists:item_categories,id): معرف فئة الغرض
- `essential` (optional, boolean): هل الغرض ضروري (الافتراضي: false)
- `packed` (optional, boolean): هل تم تحزيم الغرض (الافتراضي: false)
- `quantity` (optional, integer, min:1, max:999): الكمية (الافتراضي: 1)
- `notes` (optional, string, max:1000): ملاحظات إضافية

**Response 201:**
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
    "item_category_id": 1,
    "category": {
      "id": 1,
      "name": "إلكترونيات",
      "name_ar": "إلكترونيات",
      "name_en": "Electronics",
      "icon": "phone",
      "icon_color": "#3B82F6"
    },
    "essential": true,
    "packed": false,
    "quantity": 1,
    "notes": "شاحن MacBook",
    "created_at": "2024-12-10T11:00:00Z",
    "updated_at": "2024-12-10T11:00:00Z"
  }
}
```

**Response 404:**
```json
{
  "success": false,
  "message": "Bag not found",
  "message_ar": "الحقيبة غير موجودة"
}
```

**Note:** بعد إضافة الغرض، يتم إعادة حساب الوزن الإجمالي للحقيبة تلقائيًا.

---

### 3.2 تحديث غرض

**Endpoint:** `PUT /api/smart-bags/{bagId}/items/{itemId}`

**Description:** تحديث بيانات غرض موجود في الحقيبة

**Authentication:** ✅ Required (Bearer Token)

**URL Parameters:**
- `bagId` (required): معرف الحقيبة (UUID أو عدد صحيح)
- `itemId` (required): معرف الغرض (UUID أو عدد صحيح)

**Request Body:** (نفس قواعد إضافة غرض، جميع الحقول اختيارية)
```json
{
  "name": "شاحن لابتوب - محدث",
  "weight": 0.6,
  "item_category_id": 1,
  "essential": false,
  "packed": true,
  "quantity": 2,
  "notes": "شاحن MacBook Pro"
}
```

**Response 200:**
```json
{
  "success": true,
  "message": "Item updated successfully",
  "message_ar": "تم تحديث الغرض بنجاح",
  "data": {
    "id": 13,
    "name": "شاحن لابتوب - محدث",
    "weight": 0.60,
    "total_weight": 1.20,
    "item_category_id": 1,
    "category": {...},
    "essential": false,
    "packed": true,
    "quantity": 2,
    "notes": "شاحن MacBook Pro",
    "created_at": "2024-12-10T11:00:00Z",
    "updated_at": "2024-12-10T11:30:00Z"
  }
}
```

**Note:** `total_weight` = `weight` × `quantity`

---

### 3.3 تغيير حالة التحزيم

**Endpoint:** `POST /api/smart-bags/{bagId}/items/{itemId}/toggle-packed`

**Description:** تغيير حالة تحزيم الغرض (packed/unpacked)

**Authentication:** ✅ Required (Bearer Token)

**URL Parameters:**
- `bagId` (required): معرف الحقيبة (UUID أو عدد صحيح)
- `itemId` (required): معرف الغرض (UUID أو عدد صحيح)

**Request Body:** لا يوجد

**Response 200:**
```json
{
  "success": true,
  "message": "Item packed status updated",
  "message_ar": "تم تحديث حالة التحزيم",
  "data": {
    "id": 13,
    "name": "شاحن لابتوب",
    "weight": 0.50,
    "total_weight": 0.50,
    "item_category_id": 1,
    "category": {...},
    "essential": true,
    "packed": true,
    "quantity": 1,
    "notes": "شاحن MacBook",
    "created_at": "2024-12-10T11:00:00Z",
    "updated_at": "2024-12-10T11:45:00Z"
  }
}
```

---

### 3.4 حذف غرض

**Endpoint:** `DELETE /api/smart-bags/{bagId}/items/{itemId}`

**Description:** حذف غرض من الحقيبة

**Authentication:** ✅ Required (Bearer Token)

**URL Parameters:**
- `bagId` (required): معرف الحقيبة (UUID أو عدد صحيح)
- `itemId` (required): معرف الغرض (UUID أو عدد صحيح)

**Response 200:**
```json
{
  "success": true,
  "message": "Item deleted successfully",
  "message_ar": "تم حذف الغرض بنجاح"
}
```

**Note:** بعد حذف الغرض، يتم إعادة حساب الوزن الإجمالي للحقيبة تلقائيًا.

---

## 4. AI Analysis (التحليل بالذكاء الاصطناعي)

### 4.1 تحليل الحقيبة بالذكاء الاصطناعي

**Endpoint:** `POST /api/smart-bags/{bagId}/analyze`

**Description:** تحليل الحقيبة باستخدام الذكاء الاصطناعي (Gemini AI) لتقديم اقتراحات وتحسينات

**Authentication:** ✅ Required (Bearer Token)

**URL Parameters:**
- `bagId` (required): معرف الحقيبة (UUID أو عدد صحيح)

**Request Body:**
```json
{
  "preferences": {
    "style": "standard",
    "priorities": ["comfort", "utility"]
  },
  "force_reanalysis": false
}
```

**Validation Rules:**
- `preferences` (optional, object): تفضيلات التحليل
  - `preferences.style` (optional, enum): نمط التفضيل - `minimalist`, `standard`, `luxury`
  - `preferences.priorities` (optional, array): قائمة الأولويات
- `force_reanalysis` (optional, boolean): إجبار إعادة التحليل حتى لو تم التحليل مؤخرًا (الافتراضي: false)

**Constraints:**
- الحقيبة يجب أن تحتوي على أغراض على الأقل
- إذا تم التحليل خلال آخر 24 ساعة، يتم رفض الطلب إلا إذا كان `force_reanalysis=true`

**Response 201:**
```json
{
  "success": true,
  "message": "Bag analyzed successfully",
  "message_ar": "تم تحليل الحقيبة بنجاح",
  "data": {
    "id": 1,
    "analysis_id": "analysis_1234567890",
    "bag_id": 1,
    "missing_items": [
      {
        "name": "محول كهرباء",
        "name_en": "Power Adapter",
        "category": "إلكترونيات",
        "reason": "مطلوب للشحن في دبي",
        "priority": "high"
      }
    ],
    "missing_items_count": 3,
    "extra_items": [
      {
        "name": "كتاب",
        "name_en": "Book",
        "reason": "يمكن استبداله بكتاب إلكتروني لتوفير الوزن",
        "weight_saved": 0.3
      }
    ],
    "extra_items_count": 2,
    "weight_optimization": [
      {
        "item": "شاحن لابتوب",
        "suggestion": "استبدل بشاحن أخف",
        "weight_saved": 0.2
      }
    ],
    "weight_saved": 0.5,
    "additional_suggestions": [
      {
        "type": "organization",
        "message": "نظم الأغراض حسب الفئة لتسهيل الوصول",
        "message_en": "Organize items by category for easy access"
      }
    ],
    "suggestions_count": 5,
    "smart_alert": {
      "type": "warning",
      "message": "يوجد 3 أغراض مهمة مفقودة",
      "message_en": "3 important items are missing"
    },
    "has_high_priority_alerts": true,
    "high_priority_missing_items": [...],
    "confidence_score": 0.85,
    "processing_time_ms": 1250,
    "ai_model": "gemini-2.0-flash-exp",
    "metadata": {
      "version": "1.0",
      "timestamp": "2024-12-10T12:00:00Z"
    },
    "created_at": "2024-12-10T12:00:00Z",
    "updated_at": "2024-12-10T12:00:00Z"
  }
}
```

**Response 422 (Empty Bag):**
```json
{
  "success": false,
  "message": "Cannot analyze empty bag. Please add items first.",
  "message_ar": "لا يمكن تحليل حقيبة فارغة. الرجاء إضافة أغراض أولاً."
}
```

**Response 422 (Recent Analysis):**
```json
{
  "success": false,
  "message": "Bag was analyzed recently. Use force_reanalysis=true to reanalyze.",
  "message_ar": "تم تحليل الحقيبة مؤخراً. استخدم force_reanalysis=true لإعادة التحليل.",
  "last_analyzed_at": "2024-12-10T11:00:00Z"
}
```

**Analysis Fields Description:**
- `missing_items`: قائمة الأغراض المفقودة الموصى بإضافتها
- `missing_items_count`: عدد الأغراض المفقودة
- `extra_items`: قائمة الأغراض التي يمكن إزالتها
- `extra_items_count`: عدد الأغراض الإضافية
- `weight_optimization`: اقتراحات لتحسين الوزن
- `weight_saved`: الوزن المحفوظ بالكيلوجرام إذا تم تطبيق الاقتراحات
- `additional_suggestions`: اقتراحات إضافية
- `suggestions_count`: عدد الاقتراحات الإضافية
- `smart_alert`: تنبيه ذكي رئيسي
- `has_high_priority_alerts`: هل يوجد تنبيهات عالية الأولوية
- `high_priority_missing_items`: الأغراض المفقودة عالية الأولوية
- `confidence_score`: درجة الثقة في التحليل (0-1)
- `processing_time_ms`: وقت المعالجة بالميلي ثانية
- `ai_model`: نموذج الذكاء الاصطناعي المستخدم

---

### 4.2 الحصول على آخر تحليل

**Endpoint:** `GET /api/smart-bags/{bagId}/analysis/latest`

**Description:** جلب آخر تحليل تم إجراؤه على الحقيبة

**Authentication:** ✅ Required (Bearer Token)

**URL Parameters:**
- `bagId` (required): معرف الحقيبة (UUID أو عدد صحيح)

**Response 200:**
```json
{
  "success": true,
  "message": "Latest analysis retrieved successfully",
  "message_ar": "تم جلب آخر تحليل بنجاح",
  "data": {
    "id": 1,
    "analysis_id": "analysis_1234567890",
    "bag_id": 1,
    "missing_items": [...],
    "missing_items_count": 3,
    "extra_items": [...],
    "extra_items_count": 2,
    "weight_optimization": [...],
    "weight_saved": 0.5,
    "additional_suggestions": [...],
    "suggestions_count": 5,
    "smart_alert": {...},
    "has_high_priority_alerts": true,
    "high_priority_missing_items": [...],
    "confidence_score": 0.85,
    "processing_time_ms": 1250,
    "ai_model": "gemini-2.0-flash-exp",
    "metadata": {...},
    "created_at": "2024-12-10T12:00:00Z",
    "updated_at": "2024-12-10T12:00:00Z"
  }
}
```

**Response 404:**
```json
{
  "success": false,
  "message": "No analysis found for this bag",
  "message_ar": "لا يوجد تحليل لهذه الحقيبة"
}
```

---

### 4.3 الحصول على سجل التحليلات

**Endpoint:** `GET /api/smart-bags/{bagId}/analysis/history`

**Description:** جلب سجل جميع التحليلات التي تم إجراؤها على الحقيبة

**Authentication:** ✅ Required (Bearer Token)

**URL Parameters:**
- `bagId` (required): معرف الحقيبة (UUID أو عدد صحيح)

**Query Parameters:**
- `per_page` (optional): عدد النتائج في الصفحة (الافتراضي: 10)

**Response 200:**
```json
{
  "success": true,
  "message": "Analysis history retrieved successfully",
  "message_ar": "تم جلب سجل التحليلات بنجاح",
  "data": [
    {
      "id": 1,
      "analysis_id": "analysis_1234567890",
      "bag_id": 1,
      "missing_items": [...],
      "missing_items_count": 3,
      ...
      "created_at": "2024-12-10T12:00:00Z",
      "updated_at": "2024-12-10T12:00:00Z"
    },
    {
      "id": 2,
      ...
    }
  ],
  "pagination": {
    "total": 5,
    "per_page": 10,
    "current_page": 1,
    "last_page": 1
  }
}
```

---

## 5. Smart Alert (التنبيه الذكي)

### 5.1 الحصول على التنبيه الذكي

**Endpoint:** `GET /api/smart-bags/{bagId}/smart-alert`

**Description:** الحصول على التنبيه الذكي للحقيبة بناءً على حالة الحقيبة وموعد المغادرة

**Authentication:** ✅ Required (Bearer Token)

**URL Parameters:**
- `bagId` (required): معرف الحقيبة (UUID أو عدد صحيح)

**Response 200 (With Alerts):**
```json
{
  "success": true,
  "message": "Smart alert retrieved successfully",
  "message_ar": "تم جلب التنبيه الذكي بنجاح",
  "data": {
    "alert_id": "alert_1733839200_1",
    "bag_id": 1,
    "hours_remaining": 24,
    "time_remaining": "1 يوم",
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
        "category": "unpacked",
        "message": "يوجد 3 أغراض ضرورية غير محزومة",
        "message_en": "3 essential items are not packed",
        "action": "راجع الأغراض الضرورية وقم بتحزيمها",
        "action_en": "Review essential items and pack them",
        "severity": "high"
      }
    ],
    "message": "تبقى 1 يوم على الرحلة وحقيبة الأدوية غير مكتملة",
    "severity": "high",
    "created_at": "2024-12-10T13:00:00Z"
  }
}
```

**Response 200 (No Alerts):**
```json
{
  "success": true,
  "message": "No alerts for this bag",
  "message_ar": "لا توجد تنبيهات لهذه الحقيبة",
  "data": null
}
```

**Alert Categories:**
- `medicines`: مشاكل متعلقة بالأدوية
- `documents`: مشاكل متعلقة بالمستندات (لرحلات العمل)
- `weight`: مشاكل متعلقة بالوزن
- `unpacked`: أغراض ضرورية غير محزومة

**Severity Levels:**
- `high`: تنبيه عالي الأولوية (يحتاج إجراء فوري)
- `medium`: تنبيه متوسط الأولوية
- `low`: تنبيه منخفض الأولوية

---

## 6. Data Models (نماذج البيانات)

### 6.1 Bag Model (نموذج الحقيبة)

```typescript
interface Bag {
  id: string | number;
  name: string;
  trip_type: "عمل" | "سياحة" | "عائلية" | "علاج";
  trip_type_en: "Business" | "Tourism" | "Family" | "Medical";
  duration: number; // 1-365 days
  destination: string;
  departure_date: string; // YYYY-MM-DD
  max_weight: number; // kg
  total_weight: number; // kg
  weight_percentage: number; // 0-100
  remaining_weight: number; // kg
  is_overweight: boolean;
  days_until_departure: number;
  status: "draft" | "in_progress" | "completed" | "cancelled";
  status_en: "Draft" | "In Progress" | "Completed" | "Cancelled";
  preferences: {
    style?: string;
    priorities?: string[];
    [key: string]: any;
  };
  is_analyzed: boolean;
  last_analyzed_at: string | null; // ISO 8601
  items_count: number;
  items: BagItem[];
  latest_analysis: BagAnalysis | null;
  created_at: string; // ISO 8601
  updated_at: string; // ISO 8601
}
```

### 6.2 BagItem Model (نموذج غرض الحقيبة)

```typescript
interface BagItem {
  id: string | number;
  name: string;
  weight: number; // kg
  total_weight: number; // weight × quantity
  item_category_id: number;
  category: {
    id: number;
    name: string;
    name_ar: string;
    name_en: string;
    icon: string;
    icon_color: string;
  } | null;
  essential: boolean;
  packed: boolean;
  quantity: number;
  notes: string | null;
  created_at: string; // ISO 8601
  updated_at: string; // ISO 8601
}
```

### 6.3 BagAnalysis Model (نموذج التحليل)

```typescript
interface BagAnalysis {
  id: number;
  analysis_id: string;
  bag_id: number;
  missing_items: Array<{
    name: string;
    name_en: string;
    category: string;
    reason: string;
    priority: "high" | "medium" | "low";
  }>;
  missing_items_count: number;
  extra_items: Array<{
    name: string;
    name_en: string;
    reason: string;
    weight_saved: number;
  }>;
  extra_items_count: number;
  weight_optimization: Array<{
    item: string;
    suggestion: string;
    weight_saved: number;
  }>;
  weight_saved: number; // kg
  additional_suggestions: Array<{
    type: string;
    message: string;
    message_en: string;
  }>;
  suggestions_count: number;
  smart_alert: {
    type: string;
    message: string;
    message_en: string;
  } | null;
  has_high_priority_alerts: boolean;
  high_priority_missing_items: Array<any>;
  confidence_score: number; // 0-1
  processing_time_ms: number;
  ai_model: string;
  metadata: Record<string, any>;
  created_at: string; // ISO 8601
  updated_at: string; // ISO 8601
}
```

### 6.4 SmartAlert Model (نموذج التنبيه الذكي)

```typescript
interface SmartAlert {
  alert_id: string;
  bag_id: number;
  hours_remaining: number;
  time_remaining: string;
  issues: Array<{
    category: "medicines" | "documents" | "weight" | "unpacked";
    message: string;
    message_en: string;
    action: string;
    action_en: string;
    severity: "high" | "medium" | "low";
  }>;
  message: string;
  severity: "high" | "medium" | "low";
  created_at: string; // ISO 8601
}
```

### 6.5 ItemCategory Model (نموذج فئة الغرض)

```typescript
interface ItemCategory {
  id: number;
  name: string;
  name_ar: string;
  name_en: string;
  icon: string;
  icon_color: string;
  sort_order: number;
}
```

---

## 7. Error Handling (معالجة الأخطاء)

### 7.1 Error Response Format

جميع الأخطاء تتبع نفس الصيغة:

```json
{
  "success": false,
  "message": "Error message in English",
  "message_ar": "رسالة الخطأ بالعربية",
  "error": "Detailed error message (optional)",
  "errors": {
    "field_name": ["Validation error message"]
  }
}
```

### 7.2 HTTP Status Codes

- **200 OK**: طلب ناجح
- **201 Created**: تم إنشاء المورد بنجاح
- **404 Not Found**: المورد غير موجود
- **422 Unprocessable Entity**: خطأ في التحقق من البيانات
- **500 Internal Server Error**: خطأ في الخادم

### 7.3 Common Errors

**401 Unauthorized:**
```json
{
  "message": "Unauthenticated."
}
```

**404 Not Found:**
```json
{
  "success": false,
  "message": "Bag not found",
  "message_ar": "الحقيبة غير موجودة"
}
```

**422 Validation Error:**
```json
{
  "success": false,
  "message": "The given data was invalid.",
  "message_ar": "البيانات المدخلة غير صحيحة",
  "errors": {
    "name": ["اسم الحقيبة مطلوب"],
    "departure_date": ["تاريخ المغادرة يجب أن يكون اليوم أو في المستقبل"],
    "max_weight": ["الحد الأقصى للوزن يجب أن يكون أكبر من صفر"]
  }
}
```

**500 Server Error:**
```json
{
  "success": false,
  "message": "Failed to create bag",
  "message_ar": "فشل في إنشاء الحقيبة",
  "error": "Internal server error details"
}
```

---

## 8. Authentication (المصادقة)

جميع endpoints الخاصة بـ Smart Bags تتطلب المصادقة باستخدام Laravel Sanctum.

### 8.1 Headers Required

```
Authorization: Bearer {token}
Accept: application/json
Accept-Language: ar|en (optional)
```

### 8.2 Getting Token

يتم الحصول على Token من endpoint المصادقة:

```
POST /api/auth/login
```

**Response:**
```json
{
  "success": true,
  "data": {
    "user": {...},
    "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
  }
}
```

استخدم هذا Token في header `Authorization` لجميع الطلبات.

---

## 9. Examples (أمثلة)

### 9.1 Example: إنشاء حقيبة كاملة

```bash
# 1. إنشاء حقيبة
POST /api/smart-bags
{
  "name": "رحلة دبي",
  "trip_type": "سياحة",
  "duration": 5,
  "destination": "دبي",
  "departure_date": "2024-12-25",
  "max_weight": 23.00,
  "status": "draft"
}

# 2. إضافة أغراض
POST /api/smart-bags/1/items
{
  "name": "شاحن هاتف",
  "weight": 0.2,
  "item_category_id": 1,
  "essential": true,
  "quantity": 1
}

POST /api/smart-bags/1/items
{
  "name": "ملابس",
  "weight": 5.0,
  "item_category_id": 2,
  "essential": true,
  "quantity": 5
}

# 3. تحديث الحالة
PUT /api/smart-bags/1
{
  "status": "in_progress"
}

# 4. تحليل الحقيبة
POST /api/smart-bags/1/analyze
{
  "preferences": {
    "style": "standard"
  }
}

# 5. الحصول على التنبيه الذكي
GET /api/smart-bags/1/smart-alert

# 6. تحديث حالة تحزيم غرض
POST /api/smart-bags/1/items/1/toggle-packed
```

### 9.2 Example: تصفية الحقائب

```bash
# الحقائب قيد التقدم من نوع سياحة
GET /api/smart-bags?status=in_progress&trip_type=سياحة

# الرحلات القادمة مرتبة حسب التاريخ
GET /api/smart-bags?upcoming=true&sort_by=departure_date&sort_order=asc

# رحلة عمل
GET /api/smart-bags?trip_type=عمل&status=in_progress
```

---

## 10. Notes (ملاحظات مهمة)

1. **الوزن**: جميع الأوزان بالكيلوجرام (kg)
2. **التواريخ**: جميع التواريخ بصيغة ISO 8601 أو YYYY-MM-DD
3. **اللغة**: استخدم header `Accept-Language` للحصول على الرسائل باللغة المطلوبة (ar/en)
4. **التحليل**: يتم حفظ التحليلات في قاعدة البيانات ويمكن الرجوع إليها
5. **التنبيهات**: التنبيهات الذكية يتم توليدها ديناميكيًا بناءً على حالة الحقيبة
6. **الوزن التلقائي**: يتم إعادة حساب الوزن الإجمالي تلقائيًا عند إضافة/تحديث/حذف الأغراض
7. **Soft Delete**: عند حذف حقيبة، يتم استخدام Soft Delete (لا يتم حذف البيانات فعليًا)
8. **التحليل المتكرر**: لا يمكن تحليل الحقيبة أكثر من مرة خلال 24 ساعة إلا باستخدام `force_reanalysis=true`

---

## 11. Rate Limiting (حدود الطلبات)

قد يتم تطبيق حدود على عدد الطلبات:
- تحليل الحقيبة: محدود لتجنب الإفراط في استخدام AI
- التنبيهات: قد يتم تخزينها مؤقتًا (cache) لتحسين الأداء

---

## 12. Version (الإصدار)

**API Version:** 1.0  
**Last Updated:** 2024-12-10

---

## 13. Support (الدعم)

للمزيد من المعلومات أو الدعم الفني، يرجى التواصل مع فريق التطوير.

---

**تم إنشاء هذا التوثيق بواسطة:** Auto (Cursor AI)  
**تاريخ الإنشاء:** 2024-12-10



