# 🧪 دليل اختبار Gemini API

## الخطوة 1️⃣: مسح الكاش

```bash
php artisan config:clear
```

## الخطوة 2️⃣: الحصول على Token

### باستخدام Postman أو curl:

```bash
POST http://localhost:8000/api/auth/login

Body (JSON):
{
  "email": "your_email@example.com",
  "password": "your_password"
}
```

**احفظ الـ Token من الرد** (سيكون في حقل `token`)

---

## 🎯 اختبار Endpoints

### 1️⃣ اختبار البحث العام (`/api/gemini/search`)

```bash
POST http://localhost:8000/api/gemini/search

Headers:
  Authorization: Bearer YOUR_TOKEN_HERE
  Content-Type: application/json
  Accept: application/json

Body (JSON):
{
  "query": "ما هي أفضل الأماكن للزيارة في مصر؟",
  "temperature": 0.7,
  "max_output_tokens": 1000
}
```

### 2️⃣ اختبار البحث المبسط (`/api/gemini/query`)

```bash
POST http://localhost:8000/api/gemini/query

Headers:
  Authorization: Bearer YOUR_TOKEN_HERE
  Content-Type: application/json

Body (JSON):
{
  "query": "أخبرني عن السياحة في دبي"
}
```

### 3️⃣ اختبار البحث عن الأحداث (`/api/gemini/events`) ⭐

**⚠️ ملاحظة:** هذا الـ endpoint متاح بدون authentication (لا يحتاج token)

#### الطريقة الأولى: باستخدام Country Code في Header (مُوصى به) ✅

```bash
GET http://localhost:8000/api/gemini/events

Headers:
  Accept-Language: ar
  Accept-Country: EGY
  Accept: application/json
```

**رموز الدول المدعومة:**
- `EGY` - مصر (Egypt)
- `KSA` أو `SAU` - السعودية (Saudi Arabia)
- `UAE` أو `ARE` - الإمارات (United Arab Emirates)
- `JOR` - الأردن (Jordan)
- `KWT` - الكويت (Kuwait)
- `QAT` - قطر (Qatar)
- `BHR` - البحرين (Bahrain)
- `OMN` - عمان (Oman)
- `LBN` - لبنان (Lebanon)
- وغيرها...

#### الطريقة الثانية: باستخدام Query Parameters

```bash
GET http://localhost:8000/api/gemini/events?country=EGY

Headers:
  language: en
  Accept: application/json
```

#### الطريقة الثالثة: POST Request

```bash
POST http://localhost:8000/api/gemini/events

Headers:
  Accept-Language: ar
  Accept-Country: KSA
  Content-Type: application/json
  Accept: application/json
```

**ملاحظة:** 
- استخدم `Accept-Country` و `Accept-Language` في الـ headers (مُوصى به)
- يمكنك أيضاً استخدام `country` و `language` للتوافق مع الإصدارات السابقة
- يمكنك استخدام اسم الدولة الكامل (مثل "Egypt" أو "Saudi Arabia") بدلاً من الرمز

---

## 🧪 أمثلة curl كاملة

### 1. Login للحصول على Token:

```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d "{\"email\":\"your_email@example.com\",\"password\":\"your_password\"}"
```

### 2. اختبار البحث العام:

```bash
curl -X POST http://localhost:8000/api/gemini/search \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d "{\"query\":\"ما هي أفضل الأماكن للزيارة في مصر؟\"}"
```

### 3. اختبار البحث عن الأحداث (باستخدام Country Code) - بدون Token:

```bash
curl -X GET "http://localhost:8000/api/gemini/events?country=EGY" \
  -H "Accept-Language: ar" \
  -H "Accept: application/json"
```

أو باستخدام Header (مُوصى به):

```bash
curl -X GET "http://localhost:8000/api/gemini/events" \
  -H "Accept-Language: ar" \
  -H "Accept-Country: EGY" \
  -H "Accept: application/json"
```

---

## 📱 أمثلة Postman

### Collection Structure:

1. **Login**
   - Method: POST
   - URL: `{{base_url}}/api/auth/login`
   - Body: `{"email":"...","password":"..."}`

2. **Gemini Search**
   - Method: POST
   - URL: `{{base_url}}/api/gemini/search`
   - Headers: `Authorization: Bearer {{token}}`
   - Body: `{"query":"..."}`

3. **Gemini Events** (Public - No Auth Required)
   - Method: GET
   - URL: `{{base_url}}/api/gemini/events`
   - Headers: 
     - `Accept-Language: ar` (أو `language: ar`)
     - `Accept-Country: EGY` (أو `country: EGY`) - استخدم رمز الدولة: EGY, KSA, UAE, إلخ
   - **ملاحظة:** لا يحتاج Authorization header

---

## ✅ Response المتوقع

### للبحث العام:
```json
{
  "status": 200,
  "message": "Search completed successfully",
  "data": {
    "query": "...",
    "response": "النص من Gemini...",
    "model": "gemini-2.5-flash",
    "usage": {...}
  }
}
```

### للبحث عن الأحداث:
```json
{
  "country": "Egypt",
  "language": "ar",
  "generated_at": "2025-01-15",
  "events": [
    {
      "title": "...",
      "date": "2025-01-20",
      "start_at": "18:00",
      "end_at": "22:00",
      "city": "...",
      "venue": "...",
      "country": "Egypt",
      "category": "...",
      "url": "https://...",
      "source": "..."
    }
  ]
}
```

**ملاحظة:** عند إرسال `country: EGY` في header، سيتم تحويله تلقائياً إلى "Egypt" للبحث.

---

## ⚠️ حل المشاكل

### خطأ: "Gemini API key is not configured"
```bash
# تأكد من وجود المتغيرات في .env
php artisan config:clear
php artisan config:cache
```

### خطأ: "Unauthenticated"
- هذا الخطأ لا يظهر في `/api/gemini/events` لأنه متاح بدون authentication
- للـ endpoints الأخرى (`/search`, `/query`)، تأكد من إرسال Token صحيح في Header

### خطأ: "Failed to parse JSON response"
- هذا يعني أن Gemini رجع response غير صحيح
- تحقق من الـ logs في `storage/logs/laravel.log`

---

## 🚀 اختبار سريع من Terminal

### اختبار Events (بدون Token):

```bash
curl -X GET "http://localhost:8000/api/gemini/events" \
  -H "Accept-Language: ar" \
  -H "Accept-Country: EGY" \
  -H "Accept: application/json"
```

### اختبار Search/Query (يحتاج Token):

```bash
# 1. احصل على token
TOKEN=$(curl -s -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"your_email","password":"your_password"}' | jq -r '.token')

# 2. اختبر البحث
curl -X POST http://localhost:8000/api/gemini/query \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"query":"مرحباً"}'
```

---

**بالتوفيق! 🎉**

