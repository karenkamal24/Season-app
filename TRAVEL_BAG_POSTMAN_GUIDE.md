# دليل اختبار Travel Bag APIs في Postman

## خطوات الإعداد

### 1. استيراد Collection و Environment

1. افتح Postman
2. اضغط على **Import** في الأعلى
3. استورد الملفات التالية:
   - `Travel_Bag_API_Collection.postman_collection.json`
   - `Travel_Bag_API_Environment.postman_environment.json`

### 2. اختيار Environment

- في الزاوية اليمنى العلوية، اختر **Season Travel Bag API - Environment**
- تأكد من أن `base_url` = `http://localhost:8000/api`

### 3. تشغيل السيرفر

```bash
php artisan serve
```

### 4. التأكد من وجود البيانات

قبل البدء، تأكد من تشغيل الـ Seeders:

```bash
php artisan db:seed --class=BagTypesSeeder
php artisan db:seed --class=ItemCategoriesSeeder
php artisan db:seed --class=ItemsSeeder
php artisan db:seed --class=PackingTipsSeeder
```

## خطوات الاختبار

### الخطوة 1: تسجيل الدخول (Authentication)

1. افتح **1. Authentication → Login (Get Token)**
2. عدّل بيانات الدخول:
   ```json
   {
       "email": "your_email@example.com",
       "password": "your_password"
   }
   ```
3. أرسل الطلب
4. سيتم حفظ الـ token تلقائياً في Collection Variables

### الخطوة 2: Travel Bag Management

#### 2.1 Get Travel Bag Details
- افتح **2. Travel Bag Management → Get Travel Bag Details**
- أرسل الطلب
- يجب أن ترى تفاصيل الشنطة (فارغة في البداية)

#### 2.2 Update Maximum Weight
- افتح **Update Maximum Weight**
- عدّل `max_weight` إذا أردت
- أرسل الطلب

### الخطوة 3: Item Management

#### 3.1 Get All Categories
- افتح **3. Item Management → Get All Categories**
- أرسل الطلب
- سيتم حفظ أول `category_id` تلقائياً

#### 3.2 Get Items by Category
- افتح **Get Items by Category**
- أرسل الطلب
- سترى جميع العناصر في الفئة
- سيتم حفظ أول `item_id` تلقائياً

#### 3.3 Get Single Item Details
- افتح **Get Single Item Details**
- أرسل الطلب
- سترى تفاصيل العنصر

### الخطوة 4: إضافة عناصر للشنطة

#### 4.1 Add Item to Bag
- افتح **2. Travel Bag Management → Add Item to Bag**
- عدّل `item_id` ليكون ID عنصر موجود
- أرسل الطلب
- يجب أن ترى أن العنصر تم إضافته

#### 4.2 Get Bag Items
- افتح **Get Bag Items**
- أرسل الطلب
- سترى جميع العناصر الموجودة في الشنطة

#### 4.3 Remove Item from Bag
- افتح **Remove Item from Bag**
- أرسل الطلب
- سيتم حذف العنصر أو تقليل الكمية

### الخطوة 5: Reminder Management

#### 5.1 Create Reminder
- افتح **4. Reminder Management → Create Reminder**
- عدّل البيانات:
  ```json
  {
      "title": "Pack suitcase",
      "date": "2025-10-06",
      "time": "10:00",
      "recurrence": "once",
      "notes": "Don't forget passport"
  }
  ```
- أرسل الطلب
- سيتم حفظ `reminder_id` تلقائياً

#### 5.2 Get All Reminders
- افتح **Get All Reminders**
- أرسل الطلب

#### 5.3 Update Reminder
- افتح **Update Reminder**
- عدّل البيانات
- أرسل الطلب

#### 5.4 Delete Reminder
- افتح **Delete Reminder**
- أرسل الطلب

### الخطوة 6: Packing Tips

#### 6.1 Get Packing Tips
- افتح **5. Packing Tips → Get Packing Tips**
- أرسل الطلب
- سترى قائمة النصائح

### الخطوة 7: AI Suggestions

#### 7.1 Get AI Suggestions
- افتح **6. AI Suggestions → Get AI Suggestions**
- يمكنك تعديل `destination`
- أرسل الطلب

#### 7.2 Add Suggested Item to Bag
- افتح **Add Suggested Item to Bag**
- أرسل الطلب لإضافة عنصر مقترح

## ملاحظات مهمة

1. **Authentication**: جميع الـ endpoints تحتاج token في الـ header
   - يتم حفظه تلقائياً بعد تسجيل الدخول
   - إذا انتهت صلاحية الـ token، سجل دخول مرة أخرى

2. **Variables**: 
   - `token` - يتم حفظه تلقائياً بعد Login
   - `item_id` - يتم حفظه تلقائياً من Get Items by Category
   - `category_id` - يتم حفظه تلقائياً من Get All Categories
   - `reminder_id` - يتم حفظه تلقائياً من Create Reminder

3. **Error Responses**: في حالة حدوث خطأ، ستحصل على response بهذا الشكل:
   ```json
   {
       "success": false,
       "error": {
           "code": "ERROR_CODE",
           "message": "Error message"
       }
   }
   ```

4. **Success Responses**: في حالة النجاح:
   ```json
   {
       "success": true,
       "data": { ... }
   }
   ```

## Troubleshooting

### خطأ 401 Unauthorized
- تأكد من تسجيل الدخول أولاً
- تأكد من أن الـ token موجود وصحيح

### خطأ 404 Not Found
- تأكد من أن السيرفر يعمل (`php artisan serve`)
- تأكد من أن الـ `base_url` صحيح
- تأكد من أن الـ route موجود

### خطأ 500 Internal Server Error
- تحقق من الـ logs: `storage/logs/laravel.log`
- تأكد من تشغيل الـ migrations والـ seeders

### لا توجد بيانات
- تأكد من تشغيل الـ seeders:
  ```bash
  php artisan db:seed --class=BagTypesSeeder
  php artisan db:seed --class=ItemCategoriesSeeder
  php artisan db:seed --class=ItemsSeeder
  php artisan db:seed --class=PackingTipsSeeder
  ```

## ترتيب الاختبار الموصى به

1. ✅ تسجيل الدخول
2. ✅ Get All Categories
3. ✅ Get Items by Category
4. ✅ Get Travel Bag Details
5. ✅ Add Item to Bag
6. ✅ Get Bag Items
7. ✅ Create Reminder
8. ✅ Get All Reminders
9. ✅ Get Packing Tips
10. ✅ Get AI Suggestions

