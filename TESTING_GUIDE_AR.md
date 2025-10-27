# 🧪 دليل اختبار Firebase Cloud Messaging

## الخطوة 1️⃣: إضافة إعدادات Firebase إلى ملف .env

افتح ملف `.env` وأضف هذين السطرين:

```env
FIREBASE_CREDENTIALS=firebase/season-9ede3-firebase-adminsdk-fbsvc-c1b9e2f2e7.json
FIREBASE_PROJECT_ID=season-9ede3
```

## الخطوة 2️⃣: تحديث الإعدادات

قم بتشغيل هذا الأمر في Terminal:

```bash
php artisan config:cache
```

---

## 🎯 الاختبار السريع

### الطريقة 1: اختبار من خلال Postman

#### 1. احصل على Token المصادقة أولاً:

```
POST http://localhost:8000/api/auth/login

Body (JSON):
{
  "email": "admin@example.com",
  "password": "password"
}
```

**احفظ الـ Token من الرد**

#### 2. اختبر إرسال الإشعار:

```
POST http://localhost:8000/api/notifications/send-to-user

Headers:
Authorization: Bearer YOUR_TOKEN_HERE
Content-Type: application/json

Body (JSON):
{
  "user_id": 1,
  "title": "اختبار الإشعارات",
  "body": "مرحباً! هذا إشعار تجريبي من Firebase",
  "data": {
    "type": "test",
    "timestamp": "2025-10-27"
  }
}
```

---

### الطريقة 2: اختبار من خلال Tinker

افتح Terminal وشغل:

```bash
php artisan tinker
```

ثم نفذ هذا الكود:

```php
// 1. احصل على المستخدم
$user = App\Models\User::first();

// 2. أضف FCM Token تجريبي (في حالة عدم وجود token حقيقي)
$user->fcm_token = 'test_token_from_mobile_app';
$user->save();

// 3. جرب إرسال الإشعار
$firebase = app(App\Services\FirebaseService::class);

$firebase->sendToDevice(
    $user->fcm_token,
    'اختبار من Tinker',
    'مرحباً! هذا اختبار للتأكد من عمل النظام',
    ['type' => 'test']
);
```

**ملاحظة:** إذا كان الـ FCM Token تجريبي وليس من تطبيق حقيقي، سيفشل الإرسال لكن ستتأكد من أن الكود يعمل.

---

### الطريقة 3: فحص الـ Routes

تأكد من أن الـ API Routes موجودة:

```bash
php artisan route:list | findstr notifications
```

يجب أن ترى:

```
POST   api/notifications/send-to-user
POST   api/notifications/send-to-all
POST   api/notifications/send-to-multiple
```

---

## 📊 التحقق من الـ Logs

افتح ملف الـ Logs:

```bash
type storage\logs\laravel.log
```

أو افتح الملف: `storage/logs/laravel.log`

**ابحث عن:**

✅ **إذا نجح الإرسال:**
```
FCM Notification sent successfully
```

❌ **إذا فشل:**
```
FCM Send Error
FCM Notification failed
Firebase Access Token Error
```

---

## 🔍 اختبار خطوة بخطوة مع شرح النتائج

### الاختبار 1: التحقق من وجود ملف Firebase

```bash
dir storage\app\firebase\
```

**النتيجة المتوقعة:**
```
season-9ede3-firebase-adminsdk-fbsvc-c1b9e2f2e7.json
```

✅ إذا ظهر الملف = ممتاز!  
❌ إذا لم يظهر = المشكلة في مكان الملف

---

### الاختبار 2: التحقق من قاعدة البيانات

افتح `php artisan tinker` ونفذ:

```php
// تحقق من وجود عمود fcm_token
Schema::hasColumn('users', 'fcm_token');
// يجب أن يعطي: true

// شوف بيانات أول مستخدم
User::first()->makeVisible('fcm_token')->toArray();
```

---

### الاختبار 3: اختبار الـ Service مباشرة

```php
// في tinker
$firebase = app(App\Services\FirebaseService::class);

// اختبر التحقق من Token
$firebase->isValidToken('short');  // false
$firebase->isValidToken(str_repeat('a', 150));  // true
```

---

## 🚀 الاختبار الكامل مع مستخدم حقيقي

### الخطوات:

#### 1. أضف FCM Token للمستخدم

عند تسجيل الدخول من التطبيق، أرسل الـ FCM Token:

```
POST /api/auth/login

{
  "email": "user@example.com",
  "password": "password",
  "fcm_token": "FCM_TOKEN_FROM_MOBILE_APP"
}
```

#### 2. احفظ الـ Token في الكنترولر

عدّل `AuthController` أو أي كنترولر تسجيل الدخول:

```php
public function login(Request $request)
{
    // ... كود تسجيل الدخول ...
    
    // احفظ FCM Token
    if ($request->has('fcm_token')) {
        $user->fcm_token = $request->fcm_token;
        $user->save();
    }
    
    return response()->json([
        'status' => 200,
        'message' => 'Login successful',
        'data' => $user
    ]);
}
```

#### 3. جرب الإرسال

```
POST /api/notifications/send-to-user

{
  "user_id": 1,
  "title": "مرحباً",
  "body": "تم تسجيل الدخول بنجاح",
  "data": {
    "type": "login_success"
  }
}
```

---

## 📱 اختبار من التطبيق Mobile

### في Flutter مثلاً:

```dart
import 'package:firebase_messaging/firebase_messaging.dart';

// احصل على FCM Token
String? fcmToken = await FirebaseMessaging.instance.getToken();

// أرسله مع Login Request
final response = await http.post(
  Uri.parse('http://your-api.com/api/auth/login'),
  headers: {'Content-Type': 'application/json'},
  body: jsonEncode({
    'email': 'user@example.com',
    'password': 'password',
    'fcm_token': fcmToken,
  }),
);
```

---

## ⚠️ الأخطاء الشائعة وحلولها

### خطأ 1: "Failed to fetch access token"

**الحل:**
```bash
# تأكد من وجود الملف
dir storage\app\firebase\season-9ede3-firebase-adminsdk-fbsvc-c1b9e2f2e7.json

# تأكد من الإعدادات
php artisan config:clear
php artisan config:cache
```

---

### خطأ 2: "User has no FCM token"

**الحل:**
```php
// في tinker
$user = User::find(1);
$user->fcm_token = 'test_token_here';
$user->save();
```

---

### خطأ 3: "Invalid project ID"

**الحل:**
افتح `.env` وتأكد من:
```env
FIREBASE_PROJECT_ID=season-9ede3
```

ثم:
```bash
php artisan config:cache
```

---

## ✅ قائمة التحقق السريعة

قبل الاختبار، تأكد من:

- [ ] ملف Firebase موجود في `storage/app/firebase/`
- [ ] `.env` يحتوي على `FIREBASE_CREDENTIALS` و `FIREBASE_PROJECT_ID`
- [ ] تم تشغيل `php artisan config:cache`
- [ ] تم تشغيل `composer require google/apiclient`
- [ ] الجدول `users` يحتوي على عمود `fcm_token`
- [ ] المستخدم لديه `fcm_token` محفوظ

---

## 🎉 مثال اختبار ناجح

### Request:
```json
POST /api/notifications/send-to-user
Authorization: Bearer 1|abc123...

{
  "user_id": 1,
  "title": "مرحباً",
  "body": "الإشعار يعمل بنجاح!",
  "data": {
    "type": "test",
    "action": "open_home"
  }
}
```

### Response (نجح):
```json
{
  "success": true,
  "message": "Notification sent successfully",
  "response": {
    "name": "projects/season-9ede3/messages/0:1234567890"
  }
}
```

### في الـ Logs:
```
[2025-10-27 17:00:00] local.INFO: FCM Notification sent successfully
```

---

## 🔧 أدوات إضافية للاختبار

### 1. اختبار من Firebase Console

- افتح Firebase Console
- اذهب إلى Cloud Messaging
- اضغط "Send test message"
- أدخل FCM Token من قاعدة البيانات
- أرسل إشعار تجريبي

### 2. استخدام cURL

```bash
curl -X POST http://localhost:8000/api/notifications/send-to-user \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d "{\"user_id\":1,\"title\":\"Test\",\"body\":\"Hello\"}"
```

---

## 📞 الدعم

إذا واجهت مشكلة:

1. **افحص الـ Logs أولاً:**
   ```bash
   type storage\logs\laravel.log
   ```

2. **اختبر الـ Service:**
   ```bash
   php artisan tinker
   >>> app(App\Services\FirebaseService::class)
   ```

3. **تحقق من الإعدادات:**
   ```bash
   php artisan config:show | findstr FIREBASE
   ```

---

**بالتوفيق! 🚀**



