# 📱 دليل الفرونت إند - Smart Bags API

## 🎯 نظرة عامة

Base URL: `http://localhost:8000/api`

**جميع الطلبات تحتاج:**
```
Authorization: Bearer YOUR_TOKEN
Accept-Language: ar (للعربية) أو en (للإنجليزية)
```

---

## 📚 1. فئات الأغراض (Item Categories)

### 1.1 الحصول على جميع الفئات

**URL:** `GET /api/item-categories`

**لا يحتاج Token!** ✨

**Response:**
```json
{
  "success": true,
  "message": "تم جلب الفئات بنجاح",
  "data": [
    {
      "id": 1,
      "name": "ملابس",
      "icon": "shirt",
      "icon_color": "#3B82F6"
    },
    {
      "id": 2,
      "name": "أحذية",
      "icon": "shoe",
      "icon_color": "#8B5CF6"
    },
    {
      "id": 3,
      "name": "إلكترونيات",
      "icon": "laptop",
      "icon_color": "#10B981"
    }
  ]
}
```

**استخدمها:** لعرض قائمة الفئات عند إضافة غرض جديد.

---

## 📦 2. إدارة الحقائب (Bags Management)

### 2.1 الحصول على جميع الحقائب

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
      "items_count": 2,
      "items": [
        {
          "id": 1,
          "name": "لابتوب",
          "weight": 2.3,
          "quantity": 1,
          "total_weight": 2.3,
          "category": {
            "id": 3,
            "name": "إلكترونيات",
            "icon": "laptop"
          },
          "essential": true,
          "packed": false
        }
      ],
      "created_at": "2026-01-01T21:00:00+00:00"
    }
  ]
}
```

---

### 2.2 الحصول على حقيبة واحدة

**URL:** `GET /api/smart-bags/{bagId}`

مثال: `GET /api/smart-bags/1`

**Response:** نفس بنية الحقيبة في القائمة أعلاه، لكن حقيبة واحدة فقط.

---

### 2.3 إنشاء حقيبة جديدة

**URL:** `POST /api/smart-bags`

**Body:**
```json
{
  "name": "حقيبة رحلة القاهرة",
  "trip_type": "سياحة",
  "duration": 5,
  "destination": "القاهرة",
  "departure_date": "2026-02-15",
  "max_weight": 23
}
```

**الحقول:**
- `name` (مطلوب): اسم الحقيبة
- `trip_type` (مطلوب): نوع الرحلة (عمل، سياحة، عائلية، علاج)
- `duration` (مطلوب): مدة الرحلة بالأيام (رقم)
- `destination` (مطلوب): الوجهة
- `departure_date` (مطلوب): تاريخ المغادرة (YYYY-MM-DD)
- `max_weight` (مطلوب): الوزن الأقصى بالكيلو (رقم)

**Response:**
```json
{
  "success": true,
  "message": "تم إنشاء الحقيبة بنجاح",
  "data": {
    "id": 2,
    "name": "حقيبة رحلة القاهرة",
    "trip_type": "سياحة",
    "status": "مسودة",
    "total_weight": 0,
    "items_count": 0
  }
}
```

---

### 2.4 تعديل حقيبة

**URL:** `PUT /api/smart-bags/{bagId}`

مثال: `PUT /api/smart-bags/1`

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
  "message": "تم تحديث الحقيبة بنجاح",
  "data": { /* الحقيبة المحدثة */ }
}
```

---

### 2.5 حذف حقيبة

**URL:** `DELETE /api/smart-bags/{bagId}`

مثال: `DELETE /api/smart-bags/1`

**Response:**
```json
{
  "success": true,
  "message": "تم حذف الحقيبة بنجاح"
}
```

---

## 📝 3. إدارة الأغراض (Items Management)

### 3.1 إضافة غرض للحقيبة

**URL:** `POST /api/smart-bags/{bagId}/items`

مثال: `POST /api/smart-bags/1/items`

**Body:**
```json
{
  "name": "لابتوب ماك بوك",
  "weight": 2.3,
  "item_category_id": 3,
  "quantity": 1,
  "essential": true,
  "notes": "للعمل"
}
```

**الحقول:**
- `name` (مطلوب): اسم الغرض
- `weight` (مطلوب): الوزن بالكيلو (رقم)
- `item_category_id` (مطلوب): رقم الفئة (من endpoint الفئات)
- `quantity` (اختياري): الكمية (افتراضي: 1)
- `essential` (اختياري): ضروري؟ (true/false)
- `packed` (اختياري): محزوم؟ (true/false)
- `notes` (اختياري): ملاحظات

**Response:**
```json
{
  "success": true,
  "message": "تم إضافة الغرض بنجاح",
  "data": {
    "id": 5,
    "name": "لابتوب ماك بوك",
    "weight": 2.3,
    "quantity": 1,
    "total_weight": 2.3,
    "item_category_id": 3,
    "category": {
      "id": 3,
      "name": "إلكترونيات",
      "icon": "laptop"
    },
    "essential": true,
    "packed": false,
    "notes": "للعمل"
  }
}
```

---

### 3.2 تعديل غرض

**URL:** `PUT /api/smart-bags/{bagId}/items/{itemId}`

مثال: `PUT /api/smart-bags/1/items/5`

**Body:** (أرسل فقط ما تريد تغييره)
```json
{
  "name": "لابتوب ماك بوك برو",
  "weight": 2.5,
  "quantity": 1
}
```

**Response:**
```json
{
  "success": true,
  "message": "تم تحديث الغرض بنجاح",
  "data": { /* الغرض المحدث */ }
}
```

---

### 3.3 تبديل حالة التحزيم

**URL:** `POST /api/smart-bags/{bagId}/items/{itemId}/toggle-packed`

مثال: `POST /api/smart-bags/1/items/5/toggle-packed`

**Body:** فارغ `{}`

**Response:**
```json
{
  "success": true,
  "message": "تم تحديث حالة التحزيم",
  "data": {
    "id": 5,
    "name": "لابتوب",
    "packed": true  ← تم التحزيم!
  }
}
```

---

### 3.4 حذف غرض

**URL:** `DELETE /api/smart-bags/{bagId}/items/{itemId}`

مثال: `DELETE /api/smart-bags/1/items/5`

**Response:**
```json
{
  "success": true,
  "message": "تم حذف الغرض بنجاح"
}
```

---

## 🤖 4. التحليل الذكي (AI Analysis)

### 4.1 تحليل الحقيبة

**URL:** `POST /api/smart-bags/{bagId}/analyze`

مثال: `POST /api/smart-bags/1/analyze`

**Body:** (اختياري كله)
```json
{
  "preferences": ["أدوية", "مستندات"],
  "force_reanalysis": false
}
```

**ملاحظة:** الحقيبة يجب أن تحتوي على أغراض!

**Response:**
```json
{
  "success": true,
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
        "reason": "مهم للرحلات",
        "priority": "medium"
      }
    ],
    "unnecessary_items": [
      {
        "item": "كتب ثقيلة",
        "reason": "يمكن استخدام نسخة إلكترونية",
        "weight_saved": 2.5
      }
    ],
    "weight_optimization": {
      "current_weight": 15.3,
      "optimal_weight": 12.8,
      "can_reduce": 2.5,
      "suggestions": [
        "استبدل الكتب بنسخ إلكترونية",
        "استخدم منتجات مصغرة للعناية"
      ]
    },
    "smart_alerts": [
      {
        "type": "medicines_missing",
        "severity": "high",
        "message": "لا توجد أدوية في الحقيبة",
        "action": "أضف الأدوية الأساسية"
      }
    ],
    "additional_suggestions": [
      "احزم ملابس إضافية ليوم واحد",
      "لا تنسى شاحن محمول"
    ],
    "confidence_score": 0.92,
    "analyzed_at": "2026-01-01T22:30:45+00:00"
  }
}
```

---

### 4.2 الحصول على آخر تحليل

**URL:** `GET /api/smart-bags/{bagId}/analysis/latest`

مثال: `GET /api/smart-bags/1/analysis/latest`

**Response:** نفس بنية التحليل أعلاه.

---

### 4.3 الحصول على تاريخ التحليلات

**URL:** `GET /api/smart-bags/{bagId}/analysis/history`

مثال: `GET /api/smart-bags/1/analysis/history`

**Response:**
```json
{
  "success": true,
  "message": "تم جلب تاريخ التحليلات",
  "data": [
    { /* تحليل 1 */ },
    { /* تحليل 2 */ },
    { /* تحليل 3 */ }
  ]
}
```

---

### 4.4 الحصول على التنبيهات الذكية

**URL:** `GET /api/smart-bags/{bagId}/smart-alert`

مثال: `GET /api/smart-bags/1/smart-alert`

**Response:**
```json
{
  "success": true,
  "data": {
    "alerts": [
      {
        "type": "medicines_missing",
        "severity": "high",
        "message": "لا توجد أدوية في الحقيبة",
        "action": "أضف الأدوية الأساسية"
      },
      {
        "type": "overweight",
        "severity": "medium",
        "message": "الوزن قريب من الحد الأقصى",
        "action": "راجع الأغراض وقلل الوزن"
      }
    ]
  }
}
```

---

## 📋 ملخص سريع لكل الـ Endpoints

| الوظيفة | Method | URL | يحتاج Token؟ |
|---------|--------|-----|-------------|
| **الفئات** |
| جلب الفئات | GET | `/api/item-categories` | ❌ لا |
| **الحقائب** |
| جلب كل الحقائب | GET | `/api/smart-bags` | ✅ نعم |
| جلب حقيبة واحدة | GET | `/api/smart-bags/{id}` | ✅ نعم |
| إنشاء حقيبة | POST | `/api/smart-bags` | ✅ نعم |
| تعديل حقيبة | PUT | `/api/smart-bags/{id}` | ✅ نعم |
| حذف حقيبة | DELETE | `/api/smart-bags/{id}` | ✅ نعم |
| **الأغراض** |
| إضافة غرض | POST | `/api/smart-bags/{bagId}/items` | ✅ نعم |
| تعديل غرض | PUT | `/api/smart-bags/{bagId}/items/{itemId}` | ✅ نعم |
| تبديل التحزيم | POST | `/api/smart-bags/{bagId}/items/{itemId}/toggle-packed` | ✅ نعم |
| حذف غرض | DELETE | `/api/smart-bags/{bagId}/items/{itemId}` | ✅ نعم |
| **التحليل الذكي** |
| تحليل حقيبة | POST | `/api/smart-bags/{bagId}/analyze` | ✅ نعم |
| آخر تحليل | GET | `/api/smart-bags/{bagId}/analysis/latest` | ✅ نعم |
| تاريخ التحليلات | GET | `/api/smart-bags/{bagId}/analysis/history` | ✅ نعم |
| التنبيهات الذكية | GET | `/api/smart-bags/{bagId}/smart-alert` | ✅ نعم |

---

## 🎨 أنواع الرحلات المتاحة

للاستخدام في `trip_type`:
- `عمل` → Business
- `سياحة` → Tourism
- `عائلية` → Family
- `علاج` → Medical

---

## 🎯 حالات الحقيبة (Status)

للاستخدام في `status`:
- `draft` → مسودة
- `in_progress` → قيد التجهيز
- `completed` → مكتملة
- `cancelled` → ملغاة

---

## ⚠️ أخطاء شائعة

### 1. 401 Unauthenticated
```json
{
  "status": 401,
  "message": "Unauthenticated"
}
```
**الحل:** تأكد من إرسال الـ Token في Header.

### 2. 422 Validation Error
```json
{
  "success": false,
  "message": "اسم الحقيبة مطلوب"
}
```
**الحل:** تأكد من إرسال جميع الحقول المطلوبة.

### 3. 404 Not Found
```json
{
  "status": 404,
  "message": "Bag not found"
}
```
**الحل:** تأكد من الـ ID صحيح.

---

## 💡 نصائح للفرونت إند

### 1. حفظ الـ Token
بعد Login، احفظ الـ Token في localStorage أو sessionStorage:
```
localStorage.setItem('token', response.data.token);
```

### 2. إرسال الـ Token مع كل طلب
```
headers: {
  'Authorization': 'Bearer ' + token,
  'Accept-Language': 'ar',
  'Content-Type': 'application/json'
}
```

### 3. جلب الفئات مرة واحدة
اجلب الفئات مرة واحدة عند تشغيل التطبيق واحفظها في state.

### 4. تحديث الحقيبة تلقائياً
بعد إضافة/حذف غرض، اجلب الحقيبة من جديد لعرض الوزن المحدث.

### 5. عرض التنبيهات
إذا `days_until_departure` أقل من 7، اعرض تنبيه للمستخدم.

---

## 🎉 جاهز للاستخدام!

كل ما تحتاجه موجود في هذا الملف! 

**للتجربة:** استخدم Postman Collection الموجود في:
- `Smart_Bags_API_Collection.postman_collection.json`
- `Smart_Bags_API_Environment.postman_environment.json`

---

## 📦 5. نظام الشنطة القديم (Travel Bag)

### 5.1 أنواع الحقائب (Bag Types)

**URL:** `GET /api/bag-types`

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "حقيبة يد",
      "description": "حقيبة صغيرة للأغراض الشخصية",
      "max_weight": 7,
      "icon": "handbag"
    },
    {
      "id": 2,
      "name": "حقيبة كابينة",
      "description": "حقيبة الكابينة",
      "max_weight": 10,
      "icon": "cabin-bag"
    }
  ]
}
```

---

### 5.2 تفاصيل الشنطة

**URL:** `GET /api/travel-bag/details`

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "user_id": 1,
    "bag_type_id": 2,
    "max_weight": 20,
    "total_weight": 12.5,
    "remaining_weight": 7.5,
    "weight_percentage": 62.5,
    "travel_date": "2026-02-15",
    "is_ready": false,
    "items": [
      {
        "id": 1,
        "name": "قميص",
        "weight": 0.3,
        "quantity": 5,
        "category": "ملابس"
      }
    ]
  }
}
```

---

### 5.3 تحديث الوزن الأقصى

**URL:** `PUT /api/travel-bag/max-weight`

**Body:**
```json
{
  "max_weight": 25
}
```

**Response:**
```json
{
  "success": true,
  "message": "تم تحديث الوزن الأقصى بنجاح",
  "data": {
    "max_weight": 25,
    "total_weight": 12.5,
    "remaining_weight": 12.5
  }
}
```

---

### 5.4 إضافة غرض للشنطة

**URL:** `POST /api/travel-bag/add-item`

**Body:**
```json
{
  "item_id": 5,
  "quantity": 2,
  "custom_weight": 0.5
}
```

**Response:**
```json
{
  "success": true,
  "message": "تم إضافة الغرض بنجاح",
  "data": {
    "id": 10,
    "item_id": 5,
    "quantity": 2,
    "weight": 0.5
  }
}
```

---

### 5.5 الحصول على أغراض الشنطة

**URL:** `GET /api/travel-bag/items`

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "item_id": 5,
      "name": "قميص",
      "quantity": 5,
      "weight_per_item": 0.3,
      "total_weight": 1.5,
      "category": "ملابس"
    }
  ]
}
```

---

### 5.6 تحديث كمية غرض

**URL:** `PUT /api/travel-bag/items/{itemId}/quantity`

مثال: `PUT /api/travel-bag/items/1/quantity`

**Body:**
```json
{
  "quantity": 3
}
```

**Response:**
```json
{
  "success": true,
  "message": "تم تحديث الكمية بنجاح",
  "data": {
    "quantity": 3,
    "total_weight": 0.9
  }
}
```

---

### 5.7 حذف غرض من الشنطة

**URL:** `DELETE /api/travel-bag/items/{itemId}`

مثال: `DELETE /api/travel-bag/items/1`

**Response:**
```json
{
  "success": true,
  "message": "تم حذف الغرض بنجاح"
}
```

---

### 5.8 تعيين تاريخ السفر

**URL:** `POST /api/travel-bag/travel-date`

**Body:**
```json
{
  "travel_date": "2026-02-15"
}
```

**Response:**
```json
{
  "success": true,
  "message": "تم تعيين تاريخ السفر بنجاح",
  "data": {
    "travel_date": "2026-02-15",
    "days_remaining": 45
  }
}
```

---

### 5.9 الحصول على تذكير الشنطة

**URL:** `GET /api/travel-bag/reminder`

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "travel_date": "2026-02-15",
    "is_ready": false,
    "reminder_enabled": true,
    "days_until_travel": 45
  }
}
```

---

### 5.10 تعيين تذكير

**URL:** `POST /api/travel-bag/reminder`

**Body:**
```json
{
  "travel_date": "2026-02-15",
  "reminder_enabled": true
}
```

**Response:**
```json
{
  "success": true,
  "message": "تم تعيين التذكير بنجاح"
}
```

---

## 📝 6. الأغراض العامة (Items)

### 6.1 فئات الأغراض (للنظام القديم)

**URL:** `GET /api/items/categories`

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "ملابس",
      "icon": "shirt"
    },
    {
      "id": 2,
      "name": "أحذية",
      "icon": "shoe"
    }
  ]
}
```

---

### 6.2 الحصول على الأغراض حسب الفئة

**URL:** `GET /api/items?category_id={categoryId}`

مثال: `GET /api/items?category_id=1`

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "قميص",
      "name_en": "Shirt",
      "default_weight": 0.3,
      "category_id": 1,
      "category_name": "ملابس"
    },
    {
      "id": 2,
      "name": "بنطلون",
      "name_en": "Pants",
      "default_weight": 0.5,
      "category_id": 1,
      "category_name": "ملابس"
    }
  ]
}
```

---

## ⏰ 7. التذكيرات (Reminders)

### 7.1 الحصول على جميع التذكيرات

**URL:** `GET /api/reminders`

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "شراء تذاكر الطيران",
      "description": "حجز تذاكر الطيران لرحلة دبي",
      "reminder_date": "2026-01-20T10:00:00+00:00",
      "status": "pending",
      "priority": "high",
      "is_completed": false
    }
  ]
}
```

---

### 7.2 إنشاء تذكير جديد

**URL:** `POST /api/reminders`

**Body:**
```json
{
  "title": "شراء تذاكر الطيران",
  "description": "حجز تذاكر الطيران لرحلة دبي",
  "reminder_date": "2026-01-20 10:00:00",
  "priority": "high"
}
```

**الحقول:**
- `title` (مطلوب): عنوان التذكير
- `description` (اختياري): وصف التذكير
- `reminder_date` (مطلوب): تاريخ ووقت التذكير
- `priority` (اختياري): الأولوية (low, medium, high)

**Response:**
```json
{
  "success": true,
  "message": "تم إنشاء التذكير بنجاح",
  "data": {
    "id": 5,
    "title": "شراء تذاكر الطيران",
    "reminder_date": "2026-01-20T10:00:00+00:00",
    "status": "pending"
  }
}
```

---

### 7.3 الحصول على تذكير واحد

**URL:** `GET /api/reminders/{id}`

مثال: `GET /api/reminders/1`

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "شراء تذاكر الطيران",
    "description": "حجز تذاكر الطيران لرحلة دبي",
    "reminder_date": "2026-01-20T10:00:00+00:00",
    "priority": "high",
    "is_completed": false
  }
}
```

---

### 7.4 تحديث تذكير

**URL:** `PUT /api/reminders/{id}`

مثال: `PUT /api/reminders/1`

**Body:**
```json
{
  "title": "شراء تذاكر الطيران - محدث",
  "is_completed": true
}
```

**Response:**
```json
{
  "success": true,
  "message": "تم تحديث التذكير بنجاح",
  "data": { /* التذكير المحدث */ }
}
```

---

### 7.5 حذف تذكير

**URL:** `DELETE /api/reminders/{id}`

مثال: `DELETE /api/reminders/1`

**Response:**
```json
{
  "success": true,
  "message": "تم حذف التذكير بنجاح"
}
```

---

## 💡 8. نصائح التعبئة (Packing Tips)

### 8.1 الحصول على نصائح التعبئة

**URL:** `GET /api/packing-tips`

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "لف الملابس بدلاً من طيها",
      "description": "لف الملابس يوفر مساحة أكبر ويقلل التجاعيد",
      "category": "ملابس",
      "icon": "lightbulb"
    },
    {
      "id": 2,
      "title": "استخدم أكياس ضغط الهواء",
      "description": "أكياس ضغط الهواء توفر حتى 50% من المساحة",
      "category": "general",
      "icon": "compress"
    }
  ]
}
```

---

## 🤖 9. اقتراحات الذكاء الاصطناعي (AI Suggestions)

### 9.1 الحصول على اقتراحات

**URL:** `GET /api/ai/suggestions`

**Response:**
```json
{
  "success": true,
  "data": {
    "suggested_items": [
      {
        "item_id": 15,
        "name": "شاحن محمول",
        "reason": "مهم للرحلات الطويلة",
        "category": "إلكترونيات",
        "default_weight": 0.3,
        "priority": "high"
      },
      {
        "item_id": 28,
        "name": "أدوية شخصية",
        "reason": "ضروري للطوارئ",
        "category": "أدوية وعناية",
        "default_weight": 0.2,
        "priority": "high"
      }
    ],
    "tips": [
      "لا تنسى جواز السفر",
      "احزم ملابس إضافية ليوم واحد"
    ]
  }
}
```

---

### 9.2 إضافة غرض مقترح

**URL:** `POST /api/ai/suggestions/add-item`

**Body:**
```json
{
  "item_id": 15,
  "quantity": 1
}
```

**Response:**
```json
{
  "success": true,
  "message": "تم إضافة الغرض بنجاح",
  "data": {
    "id": 20,
    "item_id": 15,
    "name": "شاحن محمول",
    "quantity": 1
  }
}
```

---

## 📋 ملخص كامل لكل الـ Endpoints

### 🔐 بدون Token (Public)

| الوظيفة | Method | URL |
|---------|--------|-----|
| جلب فئات الأغراض (Smart Bags) | GET | `/api/item-categories` |

### ✅ يحتاج Token

#### Smart Bags (الحقائب الذكية)

| الوظيفة | Method | URL |
|---------|--------|-----|
| جلب كل الحقائب | GET | `/api/smart-bags` |
| جلب حقيبة واحدة | GET | `/api/smart-bags/{id}` |
| إنشاء حقيبة | POST | `/api/smart-bags` |
| تعديل حقيبة | PUT | `/api/smart-bags/{id}` |
| حذف حقيبة | DELETE | `/api/smart-bags/{id}` |
| إضافة غرض | POST | `/api/smart-bags/{bagId}/items` |
| تعديل غرض | PUT | `/api/smart-bags/{bagId}/items/{itemId}` |
| تبديل التحزيم | POST | `/api/smart-bags/{bagId}/items/{itemId}/toggle-packed` |
| حذف غرض | DELETE | `/api/smart-bags/{bagId}/items/{itemId}` |
| تحليل حقيبة | POST | `/api/smart-bags/{bagId}/analyze` |
| آخر تحليل | GET | `/api/smart-bags/{bagId}/analysis/latest` |
| تاريخ التحليلات | GET | `/api/smart-bags/{bagId}/analysis/history` |
| التنبيهات الذكية | GET | `/api/smart-bags/{bagId}/smart-alert` |

#### Travel Bag (الشنطة القديمة)

| الوظيفة | Method | URL |
|---------|--------|-----|
| جلب أنواع الحقائب | GET | `/api/bag-types` |
| تفاصيل الشنطة | GET | `/api/travel-bag/details` |
| تحديث الوزن الأقصى | PUT | `/api/travel-bag/max-weight` |
| إضافة غرض | POST | `/api/travel-bag/add-item` |
| جلب الأغراض | GET | `/api/travel-bag/items` |
| تحديث كمية غرض | PUT | `/api/travel-bag/items/{id}/quantity` |
| حذف غرض | DELETE | `/api/travel-bag/items/{id}` |
| تعيين تاريخ السفر | POST | `/api/travel-bag/travel-date` |
| جلب التذكير | GET | `/api/travel-bag/reminder` |
| تعيين تذكير | POST | `/api/travel-bag/reminder` |

#### الأغراض العامة

| الوظيفة | Method | URL |
|---------|--------|-----|
| فئات الأغراض | GET | `/api/items/categories` |
| جلب الأغراض | GET | `/api/items?category_id={id}` |

#### التذكيرات

| الوظيفة | Method | URL |
|---------|--------|-----|
| جلب التذكيرات | GET | `/api/reminders` |
| إنشاء تذكير | POST | `/api/reminders` |
| جلب تذكير واحد | GET | `/api/reminders/{id}` |
| تعديل تذكير | PUT | `/api/reminders/{id}` |
| حذف تذكير | DELETE | `/api/reminders/{id}` |

#### نصائح التعبئة

| الوظيفة | Method | URL |
|---------|--------|-----|
| جلب النصائح | GET | `/api/packing-tips` |

#### اقتراحات الذكاء الاصطناعي

| الوظيفة | Method | URL |
|---------|--------|-----|
| جلب الاقتراحات | GET | `/api/ai/suggestions` |
| إضافة غرض مقترح | POST | `/api/ai/suggestions/add-item` |

---

## 🎯 الفرق بين Smart Bags و Travel Bag

### 🆕 Smart Bags (الجديد - مع AI)
- ✅ حقائب متعددة
- ✅ تحليل ذكي بالـ AI
- ✅ اقتراحات تلقائية
- ✅ تنبيهات ذكية
- ✅ يستخدم `item_category_id`

**استخدمه للميزات الجديدة!**

### 📦 Travel Bag (القديم)
- حقيبة واحدة لكل مستخدم
- بدون تحليل AI
- يستخدم `item_id` من جدول الأغراض الثابتة
- نظام أبسط

---

## 💡 نصائح إضافية للفرونت إند

### 1. اختيار النظام المناسب
- للتطبيق الجديد: استخدم **Smart Bags** ✅
- للتطبيق القديم: استخدم **Travel Bag**

### 2. عرض الأغراض
```javascript
// Smart Bags - اسم مباشر
item.name  // "لابتوب ماك بوك"

// Travel Bag - اسم من جدول Items
item.name  // "قميص"
item.name_en  // "Shirt"
```

### 3. التحزيم (Packing)
**Smart Bags فقط** - له toggle packed endpoint.

### 4. التذكيرات
- **Reminders**: تذكيرات عامة (مهام، مواعيد)
- **Travel Bag Reminder**: تذكير خاص بالشنطة

### 5. الفئات
- **Smart Bags**: استخدم `/api/item-categories`
- **Travel Bag**: استخدم `/api/items/categories`

---

## 🎉 كل شيء جاهز!

الآن عندك **44 endpoint** كاملين! 🚀

### للاستخدام:
1. ✅ اختر Smart Bags للتطبيق الجديد
2. ✅ استخدم Travel Bag إذا كنت تحافظ على التوافق مع القديم
3. ✅ أضف Reminders لإدارة المهام
4. ✅ استخدم AI Analysis للذكاء الاصطناعي

---

**آخر تحديث:** يناير 2026  
**الإصدار:** 2.5 - Complete Guide

