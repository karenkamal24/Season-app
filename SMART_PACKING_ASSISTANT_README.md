# 🎒 Smart Packing Assistant - Complete Documentation

## 📋 Overview

Smart Packing Assistant هو نظام متكامل لتحليل محتويات حقائب السفر باستخدام الذكاء الاصطناعي (Gemini AI) وتقديم اقتراحات ذكية للمستخدمين.

## ✨ Features

- ✅ **CRUD Operations للحقائب** - إنشاء/تعديل/حذف حقائب السفر
- ✅ **إدارة الأغراض** - إضافة/تعديل/حذف الأغراض في الحقيبة
- ✅ **تحليل ذكي بالـ AI** - استخدام Gemini AI لتحليل محتويات الحقيبة
- ✅ **تنبيهات ذكية** - إرسال تنبيهات للمستخدم قبل موعد الرحلة
- ✅ **دعم لغتين** - العربية والإنجليزية
- ✅ **Admin Panel** - لوحة تحكم Filament كاملة

---

## 🚀 Installation & Setup

### 1. Environment Configuration

أضف الـ Gemini API Key في ملف `.env`:

```env
GEMINI_API_KEY=your_gemini_api_key_here
GEMINI_MODEL=gemini-2.0-flash-exp
```

### 2. Run Migrations

```bash
php artisan migrate
```

هذا سينشئ الجداول التالية:
- `bags` - جدول الحقائب
- `bag_items` - جدول أغراض الحقيبة
- `bag_analyses` - جدول تحليلات الـ AI

### 3. Test the Scheduled Tasks

لاختبار Smart Alerts System:

```bash
php artisan bags:send-alerts --hours=24
```

### 4. Setup Cron Job (Production)

أضف في crontab:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

---

## 📡 API Endpoints

### Base URL: `/api/smart-bags`

جميع الـ endpoints تحتاج authentication (`Bearer Token`)

### 1. Get All Bags

```http
GET /api/smart-bags
```

**Query Parameters:**
- `status` - filter by status (draft, in_progress, completed, cancelled)
- `trip_type` - filter by trip type (عمل، سياحة، عائلية، علاج)
- `upcoming` - boolean (filter upcoming trips)
- `sort_by` - field to sort by (default: departure_date)
- `sort_order` - asc/desc (default: asc)
- `per_page` - items per page (default: 15)

**Response:**
```json
{
  "success": true,
  "message": "Bags retrieved successfully",
  "message_ar": "تم جلب الحقائب بنجاح",
  "data": [...],
  "pagination": {
    "total": 50,
    "per_page": 15,
    "current_page": 1,
    "last_page": 4
  }
}
```

---

### 2. Create Bag

```http
POST /api/smart-bags
```

**Request Body:**
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
      "quantity": 2
    }
  ]
}
```

**Response:**
```json
{
  "success": true,
  "message": "Bag created successfully",
  "message_ar": "تم إنشاء الحقيبة بنجاح",
  "data": { ... }
}
```

---

### 3. Get Bag Details

```http
GET /api/smart-bags/{bagId}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "حقيبة رحلة دبي",
    "trip_type": "عمل",
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
    "items": [...],
    "latest_analysis": { ... }
  }
}
```

---

### 4. Update Bag

```http
PUT /api/smart-bags/{bagId}
```

**Request Body:** (جميع الحقول optional)
```json
{
  "name": "حقيبة رحلة دبي المحدثة",
  "duration": 5,
  "status": "in_progress"
}
```

---

### 5. Delete Bag

```http
DELETE /api/smart-bags/{bagId}
```

---

### 6. Add Item to Bag

```http
POST /api/smart-bags/{bagId}/items
```

**Request Body:**
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

---

### 7. Update Item

```http
PUT /api/smart-bags/{bagId}/items/{itemId}
```

---

### 8. Delete Item

```http
DELETE /api/smart-bags/{bagId}/items/{itemId}
```

---

### 9. Toggle Item Packed Status

```http
POST /api/smart-bags/{bagId}/items/{itemId}/toggle-packed
```

---

### 10. Analyze Bag with AI ⭐

```http
POST /api/smart-bags/{bagId}/analyze
```

**Request Body:**
```json
{
  "preferences": {
    "style": "minimalist",
    "priorities": ["weight", "essentials"]
  },
  "force_reanalysis": false
}
```

**Response:**
```json
{
  "success": true,
  "message": "Bag analyzed successfully",
  "data": {
    "analysis_id": "analysis_xyz123",
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
    "extra_items": [
      {
        "id": "extra_1",
        "name": "حذاء رياضي",
        "reason": "رحلة عمل رسمية لا تحتاج حذاء رياضي",
        "weight_saved": 1.1
      }
    ],
    "weight_optimization": {
      "current_weight": 18.5,
      "suggested_weight": 14.6,
      "weight_saved": 3.9,
      "impact_level": "high",
      "percentage_saved": 21.1
    },
    "additional_suggestions": [...],
    "smart_alert": {
      "alert_id": "alert_1",
      "time_remaining": "6 ساعات",
      "message": "تبقى 6 ساعات على الرحلة وحقيبة الأدوية غير مكتملة",
      "severity": "high"
    },
    "confidence_score": 0.92,
    "processing_time_ms": 1250
  }
}
```

---

### 11. Get Latest Analysis

```http
GET /api/smart-bags/{bagId}/analysis/latest
```

---

### 12. Get Analysis History

```http
GET /api/smart-bags/{bagId}/analysis/history
```

---

### 13. Get Smart Alert

```http
GET /api/smart-bags/{bagId}/smart-alert
```

**Response:**
```json
{
  "success": true,
  "data": {
    "alert_id": "alert_123",
    "bag_id": 1,
    "hours_remaining": 6,
    "time_remaining": "6 ساعات",
    "issues": [
      {
        "category": "medicines",
        "message": "حقيبة الأدوية غير مكتملة",
        "action": "راجع الأدوية الأساسية",
        "severity": "high"
      }
    ],
    "message": "تبقى 6 ساعات على الرحلة وحقيبة الأدوية غير مكتملة",
    "severity": "high"
  }
}
```

---

## 📊 Database Structure

### Table: `bags`

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary Key |
| user_id | bigint | Foreign Key to users |
| name | string | اسم الحقيبة |
| trip_type | enum | نوع الرحلة (عمل، سياحة، عائلية، علاج) |
| duration | integer | مدة الرحلة بالأيام |
| destination | string | الوجهة |
| departure_date | date | تاريخ المغادرة |
| max_weight | decimal | الحد الأقصى للوزن (كجم) |
| total_weight | decimal | الوزن الإجمالي الحالي (كجم) |
| status | enum | الحالة (draft, in_progress, completed, cancelled) |
| preferences | json | تفضيلات المستخدم |
| is_analyzed | boolean | هل تم التحليل؟ |
| last_analyzed_at | timestamp | آخر تحليل |

### Table: `bag_items`

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary Key |
| bag_id | bigint | Foreign Key to bags |
| name | string | اسم الغرض |
| weight | decimal | الوزن (كجم) |
| category | enum | الفئة (ملابس، أحذية، إلكترونيات، أدوية، مستندات، أخرى) |
| essential | boolean | هل الغرض ضروري؟ |
| packed | boolean | هل تم تحزيمه؟ |
| quantity | integer | الكمية |
| notes | text | ملاحظات |

### Table: `bag_analyses`

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary Key |
| bag_id | bigint | Foreign Key to bags |
| analysis_id | string | معرف التحليل الفريد |
| missing_items | json | الأغراض الناقصة |
| extra_items | json | الأغراض الزائدة |
| weight_optimization | json | تحسينات الوزن |
| additional_suggestions | json | اقتراحات إضافية |
| smart_alert | json | التنبيه الذكي |
| confidence_score | decimal | درجة ثقة الـ AI (0-1) |
| processing_time_ms | integer | وقت المعالجة (ms) |
| ai_model | string | نموذج الـ AI المستخدم |

---

## 🎨 Admin Panel (Filament)

### Access

```
/admin/smart-bags
```

### Features

- ✅ View all bags with filters
- ✅ Create/Edit/Delete bags
- ✅ View bag details and items
- ✅ Color-coded weight status
- ✅ Analysis status badges
- ✅ Real-time calculations

---

## 🔔 Smart Alerts System

### How it Works

1. **Scheduled Task** - يعمل كل ساعة
2. **Check Bags** - يتحقق من الحقائب القادمة خلال 24 ساعة
3. **Generate Alerts** - يولد تنبيهات للمشاكل الموجودة
4. **Send Notifications** - يرسل إشعارات FCM للمستخدمين

### Alert Categories

- ❌ **Medicines Missing** - حقيبة الأدوية غير مكتملة
- ❌ **Documents Missing** - وثائق العمل ناقصة
- ⚠️ **Overweight** - الوزن قريب من الحد الأقصى
- ⚠️ **Unpacked Essentials** - أغراض ضرورية غير محزومة

### Manual Trigger

```bash
# Check bags departing in 24 hours
php artisan bags:send-alerts --hours=24

# Check bags departing in 6 hours (urgent)
php artisan bags:send-alerts --hours=6
```

---

## 🌐 Multi-Language Support

### Supported Languages

- 🇸🇦 Arabic (ar)
- 🇬🇧 English (en)

### Translation Files

```
lang/ar/bags.php
lang/en/bags.php
```

### API Language Detection

يتم اكتشاف اللغة من:
1. Header: `Accept-Language: ar` أو `en`
2. User preferences
3. Default: `ar`

---

## 🧪 Testing

### Test Analysis

```bash
# Create a test bag
curl -X POST http://localhost:8000/api/smart-bags \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test Bag",
    "trip_type": "عمل",
    "duration": 3,
    "destination": "Riyadh",
    "departure_date": "2024-12-30",
    "max_weight": 20
  }'

# Add items
curl -X POST http://localhost:8000/api/smart-bags/1/items \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Laptop",
    "weight": 2.5,
    "category": "إلكترونيات",
    "essential": true
  }'

# Analyze
curl -X POST http://localhost:8000/api/smart-bags/1/analyze \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json"
```

---

## 📝 Service Architecture

### Layers

```
┌─────────────────────────────────────┐
│         API Routes                  │
│     /api/smart-bags/*              │
└──────────┬──────────────────────────┘
           │
┌──────────▼──────────────────────────┐
│       Controllers                   │
│  - BagController                    │
│  - BagAnalysisController            │
└──────────┬──────────────────────────┘
           │
┌──────────▼──────────────────────────┐
│       Form Requests                 │
│  - StoreBagRequest                  │
│  - UpdateBagRequest                 │
│  - AnalyzeBagRequest                │
└──────────┬──────────────────────────┘
           │
┌──────────▼──────────────────────────┐
│       Services                      │
│  - BagAnalysisService               │
│  - GeminiAIService                  │
└──────────┬──────────────────────────┘
           │
┌──────────▼──────────────────────────┐
│       Models                        │
│  - Bag                              │
│  - BagItem                          │
│  - BagAnalysis                      │
└──────────┬──────────────────────────┘
           │
┌──────────▼──────────────────────────┐
│       Database                      │
│  - bags                             │
│  - bag_items                        │
│  - bag_analyses                     │
└─────────────────────────────────────┘
```

---

## 🔧 Configuration

### Gemini AI Settings

في `config/services.php`:

```php
'gemini' => [
    'api_key' => env('GEMINI_API_KEY'),
    'model' => env('GEMINI_MODEL', 'gemini-2.0-flash-exp'),
    'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'),
],
```

### Scheduled Tasks

في `bootstrap/app.php`:

```php
->withSchedule(function (Schedule $schedule) {
    // Send smart bag alerts every hour
    $schedule->command('bags:send-alerts --hours=24')->hourly();
    
    // Send urgent alerts for bags departing in 6 hours
    $schedule->command('bags:send-alerts --hours=6')->everyThreeHours();
})
```

---

## 📚 Additional Resources

### API Response Structure

جميع الـ responses تتبع هذه البنية:

```json
{
  "success": true|false,
  "message": "English message",
  "message_ar": "الرسالة بالعربي",
  "data": { ... },
  "meta": {
    "version": "1.0",
    "timestamp": "2024-12-20T10:30:00Z"
  }
}
```

### Error Handling

```json
{
  "success": false,
  "message": "Error message",
  "message_ar": "رسالة الخطأ",
  "error": "Detailed error information"
}
```

---

## 🎯 Best Practices

1. **Always analyze bags before departure**
   - يفضل التحليل قبل 3-7 أيام من السفر

2. **Update items as you pack**
   - استخدم toggle-packed endpoint

3. **Review AI suggestions carefully**
   - الـ AI مساعد وليس بديل عن التفكير البشري

4. **Monitor weight regularly**
   - تحقق من weight_percentage

5. **Don't ignore high-priority alerts**
   - تنبيهات severity: "high" مهمة جداً

---

## 💡 Pro Tips

- استخدم `preferences` لتخصيص التحليل
- راجع `additional_suggestions` للنصائح التنظيمية
- استخدم الفلاتر في API للحصول على نتائج محددة
- تحقق من `days_until_departure` بانتظام

---

## 🤝 Support

للمساعدة أو الاستفسارات، تواصل مع فريق التطوير.

---

**Happy Packing! 🎒✈️**

