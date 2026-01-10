# 🧪 Gemini Events API - دليل الاختبار

## نظرة عامة

هذا الدليل يوضح كيفية اختبار Gemini Events API باستخدام طرق مختلفة.

---

## 📋 متطلبات الاختبار

1. ✅ Laravel Server يعمل (`php artisan serve`)
2. ✅ Gemini API Key موجود في `.env`
3. ✅ اتصال بالإنترنت

---

## 🚀 الاختبار السريع

### الطريقة 1: استخدام curl

```bash
# اختبار أساسي
curl -X GET "http://localhost:8000/api/gemini/events" \
  -H "Accept-Language: ar" \
  -H "Accept-Country: EGY" \
  -H "Accept: application/json"
```

### الطريقة 2: استخدام Postman

1. افتح Postman
2. أنشئ Request جديد
3. Method: `GET`
4. URL: `http://localhost:8000/api/gemini/events`
5. Headers:
   - `Accept-Language`: `ar`
   - `Accept-Country`: `EGY`
   - `Accept`: `application/json`

---

## ✅ Test Cases

### Test Case 1: البحث عن أحداث في مصر (عربي)

**Request:**
```http
GET http://localhost:8000/api/gemini/events
Accept-Language: ar
Accept-Country: EGY
Accept: application/json
```

**Expected Response:**
- Status: `200 OK`
- Body contains: `country`, `language`, `generated_at`, `events`
- `language` = `"ar"`
- `country` = `"Egypt"` (not "EGY")

---

### Test Case 2: البحث عن أحداث في السعودية (إنجليزي)

**Request:**
```http
GET http://localhost:8000/api/gemini/events
Accept-Language: en
Accept-Country: SAU
Accept: application/json
```

**Expected Response:**
- Status: `200 OK`
- `language` = `"en"`
- `country` = `"Saudi Arabia"`

---

### Test Case 3: استخدام Query Parameter

**Request:**
```http
GET http://localhost:8000/api/gemini/events?country=UAE
Accept-Language: ar
Accept: application/json
```

**Expected Response:**
- Status: `200 OK`
- Works without `Accept-Country` header

---

### Test Case 4: Missing Country (Error Test)

**Request:**
```http
GET http://localhost:8000/api/gemini/events
Accept-Language: ar
Accept: application/json
```

**Expected Response:**
- Status: `400 Bad Request`
- Message: `country_required`

---

### Test Case 5: Default Language (Arabic)

**Request:**
```http
GET http://localhost:8000/api/gemini/events?country=EGY
Accept: application/json
```

**Expected Response:**
- Status: `200 OK`
- `language` = `"ar"` (default)

---

### Test Case 6: Invalid Language (Defaults to Arabic)

**Request:**
```http
GET http://localhost:8000/api/gemini/events
Accept-Language: fr
Accept-Country: EGY
Accept: application/json
```

**Expected Response:**
- Status: `200 OK`
- `language` = `"ar"` (defaults to Arabic)

---

### Test Case 7: POST Request

**Request:**
```http
POST http://localhost:8000/api/gemini/events
Accept-Language: ar
Accept-Country: EGY
Content-Type: application/json
Accept: application/json
```

**Expected Response:**
- Status: `200 OK`
- Same structure as GET request

---

### Test Case 8: Multiple Countries

اختبر مع رموز دول مختلفة:

```bash
# مصر
curl -X GET "http://localhost:8000/api/gemini/events" \
  -H "Accept-Language: ar" \
  -H "Accept-Country: EGY"

# السعودية
curl -X GET "http://localhost:8000/api/gemini/events" \
  -H "Accept-Language: ar" \
  -H "Accept-Country: SAU"

# الإمارات
curl -X GET "http://localhost:8000/api/gemini/events" \
  -H "Accept-Language: ar" \
  -H "Accept-Country: UAE"
```

---

## 🔍 Validation Checklist

عند اختبار الـ API، تأكد من:

- [ ] Status code = `200` للطلبات الصحيحة
- [ ] Status code = `400` عند عدم إرسال country
- [ ] Response يحتوي على `country`, `language`, `generated_at`, `events`
- [ ] `language` = `"ar"` أو `"en"` فقط
- [ ] `generated_at` في صيغة `YYYY-MM-DD`
- [ ] `events` هو array
- [ ] كل event يحتوي على: `title`, `date`, `city`, `country`, `category`, `source`
- [ ] `date` في صيغة `YYYY-MM-DD`
- [ ] `country` في الـ response هو اسم الدولة (مثل "Egypt") وليس الرمز (مثل "EGY")

---

## 🧪 Automated Tests

### تشغيل Unit Tests

```bash
php artisan test --filter GeminiEventsApiTest
```

أو لتشغيل جميع الاختبارات:

```bash
php artisan test
```

---

## 📊 Test Scenarios

### Scenario 1: Happy Path

1. ✅ أرسل request صحيح
2. ✅ احصل على response 200
3. ✅ تحقق من structure
4. ✅ تحقق من البيانات

### Scenario 2: Error Handling

1. ✅ أرسل request بدون country
2. ✅ احصل على 400 error
3. ✅ تحقق من error message

### Scenario 3: Edge Cases

1. ✅ Language غير صحيح → defaults to Arabic
2. ✅ Country code غير موجود → يستخدم القيمة كما هي
3. ✅ Empty events → returns empty array with note

---

## 🐛 Debugging

### إذا فشل الاختبار:

1. **تحقق من الـ Logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **تحقق من إعدادات Gemini:**
   ```bash
   php artisan tinker
   >>> config('services.gemini.api_key')
   >>> config('services.gemini.model')
   ```

3. **اختبر الاتصال بـ Gemini مباشرة:**
   ```bash
   php test_gemini.php
   ```

---

## 📝 Test Results Template

```
Test Date: ___________
Tester: ___________

Test Case 1: [ ] Pass [ ] Fail
Test Case 2: [ ] Pass [ ] Fail
Test Case 3: [ ] Pass [ ] Fail
Test Case 4: [ ] Pass [ ] Fail
Test Case 5: [ ] Pass [ ] Fail
Test Case 6: [ ] Pass [ ] Fail
Test Case 7: [ ] Pass [ ] Fail
Test Case 8: [ ] Pass [ ] Fail

Notes:
_______________________________________
_______________________________________
```

---

## 🎯 Performance Testing

### Test Response Time

```bash
time curl -X GET "http://localhost:8000/api/gemini/events" \
  -H "Accept-Language: ar" \
  -H "Accept-Country: EGY"
```

**Expected:** أقل من 30 ثانية (timeout limit)

---

## ✅ Success Criteria

الـ API يعمل بشكل صحيح إذا:

- ✅ جميع Test Cases تمر
- ✅ Response time < 30 seconds
- ✅ No 500 errors
- ✅ Proper error handling (400 for missing country)
- ✅ Correct data structure
- ✅ Language switching works
- ✅ Country code conversion works

---

**آخر تحديث:** 2025-01-15

