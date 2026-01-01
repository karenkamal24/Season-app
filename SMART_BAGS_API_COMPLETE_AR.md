# 📱 Smart Bags API - دليل كامل للفرونت إند

## 🎯 نظرة عامة

**Base URL:** `http://localhost:8000/api`

**جميع الطلبات تحتاج:**
```
Authorization: Bearer YOUR_TOKEN
Accept-Language: ar (للعربية) أو en (للإنجليزية)
Content-Type: application/json
```

---

## 📚 الفهرس السريع

1. [فئات الأغراض (Item Categories)](#1-item-categories) - بدون Token ✨
2. [إدارة الحقائب (Bags CRUD)](#2-bags-crud)
3. [إدارة الأغراض (Items Management)](#3-items-management)
4. [التحليل الذكي (AI Analysis)](#4-ai-analysis)

**المجموع:** 14 Endpoint

---

## 1. Item Categories

### 1.1 🔓 الحصول على جميع الفئات (بدون Token)

**URL:** `GET /api/item-categories`

**Headers:**
```
Accept-Language: ar
```

**Response:**
```json
{
  "success": true,
  "status": 200,
  "message": "تم جلب الفئات بنجاح",
  "data": [
    {
      "id": 1,
      "name": "ملابس",
      "icon": "shirt",
      "icon_color": "#3B82F6",
      "sort_order": 1,
      "is_active": true
    },
    {
      "id": 2,
      "name": "أحذية",
      "icon": "shoe",
      "icon_color": "#8B5CF6",
      "sort_order": 2,
      "is_active": true
    },
    {
      "id": 3,
      "name": "إلكترونيات",
      "icon": "laptop",
      "icon_color": "#10B981",
      "sort_order": 3,
      "is_active": true
    },
    {
      "id": 4,
      "name": "أدوية وعناية",
      "icon": "medical",
      "icon_color": "#EF4444",
      "sort_order": 4,
      "is_active": true
    },
    {
      "id": 5,
      "name": "مستندات",
      "icon": "document",
      "icon_color": "#F59E0B",
      "sort_order": 5,
      "is_active": true
    },
    {
      "id": 6,
      "name": "أدوات نظافة",
      "icon": "spray",
      "icon_color": "#06B6D4",
      "sort_order": 6,
      "is_active": true
    },
    {
      "id": 7,
      "name": "إكسسوارات",
      "icon": "watch",
      "icon_color": "#EC4899",
      "sort_order": 7,
      "is_active": true
    },
    {
      "id": 8,
      "name": "كتب وترفيه",
      "icon": "book",
      "icon_color": "#6366F1",
      "sort_order": 8,
      "is_active": true
    },
    {
      "id": 9,
      "name": "طعام ووجبات خفيفة",
      "icon": "food",
      "icon_color": "#F97316",
      "sort_order": 9,
      "is_active": true
    },
    {
      "id": 10,
      "name": "أخرى",
      "icon": "dots",
      "icon_color": "#6B7280",
      "sort_order": 10,
      "is_active": true
    }
  ]
}
```

**💡 متى تستخدمها:**
- عند تحميل التطبيق لأول مرة
- عند عرض قائمة الفئات للمستخدم
- احفظها في state ولا تطلبها كل مرة

---

### 1.2 🔓 الحصول على فئة واحدة (بدون Token)

**URL:** `GET /api/item-categories/{id}`

**مثال:** `GET /api/item-categories/3`

**Response:**
```json
{
  "success": true,
  "status": 200,
  "message": "تم جلب الفئة بنجاح",
  "data": {
    "id": 3,
    "name": "إلكترونيات",
    "icon": "laptop",
    "icon_color": "#10B981",
    "sort_order": 3,
    "is_active": true
  }
}
```

---

## 2. Bags CRUD

### 2.1 📋 الحصول على جميع الحقائب

**URL:** `GET /api/smart-bags`

**Headers:**
```
Authorization: Bearer YOUR_TOKEN
Accept-Language: ar
```

**Response:**
```json
{
  "success": true,
  "status": 200,
  "message": "تم جلب الحقائب بنجاح",
  "data": [
    {
      "id": 1,
      "name": "حقيبة رحلة دبي",
      "trip_type": "عمل",
      "duration": 4,
      "destination": "دبي",
      "departure_date": "2026-01-20",
      "max_weight": 20.0,
      "total_weight": 5.3,
      "weight_percentage": 26,
      "remaining_weight": 14.7,
      "is_overweight": false,
      "days_until_departure": 18,
      "status": "قيد التجهيز",
      "preferences": [],
      "is_analyzed": true,
      "last_analyzed_at": "2026-01-01T22:30:45+00:00",
      "items_count": 2,
      "items": [
        {
          "id": 1,
          "name": "لابتوب ماك بوك",
          "weight": 2.3,
          "quantity": 1,
          "total_weight": 2.3,
          "item_category_id": 3,
          "category": {
            "id": 3,
            "name": "إلكترونيات",
            "icon": "laptop",
            "icon_color": "#10B981"
          },
          "essential": true,
          "packed": false,
          "notes": "للعمل",
          "created_at": "2026-01-01T21:00:00+00:00",
          "updated_at": "2026-01-01T21:00:00+00:00"
        },
        {
          "id": 2,
          "name": "ملابس رسمية",
          "weight": 3.0,
          "quantity": 1,
          "total_weight": 3.0,
          "item_category_id": 1,
          "category": {
            "id": 1,
            "name": "ملابس",
            "icon": "shirt",
            "icon_color": "#3B82F6"
          },
          "essential": true,
          "packed": true,
          "notes": null,
          "created_at": "2026-01-01T21:05:00+00:00",
          "updated_at": "2026-01-01T21:10:00+00:00"
        }
      ],
      "latest_analysis": {
        "id": 1,
        "missing_items": [
          {
            "item": "شاحن لابتوب",
            "category": "إلكترونيات",
            "priority": "high"
          }
        ],
        "confidence_score": 0.92,
        "analyzed_at": "2026-01-01T22:30:45+00:00"
      },
      "created_at": "2026-01-01T21:00:00+00:00",
      "updated_at": "2026-01-01T22:30:45+00:00"
    }
  ]
}
```

**💡 ملاحظات:**
- ترجع كل الحقائب للمستخدم
- كل حقيبة فيها الأغراض بتاعتها
- الوزن يتحسب تلقائياً

---

### 2.2 📄 الحصول على حقيبة واحدة

**URL:** `GET /api/smart-bags/{bagId}`

**مثال:** `GET /api/smart-bags/1`

**Headers:**
```
Authorization: Bearer YOUR_TOKEN
Accept-Language: ar
```

**Response:**
```json
{
  "success": true,
  "status": 200,
  "message": "تم جلب الحقيبة بنجاح",
  "data": {
    "id": 1,
    "name": "حقيبة رحلة دبي",
    "trip_type": "عمل",
    "duration": 4,
    "destination": "دبي",
    "departure_date": "2026-01-20",
    "max_weight": 20.0,
    "total_weight": 5.3,
    "weight_percentage": 26,
    "remaining_weight": 14.7,
    "is_overweight": false,
    "days_until_departure": 18,
    "status": "قيد التجهيز",
    "items_count": 2,
    "items": [ /* نفس بنية الأغراض */ ],
    "latest_analysis": { /* آخر تحليل */ }
  }
}
```

**💡 متى تستخدمها:**
- عند فتح صفحة تفاصيل الحقيبة
- بعد إضافة/حذف غرض لتحديث البيانات

---

### 2.3 ➕ إنشاء حقيبة جديدة

**URL:** `POST /api/smart-bags`

**Headers:**
```
Authorization: Bearer YOUR_TOKEN
Accept-Language: ar
Content-Type: application/json
```

**Body:**
```json
{
  "name": "حقيبة رحلة القاهرة",
  "trip_type": "سياحة",
  "duration": 5,
  "destination": "القاهرة",
  "departure_date": "2026-02-15",
  "max_weight": 23,
  "status": "draft"
}
```

**الحقول:**

| Field | Type | Required | Description | Options |
|-------|------|----------|-------------|---------|
| `name` | string | ✅ | اسم الحقيبة | - |
| `trip_type` | string | ✅ | نوع الرحلة | `عمل`, `سياحة`, `عائلية`, `علاج` |
| `duration` | integer | ✅ | مدة الرحلة بالأيام | > 0 |
| `destination` | string | ✅ | الوجهة | - |
| `departure_date` | date | ✅ | تاريخ المغادرة | YYYY-MM-DD |
| `max_weight` | decimal | ✅ | الوزن الأقصى (كجم) | > 0 |
| `status` | string | ❌ | الحالة | `draft`, `in_progress`, `completed` |
| `preferences` | array | ❌ | تفضيلات | ["أدوية", "مستندات"] |

**Response:**
```json
{
  "success": true,
  "status": 201,
  "message": "تم إنشاء الحقيبة بنجاح",
  "data": {
    "id": 2,
    "name": "حقيبة رحلة القاهرة",
    "trip_type": "سياحة",
    "duration": 5,
    "destination": "القاهرة",
    "departure_date": "2026-02-15",
    "max_weight": 23.0,
    "total_weight": 0.0,
    "weight_percentage": 0,
    "remaining_weight": 23.0,
    "is_overweight": false,
    "days_until_departure": 44,
    "status": "مسودة",
    "items_count": 0,
    "items": [],
    "created_at": "2026-01-02T10:00:00+00:00"
  }
}
```

---

### 2.4 ✏️ تعديل حقيبة

**URL:** `PUT /api/smart-bags/{bagId}`

**مثال:** `PUT /api/smart-bags/1`

**Body:** (أرسل فقط الحقول التي تريد تغييرها)
```json
{
  "name": "حقيبة دبي - محدثة",
  "max_weight": 25,
  "status": "in_progress"
}
```

**Response:**
```json
{
  "success": true,
  "status": 200,
  "message": "تم تحديث الحقيبة بنجاح",
  "data": {
    /* الحقيبة المحدثة */
  }
}
```

---

### 2.5 🗑️ حذف حقيبة

**URL:** `DELETE /api/smart-bags/{bagId}`

**مثال:** `DELETE /api/smart-bags/1`

**Response:**
```json
{
  "success": true,
  "status": 200,
  "message": "تم حذف الحقيبة بنجاح"
}
```

**⚠️ تحذير:** الحذف نهائي ويشمل كل الأغراض والتحليلات!

---

## 3. Items Management

### 3.1 ➕ إضافة غرض للحقيبة

**URL:** `POST /api/smart-bags/{bagId}/items`

**مثال:** `POST /api/smart-bags/1/items`

**Body:**
```json
{
  "name": "لابتوب ماك بوك برو",
  "weight": 2.3,
  "item_category_id": 3,
  "quantity": 1,
  "essential": true,
  "packed": false,
  "notes": "للعمل"
}
```

**الحقول:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `name` | string | ✅ | اسم الغرض |
| `weight` | decimal | ✅ | الوزن (كجم) |
| `item_category_id` | integer | ✅ | رقم الفئة |
| `quantity` | integer | ❌ | الكمية (افتراضي: 1) |
| `essential` | boolean | ❌ | ضروري؟ (افتراضي: false) |
| `packed` | boolean | ❌ | محزوم؟ (افتراضي: false) |
| `notes` | string | ❌ | ملاحظات |

**Response:**
```json
{
  "success": true,
  "status": 201,
  "message": "تم إضافة الغرض بنجاح",
  "data": {
    "id": 5,
    "name": "لابتوب ماك بوك برو",
    "weight": 2.3,
    "quantity": 1,
    "total_weight": 2.3,
    "item_category_id": 3,
    "category": {
      "id": 3,
      "name": "إلكترونيات",
      "icon": "laptop",
      "icon_color": "#10B981"
    },
    "essential": true,
    "packed": false,
    "notes": "للعمل",
    "created_at": "2026-01-02T10:30:00+00:00",
    "updated_at": "2026-01-02T10:30:00+00:00"
  }
}
```

**💡 ملاحظة:** الوزن الكلي للحقيبة يتحدث تلقائياً!

---

### 3.2 ✏️ تعديل غرض

**URL:** `PUT /api/smart-bags/{bagId}/items/{itemId}`

**مثال:** `PUT /api/smart-bags/1/items/5`

**Body:** (أرسل فقط ما تريد تغييره)
```json
{
  "name": "لابتوب ماك بوك برو M3",
  "weight": 2.5,
  "quantity": 1,
  "notes": "محدث - للعمل والتصميم"
}
```

**Response:**
```json
{
  "success": true,
  "status": 200,
  "message": "تم تحديث الغرض بنجاح",
  "data": {
    "id": 5,
    "name": "لابتوب ماك بوك برو M3",
    "weight": 2.5,
    "quantity": 1,
    "total_weight": 2.5
    /* ... */
  }
}
```

---

### 3.3 ✅ تبديل حالة التحزيم

**URL:** `POST /api/smart-bags/{bagId}/items/{itemId}/toggle-packed`

**مثال:** `POST /api/smart-bags/1/items/5/toggle-packed`

**Body:** فارغ `{}`

**Response:**
```json
{
  "success": true,
  "status": 200,
  "message": "تم تحديث حالة التحزيم",
  "data": {
    "id": 5,
    "name": "لابتوب ماك بوك برو",
    "packed": true,  // ← تم التبديل!
    /* ... */
  }
}
```

**💡 استخدامها:**
- عند الضغط على checkbox في قائمة الأغراض
- تبدل من `false` → `true` أو العكس

---

### 3.4 🗑️ حذف غرض

**URL:** `DELETE /api/smart-bags/{bagId}/items/{itemId}`

**مثال:** `DELETE /api/smart-bags/1/items/5`

**Response:**
```json
{
  "success": true,
  "status": 200,
  "message": "تم حذف الغرض بنجاح"
}
```

**💡 ملاحظة:** الوزن الكلي يتحدث تلقائياً بعد الحذف!

---

## 4. AI Analysis

### 4.1 🤖 تحليل الحقيبة بالذكاء الاصطناعي

**URL:** `POST /api/smart-bags/{bagId}/analyze`

**مثال:** `POST /api/smart-bags/1/analyze`

**Body:** (اختياري كله)
```json
{
  "preferences": ["أدوية", "مستندات مهمة"],
  "force_reanalysis": false
}
```

**الحقول:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `preferences` | array | ❌ | تفضيلات إضافية |
| `force_reanalysis` | boolean | ❌ | إعادة التحليل حتى لو تم مؤخراً |

**⚠️ شرط مهم:** الحقيبة يجب أن تحتوي على أغراض!

**Response:**
```json
{
  "success": true,
  "status": 201,
  "message": "تم تحليل الحقيبة بنجاح",
  "data": {
    "id": 1,
    "bag_id": 1,
    "missing_items": [
      {
        "item": "شاحن لابتوب",
        "category": "إلكترونيات",
        "reason": "ضروري للابتوب",
        "priority": "high"
      },
      {
        "item": "أدوية شخصية",
        "category": "أدوية وعناية",
        "reason": "مهم للرحلات الطويلة",
        "priority": "medium"
      },
      {
        "item": "جواز السفر",
        "category": "مستندات",
        "reason": "ضروري للسفر الدولي",
        "priority": "high"
      }
    ],
    "unnecessary_items": [
      {
        "item": "كتب ورقية ثقيلة",
        "reason": "يمكن استخدام نسخة إلكترونية",
        "weight_saved": 2.5
      }
    ],
    "weight_optimization": {
      "current_weight": 15.3,
      "optimal_weight": 12.8,
      "can_reduce": 2.5,
      "suggestions": [
        "استبدل الكتب الورقية بنسخ إلكترونية",
        "استخدم منتجات عناية مصغرة",
        "احزم ملابس خفيفة متعددة الاستخدام"
      ]
    },
    "smart_alerts": [
      {
        "type": "medicines_missing",
        "severity": "high",
        "message": "لا توجد أدوية في الحقيبة",
        "action": "أضف الأدوية الأساسية والإسعافات الأولية"
      },
      {
        "type": "documents_missing",
        "severity": "medium",
        "message": "لا توجد مستندات عمل",
        "action": "راجع المستندات المطلوبة للاجتماعات"
      },
      {
        "type": "weight_warning",
        "severity": "low",
        "message": "الوزن مناسب حالياً",
        "action": "لا يوجد إجراء مطلوب"
      }
    ],
    "additional_suggestions": [
      "احزم ملابس إضافية ليوم واحد احتياطاً",
      "لا تنسى شاحن محمول (Power Bank)",
      "ضع نسخة احتياطية من المستندات المهمة",
      "تأكد من جواز السفر وصلاحيته"
    ],
    "confidence_score": 0.92,
    "processing_time_ms": 1250,
    "analyzed_at": "2026-01-02T11:00:00+00:00",
    "created_at": "2026-01-02T11:00:00+00:00"
  }
}
```

**❌ أخطاء محتملة:**

**1. حقيبة فارغة:**
```json
{
  "success": false,
  "status": 422,
  "message": "لا يمكن تحليل حقيبة فارغة. الرجاء إضافة أغراض أولاً."
}
```

**2. تم التحليل مؤخراً (أقل من 24 ساعة):**
```json
{
  "success": false,
  "status": 422,
  "message": "تم تحليل الحقيبة مؤخراً. استخدم force_reanalysis=true لإعادة التحليل.",
  "last_analyzed_at": "2026-01-02T10:00:00+00:00"
}
```

**الحل:** أضف `"force_reanalysis": true` في Body.

---

### 4.2 📊 الحصول على آخر تحليل

**URL:** `GET /api/smart-bags/{bagId}/analysis/latest`

**مثال:** `GET /api/smart-bags/1/analysis/latest`

**Response:**
```json
{
  "success": true,
  "status": 200,
  "message": "تم جلب آخر تحليل بنجاح",
  "data": {
    /* نفس بنية التحليل */
  }
}
```

**💡 متى تستخدمها:**
- عند فتح صفحة التحليل
- لعرض الاقتراحات بدون إعادة التحليل

---

### 4.3 📜 الحصول على تاريخ التحليلات

**URL:** `GET /api/smart-bags/{bagId}/analysis/history`

**مثال:** `GET /api/smart-bags/1/analysis/history`

**Response:**
```json
{
  "success": true,
  "status": 200,
  "message": "تم جلب تاريخ التحليلات بنجاح",
  "data": [
    {
      "id": 3,
      "analyzed_at": "2026-01-02T11:00:00+00:00",
      "confidence_score": 0.92,
      "missing_items_count": 3
    },
    {
      "id": 2,
      "analyzed_at": "2026-01-01T22:30:00+00:00",
      "confidence_score": 0.88,
      "missing_items_count": 5
    },
    {
      "id": 1,
      "analyzed_at": "2026-01-01T20:00:00+00:00",
      "confidence_score": 0.85,
      "missing_items_count": 7
    }
  ]
}
```

**💡 استخدامها:**
- لعرض تطور التحليلات بمرور الوقت
- مقارنة التحليلات القديمة بالجديدة

---

### 4.4 🚨 الحصول على التنبيهات الذكية

**URL:** `GET /api/smart-bags/{bagId}/smart-alert`

**مثال:** `GET /api/smart-bags/1/smart-alert`

**Response:**
```json
{
  "success": true,
  "status": 200,
  "message": "تم جلب التنبيهات بنجاح",
  "data": {
    "bag_id": 1,
    "alerts": [
      {
        "type": "medicines_missing",
        "severity": "high",
        "title": "أدوية ناقصة",
        "message": "لا توجد أدوية في الحقيبة",
        "action": "أضف الأدوية الأساسية والإسعافات الأولية",
        "icon": "medical",
        "color": "#EF4444"
      },
      {
        "type": "documents_missing",
        "severity": "medium",
        "title": "مستندات ناقصة",
        "message": "لا توجد مستندات عمل",
        "action": "راجع المستندات المطلوبة للاجتماعات",
        "icon": "document",
        "color": "#F59E0B"
      },
      {
        "type": "overweight_warning",
        "severity": "medium",
        "title": "تحذير وزن",
        "message": "الوزن قريب من الحد الأقصى (85%)",
        "action": "راجع الأغراض وحاول تقليل الوزن",
        "icon": "weight",
        "color": "#F97316"
      },
      {
        "type": "departure_soon",
        "severity": "high",
        "title": "السفر قريب",
        "message": "باقي 3 أيام فقط على موعد السفر",
        "action": "تأكد من جاهزية جميع الأغراض",
        "icon": "calendar",
        "color": "#DC2626"
      }
    ],
    "total_alerts": 4,
    "high_priority": 2,
    "medium_priority": 2,
    "low_priority": 0
  }
}
```

**💡 استخدامها:**
- عرض تنبيهات في الصفحة الرئيسية
- إشعارات push
- Badge على أيقونة الحقيبة

---

## 📊 ملخص جميع الـ Endpoints

| # | الوظيفة | Method | URL | Token؟ |
|---|---------|--------|-----|--------|
| **Item Categories** |
| 1 | جلب الفئات | GET | `/api/item-categories` | ❌ |
| 2 | فئة واحدة | GET | `/api/item-categories/{id}` | ❌ |
| **Bags CRUD** |
| 3 | جلب كل الحقائب | GET | `/api/smart-bags` | ✅ |
| 4 | جلب حقيبة واحدة | GET | `/api/smart-bags/{id}` | ✅ |
| 5 | إنشاء حقيبة | POST | `/api/smart-bags` | ✅ |
| 6 | تعديل حقيبة | PUT | `/api/smart-bags/{id}` | ✅ |
| 7 | حذف حقيبة | DELETE | `/api/smart-bags/{id}` | ✅ |
| **Items Management** |
| 8 | إضافة غرض | POST | `/api/smart-bags/{bagId}/items` | ✅ |
| 9 | تعديل غرض | PUT | `/api/smart-bags/{bagId}/items/{itemId}` | ✅ |
| 10 | تبديل التحزيم | POST | `/api/smart-bags/{bagId}/items/{itemId}/toggle-packed` | ✅ |
| 11 | حذف غرض | DELETE | `/api/smart-bags/{bagId}/items/{itemId}` | ✅ |
| **AI Analysis** |
| 12 | تحليل حقيبة | POST | `/api/smart-bags/{bagId}/analyze` | ✅ |
| 13 | آخر تحليل | GET | `/api/smart-bags/{bagId}/analysis/latest` | ✅ |
| 14 | تاريخ التحليلات | GET | `/api/smart-bags/{bagId}/analysis/history` | ✅ |
| 15 | التنبيهات الذكية | GET | `/api/smart-bags/{bagId}/smart-alert` | ✅ |

**المجموع: 15 Endpoint**

---

## 🎨 القيم الثابتة (Enums)

### نوع الرحلة (trip_type)

| القيمة بالعربي | القيمة بالإنجليزي (مع en) |
|----------------|---------------------------|
| `عمل` | `Business` |
| `سياحة` | `Tourism` |
| `عائلية` | `Family` |
| `علاج` | `Medical` |

**في الكود:**
```javascript
const tripTypes = ['عمل', 'سياحة', 'عائلية', 'علاج'];
```

---

### حالة الحقيبة (status)

| القيمة في DB | بالعربي | بالإنجليزي |
|--------------|---------|------------|
| `draft` | `مسودة` | `Draft` |
| `in_progress` | `قيد التجهيز` | `In Progress` |
| `completed` | `مكتملة` | `Completed` |
| `cancelled` | `ملغاة` | `Cancelled` |

---

### مستوى الأولوية (priority)

| Value | العربي | اللون |
|-------|--------|-------|
| `high` | عالية | 🔴 Red |
| `medium` | متوسطة | 🟠 Orange |
| `low` | منخفضة | 🟢 Green |

---

## ⚠️ أخطاء شائعة وحلولها

### 1. 401 Unauthenticated
```json
{
  "status": 401,
  "message": "Unauthenticated"
}
```
**السبب:** Token غير موجود أو منتهي الصلاحية  
**الحل:** تأكد من إرسال الـ Token في Header

---

### 2. 422 Validation Error
```json
{
  "success": false,
  "status": 422,
  "message": "اسم الحقيبة مطلوب"
}
```
**السبب:** حقول مطلوبة ناقصة  
**الحل:** تأكد من إرسال جميع الحقول المطلوبة

---

### 3. 404 Not Found
```json
{
  "status": 404,
  "message": "Bag not found"
}
```
**السبب:** الـ ID غير موجود  
**الحل:** تأكد من الـ ID صحيح

---

### 4. 500 Server Error
```json
{
  "success": false,
  "status": 500,
  "message": "خطأ في السيرفر"
}
```
**السبب:** خطأ غير متوقع  
**الحل:** راجع console أو اتصل بالباك إند

---

## 💡 نصائح للفرونت إند ديفيلوبر

### 1. حفظ الـ Token
```javascript
// بعد Login
localStorage.setItem('authToken', response.data.token);

// عند كل request
headers: {
  'Authorization': `Bearer ${localStorage.getItem('authToken')}`,
  'Accept-Language': 'ar',
  'Content-Type': 'application/json'
}
```

---

### 2. جلب الفئات مرة واحدة
```javascript
// عند تحميل التطبيق
useEffect(() => {
  fetchCategories().then(data => {
    setCategories(data);
  });
}, []);
```

---

### 3. تحديث الوزن تلقائياً
بعد إضافة/حذف/تعديل غرض، اجلب الحقيبة من جديد:
```javascript
const addItem = async (itemData) => {
  await api.post(`/smart-bags/${bagId}/items`, itemData);
  // جلب الحقيبة من جديد
  await fetchBag(bagId);
};
```

---

### 4. عرض النسبة المئوية للوزن
```javascript
const weightPercentage = (bag.total_weight / bag.max_weight) * 100;

// تحديد اللون حسب النسبة
const getWeightColor = (percentage) => {
  if (percentage >= 90) return 'red';
  if (percentage >= 75) return 'orange';
  return 'green';
};
```

---

### 5. التعامل مع التواريخ
```javascript
// حساب الأيام المتبقية
const daysUntil = Math.ceil(
  (new Date(bag.departure_date) - new Date()) / (1000 * 60 * 60 * 24)
);

// عرض تنبيه إذا قريب
if (daysUntil <= 7) {
  showUrgentAlert();
}
```

---

### 6. Loading States
```javascript
const [loading, setLoading] = useState(false);

const analyzeBag = async () => {
  setLoading(true);
  try {
    const response = await api.post(`/smart-bags/${bagId}/analyze`);
    // عرض النتائج
  } finally {
    setLoading(false);
  }
};
```

---

## 🎯 أمثلة كود جاهزة

### React Example

```javascript
import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost:8000/api',
  headers: {
    'Accept-Language': 'ar',
    'Content-Type': 'application/json'
  }
});

// إضافة Token لكل request
api.interceptors.request.use(config => {
  const token = localStorage.getItem('authToken');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// جلب الحقائب
const fetchBags = async () => {
  const response = await api.get('/smart-bags');
  return response.data.data;
};

// إضافة غرض
const addItem = async (bagId, itemData) => {
  const response = await api.post(`/smart-bags/${bagId}/items`, itemData);
  return response.data.data;
};

// تحليل حقيبة
const analyzeBag = async (bagId) => {
  const response = await api.post(`/smart-bags/${bagId}/analyze`, {});
  return response.data.data;
};
```

---

## 🎉 كل شيء جاهز!

**هذا الملف يحتوي على كل ما تحتاجه للتعامل مع Smart Bags API!**

### 📋 المحتوى:
- ✅ 15 Endpoint كامل
- ✅ كل URL و Method و Body و Response
- ✅ أمثلة حقيقية
- ✅ أخطاء وحلولها
- ✅ نصائح عملية
- ✅ كود جاهز للاستخدام

---

**للأسئلة أو المشاكل، راجع:**
- Postman Collection: `Smart_Bags_API_Collection.postman_collection.json`
- دليل الاختبار: `TEST_BAG_ANALYSIS_AR.md`

---

**آخر تحديث:** يناير 2026  
**الإصدار:** 3.0 - Smart Bags Only (Complete)

