# 🚀 البدء السريع - Firebase Cloud Messaging

## خطوة واحدة للتجهيز! ⚡

### 1️⃣ أضف إعدادات Firebase لملف .env

افتح ملف `.env` وأضف هذين السطرين في أي مكان:

```env
FIREBASE_CREDENTIALS=firebase/season-9ede3-firebase-adminsdk-fbsvc-c1b9e2f2e7.json
FIREBASE_PROJECT_ID=season-9ede3
```

### 2️⃣ شغّل هذا الأمر

```bash
php artisan config:cache
```

---

## ✅ اختبار سريع (طريقتين)

### الطريقة الأولى: ملف الاختبار التلقائي

```bash
php TEST_NOW.php
```

هذا الملف راح يفحص كل شيء ويعطيك تقرير كامل! ✨

---

### الطريقة الثانية: اختبار يدوي بـ Tinker

```bash
php artisan tinker
```

ثم نفّذ:

```php
// 1. جيب أول مستخدم
$user = User::first();

// 2. أضف له FCM Token تجريبي
$user->fcm_token = 'test_token_' . now()->timestamp;
$user->save();

// 3. جرب ترسل إشعار
$firebase = app(App\Services\FirebaseService::class);
$firebase->sendToDevice(
    $user->fcm_token,
    'اختبار',
    'مرحباً من Firebase!',
    ['type' => 'test']
);
```

**النتيجة المتوقعة:**
- إذا كان الـ token صحيح (من موبايل حقيقي) → راح يوصل الإشعار ✅
- إذا كان token تجريبي → راح يطلع error بس تتأكد إن الكود شغال ⚠️

---

## 🎯 اختبار كامل مع Postman

### الخطوة 1: Login وجيب Token

```
POST http://localhost:8000/api/auth/login

Body (JSON):
{
  "email": "admin@example.com",
  "password": "password"
}
```

**احفظ الـ token من الرد**

---

### الخطوة 2: أرسل إشعار

```
POST http://localhost:8000/api/notifications/send-to-user

Headers:
Authorization: Bearer YOUR_TOKEN_HERE
Content-Type: application/json

Body (JSON):
{
  "user_id": 1,
  "title": "اختبار الإشعارات",
  "body": "مرحباً! النظام يعمل بنجاح 🎉",
  "data": {
    "type": "test",
    "screen": "home"
  }
}
```

---

## 📊 النتائج المتوقعة

### ✅ إذا نجح:

```json
{
  "success": true,
  "message": "Notification sent successfully",
  "response": {
    "name": "projects/season-9ede3/messages/0:1234567890"
  }
}
```

### ❌ إذا فشل:

**"User has no FCM token"** → المستخدم ما عنده token، أضف واحد في tinker

**"Failed to fetch access token"** → تأكد من ملف Firebase والإعدادات في .env

**"Invalid project ID"** → تأكد من FIREBASE_PROJECT_ID في .env

---

## 🔍 فحص الـ Logs

افتح الملف: `storage/logs/laravel.log`

أو في Terminal:

```bash
type storage\logs\laravel.log
```

ابحث عن:
- `FCM Notification sent successfully` = نجح ✅
- `FCM Send Error` = فيه مشكلة ❌

---

## 💡 نصائح مهمة

1. **FCM Token الحقيقي** يجي من التطبيق (Flutter/React Native)
2. **Token التجريبي** ما راح يشتغل لكن راح يتأكد إن الكود سليم
3. **افحص الـ Logs دائماً** عشان تعرف وين المشكلة

---

## 📱 ربط التطبيق Mobile

### في Flutter مثلاً:

```dart
// احصل على FCM Token
String? token = await FirebaseMessaging.instance.getToken();

// أرسله مع Login
final response = await http.post(
  Uri.parse('http://your-api.com/api/auth/login'),
  body: {
    'email': 'user@example.com',
    'password': 'password',
    'fcm_token': token,  // 👈 هنا
  },
);
```

### في React Native:

```javascript
import messaging from '@react-native-firebase/messaging';

// احصل على Token
const token = await messaging().getToken();

// أرسله مع Login
await fetch('http://your-api.com/api/auth/login', {
  method: 'POST',
  body: JSON.stringify({
    email: 'user@example.com',
    password: 'password',
    fcm_token: token,  // 👈 هنا
  }),
});
```

---

## 🎉 تمام! جاهز للتجربة

شغّل الاختبار الآن:

```bash
php TEST_NOW.php
```

أو اتبع الخطوات اليدوية فوق 👆

---

## 📚 ملفات إضافية للمساعدة

- **TESTING_GUIDE_AR.md** → دليل شامل بالعربي
- **FIREBASE_SETUP.md** → دليل التثبيت الكامل بالإنجليزي
- **INSTALLATION_CHECKLIST.md** → قائمة التحقق

---

**بالتوفيق! إذا واجهت أي مشكلة، فتّش في الـ Logs أو شوف الملفات فوق 🚀**



