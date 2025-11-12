# كيف تجرب Update Max Weight في Postman

## الخطوات بالتفصيل:

### 1️⃣ أول حاجة: سجل دخول (Get Token)

1. افتح Postman
2. ابحث عن **"1. Authentication → Login (Get Token)"**
3. غير البيانات في Body:
   ```json
   {
       "email": "your_email@example.com",
       "password": "your_password"
   }
   ```
4. اضغط **Send**
5. الـ token هيتحفظ تلقائياً ✅

---

### 2️⃣ جرب Update Max Weight

1. ابحث عن **"2. Travel Bag Management → Update Maximum Weight"**
2. تأكد أن الـ Method = **PUT**
3. تأكد أن الـ URL = `{{base_url}}/travel-bag/max-weight`
   - الـ URL كامل: `http://localhost:8000/api/travel-bag/max-weight`

4. في الـ **Body** (اختر raw + JSON):
   ```json
   {
       "max_weight": 30.5,
       "weight_unit": "kg",
       "bag_type_id": 1
   }
   ```

5. **الأحكام المهمة:**
   - ✅ `max_weight` = **مطلوب** (رقم، مثلاً: 25 أو 30.5)
   - ⚠️ `weight_unit` = **اختياري** (إما "kg" أو "lb")
   - ⚠️ `bag_type_id` = **اختياري** (لو ما بعتهش، هيستخدم 1 افتراضياً)

6. تأكد من الـ **Headers**:
   - `Authorization`: `Bearer {{token}}`
   - `Content-Type`: `application/json`
   - `Accept`: `application/json`

7. اضغط **Send**

---

### 3️⃣ النتيجة المتوقعة (نجاح):

```json
{
    "success": true,
    "message": "تم تحديث الوزن الأقصى بنجاح",
    "data": {
        "max_weight": 30.5,
        "current_weight": 0.0,
        "weight_percentage": 0.0
    }
}
```

---

### 4️⃣ أمثلة للاختبار:

#### مثال 1: الحد الأدنى (مطلوب فقط)
```json
{
    "max_weight": 25
}
```

#### مثال 2: مع وحدة الوزن
```json
{
    "max_weight": 50,
    "weight_unit": "kg"
}
```

#### مثال 3: مع نوع الحقيبة
```json
{
    "max_weight": 30,
    "weight_unit": "kg",
    "bag_type_id": 1
}
```

#### مثال 4: باوند (lb)
```json
{
    "max_weight": 50,
    "weight_unit": "lb"
}
```

---

### 5️⃣ الأخطاء المحتملة:

#### ❌ خطأ 401 Unauthorized
**السبب:** ما عندكش token أو الـ token منتهي

**الحل:**
- سجل دخول تاني
- تأكد من الـ Authorization header

#### ❌ خطأ 422 Validation Error
**السبب:** البيانات غلط

**أمثلة:**
```json
{
    "success": false,
    "error": {
        "max_weight": ["The max weight field is required."]
    }
}
```

**الحل:**
- تأكد إن `max_weight` موجود ورقم
- تأكد إن `weight_unit` إما "kg" أو "lb" (لو بعتته)
- تأكد إن `bag_type_id` موجود في قاعدة البيانات (لو بعتته)

#### ❌ خطأ 404 Not Found
**السبب:** الـ route مش موجود أو السيرفر مش شغال

**الحل:**
- شغل السيرفر: `php artisan serve`
- تأكد من الـ URL: `http://localhost:8000/api/travel-bag/max-weight`

---

### 6️⃣ ترتيب الاختبار الموصى به:

1. ✅ Login (Get Token)
2. ✅ Get Travel Bag Details (شوف الحالة الحالية)
3. ✅ Update Max Weight (جرب التحديث)
4. ✅ Get Travel Bag Details (شوف التغييرات)

---

### 7️⃣ نصائح مهمة:

- 🔐 **الـ Token مهم:** بدون token ما هتقدر تجرب
- 📝 **الـ max_weight لازم رقم:** 25 أو 30.5 (مش نص)
- 🎯 **الـ bag_type_id اختياري:** لو ما بعتهش، هيستخدم 1
- ⚖️ **الـ weight_unit اختياري:** لو ما بعتهش، هيستخدم الافتراضي
- 🔄 **جرب قيم مختلفة:** 10, 20, 50, 100

---

## ملخص سريع:

```
PUT http://localhost:8000/api/travel-bag/max-weight

Headers:
  Authorization: Bearer {{token}}
  Content-Type: application/json

Body:
{
    "max_weight": 30,
    "weight_unit": "kg",
    "bag_type_id": 1
}
```

**ده كل حاجة! جرب وخلينا نشوف النتيجة 🚀**

