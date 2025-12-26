# 🔧 إصلاح خطأ Firebase Credentials

## ❌ الخطأ الحالي

```
file_get_contents(): Read of 12288 bytes failed with errno=21 Is a directory
```

## 🔍 السبب

الخطأ يحدث لأن:
1. ملف Firebase credentials غير موجود في المسار المحدد
2. أو المسار يشير إلى مجلد بدلاً من ملف

## ✅ الحل

### الخطوة 1: إنشاء المجلد (إذا لم يكن موجوداً)

```bash
# Windows (PowerShell)
mkdir storage\app\firebase

# Linux/Mac
mkdir -p storage/app/firebase
```

### الخطوة 2: وضع ملف Firebase Service Account JSON

1. اذهب إلى [Firebase Console](https://console.firebase.google.com/)
2. اختر المشروع: `season-9ede3`
3. اذهب إلى **Project Settings** → **Service Accounts**
4. اضغط على **Generate New Private Key**
5. احفظ الملف باسم: `season-9ede3-firebase-adminsdk-fbsvc-c1b9e2f2e7.json`
6. ضع الملف في: `storage/app/firebase/`

### الخطوة 3: التحقق من ملف .env

تأكد أن ملف `.env` يحتوي على:

```env
FIREBASE_CREDENTIALS=firebase/season-9ede3-firebase-adminsdk-fbsvc-c1b9e2f2e7.json
FIREBASE_PROJECT_ID=season-9ede3
```

**ملاحظة مهمة:** المسار يبدأ من `storage/app/` تلقائياً، لا تكتب المسار الكامل!

### الخطوة 4: مسح الكاش

```bash
php artisan config:clear
php artisan cache:clear
```

### الخطوة 5: التحقق من الملف

تأكد أن الملف موجود في:
```
storage/app/firebase/season-9ede3-firebase-adminsdk-fbsvc-c1b9e2f2e7.json
```

## 🧪 اختبار

بعد إصلاح المشكلة، جرب إرسال إشعار:

```php
$firebase = app(\App\Services\FirebaseService::class);
$firebase->sendToDevice($fcmToken, 'Test', 'This is a test notification');
```

## 📝 ملاحظات

- الملف JSON يجب أن يكون ملف صحيح وليس مجلد
- تأكد من صلاحيات القراءة للملف
- لا تضع الملف في Git (يجب أن يكون في `.gitignore`)

