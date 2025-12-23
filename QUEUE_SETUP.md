# Queue Worker Setup Guide - إعداد Queue Worker

## المشكلة
OTP emails تصل متأخرة لأن queue jobs تبقى في الانتظار حتى يتم معالجتها من queue worker.

## الحلول المطبقة

### 1. تحسينات الكود ✅
- ✅ استخدام queue منفصل `emails` لـ OTP emails
- ✅ تقليل SMTP timeout إلى 10 ثواني
- ✅ إضافة SMTPKeepAlive للحفاظ على الاتصال

### 2. تغيير Queue Connection (مستحسن)

في ملف `.env` على السيرفر، قم بتغيير:

```env
# من:
QUEUE_CONNECTION=database

# إلى:
QUEUE_CONNECTION=redis
```

**لماذا Redis؟**
- أسرع من database queue
- أفضل للأداء في الإنتاج
- يدعم queue منفصلة بشكل أفضل

### 3. إعداد Queue Worker على السيرفر

#### أ) تشغيل Queue Worker يدوياً (للتجربة)

```bash
# الانتقال لمجلد المشروع
cd /path/to/season-app

# تشغيل queue worker لـ emails queue
php artisan queue:work --queue=emails,default --tries=3 --timeout=30
```

#### ب) استخدام Supervisor (موصى به للإنتاج)

Supervisor يضمن أن queue worker يعمل دائماً ويعيد تشغيله تلقائياً إذا توقف.

**1. تثبيت Supervisor (Ubuntu/Debian):**
```bash
sudo apt-get update
sudo apt-get install supervisor
```

**2. إنشاء ملف إعداد Supervisor:**

أنشئ ملف `/etc/supervisor/conf.d/season-queue-worker.conf`:

```ini
[program:season-queue-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/season-app/artisan queue:work --queue=emails,default --tries=3 --timeout=30 --sleep=3 --max-jobs=1000
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/season-app/storage/logs/queue-worker.log
stopwaitsecs=3600
```

**مهم:** استبدل `/path/to/season-app` بالمسار الصحيح لمشروعك.

**3. تفعيل الإعداد:**
```bash
# قراءة الإعدادات الجديدة
sudo supervisorctl reread

# إضافة البرنامج
sudo supervisorctl update

# بدء queue worker
sudo supervisorctl start season-queue-worker:*

# التحقق من الحالة
sudo supervisorctl status
```

**4. أوامر مفيدة:**
```bash
# إيقاف queue worker
sudo supervisorctl stop season-queue-worker:*

# إعادة تشغيل queue worker
sudo supervisorctl restart season-queue-worker:*

# عرض logs
tail -f /path/to/season-app/storage/logs/queue-worker.log
```

#### ج) استخدام Systemd (بديل لـ Supervisor)

**1. إنشاء ملف service:**

أنشئ `/etc/systemd/system/season-queue-worker.service`:

```ini
[Unit]
Description=Season App Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /path/to/season-app/artisan queue:work --queue=emails,default --tries=3 --timeout=30 --sleep=3 --max-jobs=1000

[Install]
WantedBy=multi-user.target
```

**2. تفعيل Service:**
```bash
# إعادة تحميل systemd
sudo systemctl daemon-reload

# تفعيل service
sudo systemctl enable season-queue-worker

# بدء service
sudo systemctl start season-queue-worker

# التحقق من الحالة
sudo systemctl status season-queue-worker

# عرض logs
sudo journalctl -u season-queue-worker -f
```

### 4. التحقق من عمل Queue Worker

#### أ) التحقق من Jobs في Queue:
```bash
php artisan queue:monitor emails --max=100
```

#### ب) التحقق من Jobs الفاشلة:
```bash
php artisan queue:failed
```

#### ج) إعادة محاولة Jobs الفاشلة:
```bash
php artisan queue:retry all
```

### 5. Monitoring & Logs

- **Queue Worker Logs:** `storage/logs/queue-worker.log`
- **Laravel Logs:** `storage/logs/laravel.log`
- **Failed Jobs:** يمكن عرضها عبر `php artisan queue:failed`

### 6. تنظيف Jobs القديمة (اختياري)

أضف هذا إلى `app/Console/Kernel.php` لتنظيف jobs القديمة تلقائياً:

```php
protected function schedule(Schedule $schedule)
{
    // تنظيف jobs القديمة كل يوم
    $schedule->command('queue:prune-batches --hours=48')->daily();
    $schedule->command('queue:prune-failed --hours=168')->weekly();
}
```

## ملاحظات مهمة

1. **يجب أن يكون Queue Worker يعمل دائماً** - بدون queue worker، emails لن تُرسل
2. **استخدم Supervisor أو Systemd** - يضمنان أن worker يعمل دائماً
3. **Redis أسرع من Database** - استخدم Redis queue للإنتاج
4. **راقب Logs** - لمعرفة أي مشاكل في الإرسال

## Troubleshooting

### Queue Worker لا يعمل:
```bash
# التحقق من حالة supervisor
sudo supervisorctl status

# التحقق من logs
tail -f storage/logs/queue-worker.log
tail -f storage/logs/laravel.log
```

### Jobs تبقى في الانتظار:
```bash
# التحقق من عدد jobs في queue
php artisan queue:monitor

# معالجة jobs يدوياً
php artisan queue:work --once
```

### إعادة تشغيل Queue Worker بعد تحديث الكود:
```bash
# مع supervisor
sudo supervisorctl restart season-queue-worker:*

# مع systemd
sudo systemctl restart season-queue-worker
```

