# حل مشكلة "Failed to fetch" في رفع الملفات في Filament

## المشكلة
خطأ `Uncaught (in promise) TypeError: Failed to fetch` يحدث عند محاولة رفع ملف في Filament FileUpload component.

## الأسباب المحتملة والحلول

### 1. ✅ تم الحل: مشكلة Symlink
**المشكلة:** `public/storage` كان مجلد عادي وليس symlink
**الحل:** تم إنشاء symlink باستخدام `php artisan storage:link`

### 2. ملفات قديمة بمسارات غير صحيحة
**المشكلة:** قد تكون هناك سجلات في قاعدة البيانات تشير إلى ملفات غير موجودة
**الحل:**
```bash
# التحقق من الملفات المفقودة
php artisan tinker
>>> App\Models\Banner::all()->each(function($b) { 
    if ($b->image && !file_exists(storage_path('app/public/' . $b->image))) {
        echo "Banner ID {$b->id}: File missing - {$b->image}\n";
    }
});
```

### 3. مشكلة في URL Generation
**المشكلة:** قد يكون `APP_URL` في `.env` غير صحيح
**الحل:** تأكد من أن `APP_URL` في ملف `.env` يحتوي على الرابط الصحيح:
```env
APP_URL=http://localhost:8000
# أو
APP_URL=https://yourdomain.com
```

### 4. مشكلة في الصلاحيات (Permissions)
**المشكلة:** المجلدات لا تملك صلاحيات الكتابة
**الحل (Linux/Mac):**
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

**الحل (Windows):** تأكد من أن IIS أو Apache لديه صلاحيات الكتابة على مجلد `storage`

### 5. مشكلة في CSRF Token
**المشكلة:** قد يكون هناك مشكلة في CSRF token
**الحل:** تأكد من أن الجلسة تعمل بشكل صحيح. جرب:
- مسح cookies المتصفح
- إعادة تسجيل الدخول

### 6. حجم الملف كبير جداً
**المشكلة:** حجم الملف يتجاوز الحد المسموح
**الحل:** تم ضبط `maxSize(5120)` في الكود (5MB). تأكد من:
- `upload_max_filesize` في `php.ini` أكبر من 5MB
- `post_max_size` في `php.ini` أكبر من 5MB

### 7. مشكلة في المتصفح/Cache
**الحل:**
1. امسح cache المتصفح (Ctrl+Shift+Delete)
2. جرب متصفح آخر
3. افتح Developer Tools (F12) وافحص Network tab لرؤية الطلب الفاشل

## خطوات التشخيص

### 1. فحص Console في المتصفح
افتح Developer Tools (F12) → Console tab وابحث عن:
- أي أخطاء إضافية
- URL الملف الذي يحاول تحميله

### 2. فحص Network Tab
افتح Developer Tools (F12) → Network tab:
- ابحث عن الطلبات الفاشلة (باللون الأحمر)
- انقر عليها لرؤية التفاصيل:
  - Status Code
  - Request URL
  - Response

### 3. فحص Laravel Logs
```bash
tail -f storage/logs/laravel.log
```
ثم حاول رفع ملف مرة أخرى وراقب الأخطاء

## الحل السريع

إذا استمرت المشكلة بعد كل الحلول أعلاه:

1. **امسح cache Laravel:**
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

2. **تحقق من Symlink:**
```bash
php artisan storage:link
```

3. **أعد تشغيل الخادم:**
```bash
php artisan serve
```

4. **جرب في وضع التطوير:**
في `.env`:
```env
APP_DEBUG=true
```

## ملاحظات إضافية

- تأكد من أن `storage/app/public/banners` موجود وله صلاحيات الكتابة
- في Windows، قد تحتاج صلاحيات Administrator لإنشاء symlink
- إذا كنت تستخدم IIS، تأكد من إعداد URL Rewrite بشكل صحيح



