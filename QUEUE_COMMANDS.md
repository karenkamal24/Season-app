# أوامر تشغيل Queue Worker

## للتطوير (Development) - Windows

### 1. تشغيل Queue Worker (موصى به)
```bash
php artisan queue:work --queue=emails,default --tries=3 --timeout=30
```

هذا الأمر:
- ✅ يعالج jobs من queue `emails` أولاً ثم `default`
- ✅ يحاول 3 مرات في حالة الفشل
- ✅ timeout 30 ثانية لكل job
- ✅ يعمل بشكل مستمر حتى تقوم بإيقافه بـ `Ctrl+C`

### 2. تشغيل Queue Worker مع Auto-Restart (للتطوير)
```bash
php artisan queue:listen --queue=emails,default --tries=3 --timeout=30
```

`queue:listen` يعيد تحميل الكود تلقائياً عند تغيير الملفات (أبطأ من `queue:work`)

### 3. معالجة Job واحد فقط (للتجربة)
```bash
php artisan queue:work --queue=emails,default --once
```

### 4. تشغيل مع Verbose Output (لرؤية تفاصيل أكثر)
```bash
php artisan queue:work --queue=emails,default --tries=3 --timeout=30 -v
```

## للتطوير (Development) - Linux/Mac

نفس الأوامر، لكن يمكنك تشغيلها في background:
```bash
php artisan queue:work --queue=emails,default --tries=3 --timeout=30 > storage/logs/queue-worker.log 2>&1 &
```

## للإنتاج (Production)

### استخدام Supervisor (موصى به)

1. **تثبيت Supervisor:**
```bash
sudo apt-get update
sudo apt-get install supervisor
```

2. **إنشاء ملف الإعداد:**
أنشئ `/etc/supervisor/conf.d/season-queue-worker.conf`:

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

**مهم:** استبدل `/path/to/season-app` بالمسار الصحيح

3. **تفعيل Supervisor:**
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start season-queue-worker:*
```

4. **التحقق من الحالة:**
```bash
sudo supervisorctl status
```

5. **أوامر مفيدة:**
```bash
# إيقاف
sudo supervisorctl stop season-queue-worker:*

# إعادة تشغيل
sudo supervisorctl restart season-queue-worker:*

# عرض logs
tail -f /path/to/season-app/storage/logs/queue-worker.log
```

## استخدام Systemd (بديل)

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

ثم:
```bash
sudo systemctl daemon-reload
sudo systemctl enable season-queue-worker
sudo systemctl start season-queue-worker
sudo systemctl status season-queue-worker
```

## أوامر مفيدة أخرى

### عرض Jobs المعلقة
```bash
php artisan queue:failed
```

### إعادة محاولة Jobs الفاشلة
```bash
php artisan queue:retry all
```

### مسح Jobs الفاشلة
```bash
php artisan queue:flush
```

### فحص حالة الـ Queue
```bash
# عرض عدد jobs في الانتظار
php artisan queue:work --once --queue=emails,default
```

## ملاحظات مهمة

1. **Queue Connection:** تأكد من وجود `QUEUE_CONNECTION=database` في `.env`
2. **Database Migration:** تأكد من تشغيل migrations لإنشاء جدول `jobs`
3. **Logs:** راقب `storage/logs/laravel.log` و `storage/logs/queue-worker.log`
4. **Memory:** Queue worker قد يستهلك memory مع الوقت، استخدم `--max-jobs=1000` لإعادة التشغيل التلقائي

## للمطورين

يمكنك استخدام `composer dev` لتشغيل كل شيء معاً:
```bash
composer dev
```

هذا سيشغل:
- Laravel server
- Queue worker
- Log viewer (pail)
- Vite dev server





