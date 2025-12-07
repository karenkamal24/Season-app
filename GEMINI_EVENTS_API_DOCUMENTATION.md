# 📚 Gemini Events API Documentation

## نظرة عامة

هذا الـ API يسمح بالبحث عن الأحداث القادمة في دولة معينة باستخدام Google Gemini AI. الـ API متاح بدون authentication ويدعم اللغة العربية والإنجليزية.

---

## 🔗 Base URL

```
http://localhost:8000/api/gemini/events
```

أو في الإنتاج:
```
https://your-domain.com/api/gemini/events
```

---

## 📋 Endpoints

### GET /api/gemini/events

البحث عن الأحداث القادمة في دولة معينة.

**Authentication:** ❌ غير مطلوب (Public API)

---

## 📥 Request

### Headers

| Header Name | Type | Required | Description | Example |
|------------|------|----------|-------------|---------|
| `Accept-Language` | string | No | اللغة المطلوبة (`ar` أو `en`). الافتراضي: `ar` | `ar` |
| `Accept-Country` | string | Yes | رمز الدولة (مثل `EGY`, `KSA`, `UAE`) | `EGY` |
| `Accept` | string | No | نوع الـ response المطلوب | `application/json` |

**ملاحظة:** يمكنك أيضاً استخدام `language` و `country` headers للتوافق مع الإصدارات السابقة.

### Query Parameters

| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `country` | string | No* | رمز الدولة (إذا لم يتم إرساله في header) | `EGY` |

*مطلوب إما في header أو query parameter

---

## 📤 Response

### Success Response (200 OK)

```json
{
  "country": "Egypt",
  "language": "ar",
  "generated_at": "2025-01-15",
  "events": [
    {
      "title": "مهرجان القاهرة للموسيقى",
      "date": "2025-01-20",
      "start_at": "18:00",
      "end_at": "22:00",
      "city": "القاهرة",
      "venue": "دار الأوبرا المصرية",
      "country": "Egypt",
      "category": "موسيقى",
      "source": "موقع المهرجان الرسمي"
    },
    {
      "title": "معرض الكتاب الدولي",
      "date": "2025-01-25",
      "start_at": "10:00",
      "end_at": "20:00",
      "city": "القاهرة",
      "venue": "مركز المعارض",
      "country": "Egypt",
      "category": "معرض",
      "source": "وزارة الثقافة"
    }
  ]
}
```

### Empty Events Response (200 OK)

```json
{
  "country": "Egypt",
  "language": "ar",
  "generated_at": "2025-01-15",
  "events": [],
  "note": "No upcoming events found"
}
```

### Error Response (400 Bad Request)

```json
{
  "status": 400,
  "message": "country_required",
  "meta": null,
  "data": []
}
```

### Error Response (500 Internal Server Error)

```json
{
  "status": 500,
  "message": "events_search_error",
  "meta": null,
  "data": []
}
```

---

## 🌍 رموز الدول المدعومة

| Code | Country (English) | Country (Arabic) |
|------|------------------|-----------------|
| `EGY` | Egypt | مصر |
| `KSA` أو `SAU` | Saudi Arabia | السعودية |
| `UAE` أو `ARE` | United Arab Emirates | الإمارات |
| `JOR` | Jordan | الأردن |
| `KWT` | Kuwait | الكويت |
| `QAT` | Qatar | قطر |
| `BHR` | Bahrain | البحرين |
| `OMN` | Oman | عمان |
| `LBN` | Lebanon | لبنان |
| `IRQ` | Iraq | العراق |
| `SYR` | Syria | سوريا |
| `YEM` | Yemen | اليمن |
| `PSE` | Palestine | فلسطين |

**ملاحظة:** يمكنك أيضاً استخدام اسم الدولة الكامل (مثل `Egypt` أو `Saudi Arabia`).

---

## 📝 Response Fields

### Event Object

| Field | Type | Description | Example |
|-------|------|-------------|---------|
| `title` | string | اسم الحدث | "مهرجان القاهرة للموسيقى" |
| `date` | string | تاريخ الحدث (YYYY-MM-DD) | "2025-01-20" |
| `start_at` | string\|null | وقت البداية (HH:MM أو YYYY-MM-DD HH:MM) | "18:00" |
| `end_at` | string\|null | وقت النهاية (HH:MM أو YYYY-MM-DD HH:MM) | "22:00" |
| `city` | string | المدينة | "القاهرة" |
| `venue` | string\|null | مكان الحدث | "دار الأوبرا المصرية" |
| `country` | string | الدولة | "Egypt" |
| `category` | string | نوع الحدث | "موسيقى" |
| `source` | string | مصدر المعلومات | "موقع المهرجان الرسمي" |

### Root Object

| Field | Type | Description |
|-------|------|-------------|
| `country` | string | الدولة المستخدمة في البحث |
| `language` | string | اللغة المستخدمة (`ar` أو `en`) |
| `generated_at` | string | تاريخ إنشاء الـ response (YYYY-MM-DD) |
| `events` | array | قائمة الأحداث |
| `note` | string | ملاحظة (في حالة عدم وجود أحداث) |

---

## 💡 أمثلة الاستخدام

### مثال 1: البحث عن أحداث في مصر بالعربية

**Request:**
```http
GET http://localhost:8000/api/gemini/events
Accept-Language: ar
Accept-Country: EGY
Accept: application/json
```

**Response:**
```json
{
  "country": "Egypt",
  "language": "ar",
  "generated_at": "2025-01-15",
  "events": [...]
}
```
