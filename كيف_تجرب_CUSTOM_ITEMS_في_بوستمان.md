# كيف تجرب Custom Items في Postman

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

### 2️⃣ جرب إضافة Custom Item (عنصر مخصص)

1. ابحث عن **"2. Travel Bag Management → Add Custom Item to Bag"**
2. تأكد أن الـ Method = **POST**
3. تأكد أن الـ URL = `{{base_url}}/travel-bag/add-item`
   - الـ URL كامل: `http://localhost:8000/api/travel-bag/add-item`

4. في الـ **Body** (اختر raw + JSON):
   ```json
   {
       "custom_item_name": "محمول شخصي",
       "custom_weight": 2.5,
       "quantity": 1,
       "bag_type_id": 1
   }
   ```

5. **الأحكام المهمة:**
   - ✅ `custom_item_name` = **مطلوب** (اسم العنصر المخصص)
   - ✅ `custom_weight` = **مطلوب** (الوزن بالكيلوجرام، رقم موجب)
   - ⚠️ `quantity` = **اختياري** (افتراضي: 1)
   - ⚠️ `bag_type_id` = **اختياري** (افتراضي: 1)

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
    "message": "تمت إضافة العنصر إلى الحقيبة بنجاح",
    "data": {
        "item_added": {
            "item_id": null,
            "custom_item_name": "محمول شخصي",
            "name": "محمول شخصي",
            "category": null,
            "quantity": 1,
            "weight_per_item": 2.5,
            "total_weight": 2.5,
            "icon": null,
            "is_custom": true
        },
        "bag_type_id": 1,
        "bag_name": "الحقيبة الرئيسية",
        "updated_bag": {
            "current_weight": 2.5,
            "max_weight": 25.0,
            "weight_percentage": 10.0,
            "total_items": 1
        }
    }
}
```

---

### 4️⃣ أمثلة للاختبار:

#### مثال 1: إضافة محمول شخصي
```json
{
    "custom_item_name": "محمول شخصي",
    "custom_weight": 2.5
}
```

#### مثال 2: إضافة كتب
```json
{
    "custom_item_name": "كتب",
    "custom_weight": 3.0,
    "quantity": 2
}
```

#### مثال 3: إضافة شاحن لابتوب
```json
{
    "custom_item_name": "شاحن لابتوب",
    "custom_weight": 0.5,
    "quantity": 1,
    "bag_type_id": 1
}
```

#### مثال 4: إضافة جاكيت شتوي
```json
{
    "custom_item_name": "جاكيت شتوي",
    "custom_weight": 1.2,
    "quantity": 1
}
```

#### مثال 5: إضافة أدوات كهربائية
```json
{
    "custom_item_name": "مكواة شعر",
    "custom_weight": 0.8,
    "quantity": 1
}
```

---

### 5️⃣ شوف العناصر المضافة

1. ابحث عن **"2. Travel Bag Management → Get Bag Items"**
2. اضغط **Send**
3. هتشوف جميع العناصر (العادية والمخصصة):

```json
{
    "success": true,
    "data": {
        "items": [
            {
                "item_id": 1,
                "custom_item_name": null,
                "name": "قميص",
                "category": "ملابس",
                "quantity": 2,
                "weight_per_item": 0.3,
                "total_weight": 0.6,
                "icon": "shirt-icon.png",
                "is_custom": false
            },
            {
                "item_id": null,
                "custom_item_name": "محمول شخصي",
                "name": "محمول شخصي",
                "category": null,
                "quantity": 1,
                "weight_per_item": 2.5,
                "total_weight": 2.5,
                "icon": null,
                "is_custom": true
            }
        ]
    }
}
```

---

### 6️⃣ الأخطاء المحتملة:

#### ❌ خطأ 401 Unauthorized
**السبب:** ما عندكش token أو الـ token منتهي

**الحل:**
- سجل دخول تاني
- تأكد من الـ Authorization header

#### ❌ خطأ 422 Validation Error - `item_id_or_name_required`
**السبب:** ما بعتتش `item_id` ولا `custom_item_name`

**الحل:**
- أرسل إما `item_id` (للعناصر العادية) أو `custom_item_name` (للعناصر المخصصة)
- مش ممكن ترسل الاتنين في نفس الوقت

**مثال الخطأ:**
```json
{
    "success": false,
    "error": {
        "custom_item_name": ["The custom item name field is required when item id is not present."]
    }
}
```

#### ❌ خطأ 422 Validation Error - `custom_weight_required_for_custom_item`
**السبب:** بعت `custom_item_name` بدون `custom_weight`

**الحل:**
- أرسل `custom_weight` مع `custom_item_name`
- الوزن لازم يكون رقم موجب

**مثال الخطأ:**
```json
{
    "success": false,
    "error": {
        "custom_weight": ["The custom weight field is required when custom item name is present."]
    }
}
```

#### ❌ خطأ 400 - `cannot_add_more_weight_exceeded`
**السبب:** الوزن الإجمالي هيتجاوز الحد الأقصى للحقيبة

**الحل:**
- قلل الكمية
- قلل الوزن
- أو زود الحد الأقصى للحقيبة أولاً

---

### 7️⃣ ترتيب الاختبار الموصى به:

1. ✅ Login (Get Token)
2. ✅ Get Travel Bag Details (شوف الحالة الحالية)
3. ✅ Add Custom Item to Bag (جرب تضيف عنصر مخصص)
4. ✅ Get Bag Items (شوف العناصر المضافة)
5. ✅ Add Item to Bag (Regular) (جرب تضيف عنصر عادي)
6. ✅ Get Bag Items (شوف كل العناصر)

---

### 8️⃣ الفرق بين Regular Item و Custom Item:

#### Regular Item (عنصر عادي):
```json
{
    "item_id": 1,
    "quantity": 2
}
```
- من القائمة الموجودة
- له فئة (category)
- له أيقونة (icon)
- `is_custom: false`

#### Custom Item (عنصر مخصص):
```json
{
    "custom_item_name": "محمول شخصي",
    "custom_weight": 2.5,
    "quantity": 1
}
```
- مخصص من المستخدم
- بدون فئة
- بدون أيقونة
- `is_custom: true`

---

### 9️⃣ نصائح مهمة:

- 🔐 **الـ Token مهم:** بدون token ما هتقدر تجرب
- 📝 **الـ custom_item_name لازم نص:** "محمول شخصي"، "كتب"، إلخ
- ⚖️ **الـ custom_weight لازم رقم:** 2.5، 3.0، 0.5 (مش نص)
- 🎯 **إما item_id أو custom_item_name:** مش الاتنين في نفس الوقت
- 🔄 **التكرار:** لو أضفت نفس الـ custom item مرتين، الكمية هتزيد تلقائياً
- ⚠️ **الوزن مطلوب:** للعناصر المخصصة، الوزن مطلوب دائماً

---

## ملخص سريع:

```
POST http://localhost:8000/api/travel-bag/add-item

Headers:
  Authorization: Bearer {{token}}
  Content-Type: application/json

Body (Custom Item):
{
    "custom_item_name": "محمول شخصي",
    "custom_weight": 2.5,
    "quantity": 1,
    "bag_type_id": 1
}
```

**ده كل حاجة! جرب وخلينا نشوف النتيجة 🚀**

---

## أمثلة إضافية:

### مثال: إضافة عدة عناصر مخصصة

1. **محمول شخصي:**
```json
{
    "custom_item_name": "محمول شخصي",
    "custom_weight": 2.5
}
```

2. **كتب:**
```json
{
    "custom_item_name": "كتب",
    "custom_weight": 3.0,
    "quantity": 2
}
```

3. **شاحن لابتوب:**
```json
{
    "custom_item_name": "شاحن لابتوب",
    "custom_weight": 0.5
}
```

4. **جاكيت شتوي:**
```json
{
    "custom_item_name": "جاكيت شتوي",
    "custom_weight": 1.2
}
```

بعد كده، شوف الحقيبة كاملة في **Get Bag Items** 🎉

