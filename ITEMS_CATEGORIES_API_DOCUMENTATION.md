# Items Categories API Documentation
## دليل API شامل لتصنيفات العناصر التي يمكن إضافتها في الحقيبة

---

## 📋 جدول المحتويات

1. [نظرة عامة](#نظرة-عامة)
2. [المصادقة](#المصادقة)
3. [Endpoints](#endpoints)
4. [بنية البيانات](#بنية-البيانات)
5. [أمثلة الاستخدام](#أمثلة-الاستخدام)
6. [أكواد الأخطاء](#أكواد-الأخطاء)

---

## نظرة عامة

نظام تصنيفات العناصر يسمح للمستخدمين بـ:
- الحصول على قائمة بجميع التصنيفات المتاحة (Boarding, Funds, Personal Essentials, Entertainment, Electronics, Clothing, Toiletries, etc.)
- الحصول على جميع العناصر في تصنيف معين
- استخدام هذه العناصر لإضافتها في شنطة السفر

جميع التصنيفات والعناصر يجب أن تكون نشطة (`is_active = true`) لكي تظهر في النتائج.

---

## المصادقة

جميع الـ endpoints تتطلب مصادقة باستخدام Bearer Token:

```
Authorization: Bearer YOUR_TOKEN
```

---

## Endpoints

### 1. الحصول على جميع التصنيفات

**GET** `/api/categories`

يعيد قائمة بجميع تصنيفات العناصر النشطة المتاحة.

#### Headers:
```
Authorization: Bearer YOUR_TOKEN
Accept-Language: ar|en (اختياري - يحدد لغة النتائج)
```

#### Response (Success - 200):
```json
{
  "status": 200,
  "message": "تم جلب فئات العناصر بنجاح",
  "meta": null,
  "data": [
    {
      "category_id": 1,
      "name": "الصعود",
      "icon": "https://cdn-icons-png.flaticon.com/512/190/190601.png"
    },
    {
      "category_id": 2,
      "name": "أموال",
      "icon": "https://cdn-icons-png.flaticon.com/512/2331/2331943.png"
    },
    {
      "category_id": 3,
      "name": "أساسيات شخصية",
      "icon": "https://cdn-icons-png.flaticon.com/512/706/706164.png"
    },
    {
      "category_id": 4,
      "name": "ترفيه",
      "icon": "https://cdn-icons-png.flaticon.com/512/727/727245.png"
    },
    {
      "category_id": 5,
      "name": "إلكترونيات",
      "icon": "https://cdn-icons-png.flaticon.com/512/1041/1041916.png"
    },
    {
      "category_id": 6,
      "name": "ملابس",
      "icon": "https://cdn-icons-png.flaticon.com/512/892/892458.png"
    },
    {
      "category_id": 7,
      "name": "مستلزمات النظافة",
      "icon": "https://cdn-icons-png.flaticon.com/512/2927/2927347.png"
    }
  ]
}
```

#### Response Fields:
- `category_id`: معرف التصنيف (integer)
- `name`: اسم التصنيف (string) - يعتمد على اللغة المحددة في `Accept-Language`
- `icon`: رابط الأيقونة (string) - يمكن أن يكون URL خارجي أو مسار محلي

---

### 2. الحصول على العناصر حسب التصنيف

**GET** `/api/categories/items?category_id={category_id}`

يعيد قائمة بجميع العناصر النشطة في التصنيف المحدد.

#### Headers:
```
Authorization: Bearer YOUR_TOKEN
Accept-Language: ar|en (اختياري - يحدد لغة النتائج)
```

#### Query Parameters:
- `category_id` (required): معرف التصنيف (integer, min: 10, max: 5000)
  - يجب أن يكون التصنيف موجوداً ونشطاً (`is_active = true`)

#### Response (Success - 200):
```json
{
  "status": 200,
  "message": "تم جلب العناصر بنجاح",
  "meta": null,
  "data": [
    {
      "item_id": 1,
      "name": "جواز السفر",
      "default_weight": 0.2,
      "weight_unit": "kg",
      "category_id": 1,
      "description": "جواز السفر أو وثيقة السفر"
    },
    {
      "item_id": 2,
      "name": "تذكرة الطيران",
      "default_weight": 0.01,
      "weight_unit": "kg",
      "category_id": 1,
      "description": "تذكرة الطيران أو تأكيد الحجز"
    },
    {
      "item_id": 3,
      "name": "بطاقة الصعود",
      "default_weight": 0.01,
      "weight_unit": "kg",
      "category_id": 1,
      "description": "بطاقة الصعود إلى الطائرة"
    }
  ]
}
```

#### Response Fields:
- `item_id`: معرف العنصر (integer)
- `name`: اسم العنصر (string) - يعتمد على اللغة المحددة
- `default_weight`: الوزن الافتراضي (decimal: 2)
- `weight_unit`: وحدة الوزن (string) - عادة "kg"
- `category_id`: معرف التصنيف (integer)
- `description`: وصف العنصر (string|null) - يعتمد على اللغة المحددة

---

---

## بنية البيانات

### ItemCategory (تصنيف العنصر)

```json
{
  "category_id": 1,
  "name": "الصعود",
  "icon": "https://cdn-icons-png.flaticon.com/512/190/190601.png"
}
```

**الحقول:**
- `category_id` (integer): معرف التصنيف
- `name` (string): اسم التصنيف (بالعربية أو الإنجليزية حسب `Accept-Language`)
- `icon` (string|null): رابط الأيقونة

---

### Item (عنصر)

```json
{
  "item_id": 1,
  "name": "جواز السفر",
  "default_weight": 0.2,
  "weight_unit": "kg",
  "category_id": 1,
  "description": "جواز السفر أو وثيقة السفر"
}
```

**الحقول:**
- `item_id` (integer): معرف العنصر
- `name` (string): اسم العنصر (بالعربية أو الإنجليزية حسب `Accept-Language`)
- `default_weight` (decimal: 2): الوزن الافتراضي بالكيلوجرام
- `weight_unit` (string): وحدة الوزن (عادة "kg")
- `category_id` (integer): معرف التصنيف الذي ينتمي إليه العنصر
- `description` (string|null): وصف العنصر (بالعربية أو الإنجليزية حسب `Accept-Language`)

---

## أمثلة الاستخدام

### مثال 1: الحصول على جميع التصنيفات

```bash
curl -X GET "https://seasonksa.com/api/categories" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept-Language: ar"
```

**Response:**
```json
{
  "status": 200,
  "message": "تم جلب فئات العناصر بنجاح",
  "data": [
    {
      "category_id": 1,
      "name": "الصعود",
      "icon": "https://cdn-icons-png.flaticon.com/512/190/190601.png"
    },
    ...
  ]
}
```

---

### مثال 2: الحصول على عناصر تصنيف معين

```bash
curl -X GET "https://seasonksa.com/api/categories/items?category_id=1" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept-Language: ar"
```

**Response:**
```json
{
  "status": 200,
  "message": "تم جلب العناصر بنجاح",
  "data": [
    {
      "item_id": 1,
      "name": "جواز السفر",
      "default_weight": 0.2,
      "weight_unit": "kg",
      "category_id": 1,
      "description": "جواز السفر أو وثيقة السفر"
    },
    ...
  ]
}
```

---

## أكواد الأخطاء

### 400 Bad Request
```json
{
  "status": 400,
  "message": "التصنيف غير موجود.",
  "meta": null,
  "data": []
}
```

**الأسباب المحتملة:**
- `category_id` غير موجود في الطلب
- `category_id` غير موجود في قاعدة البيانات
- `category_id` موجود لكن التصنيف غير نشط (`is_active = false`)

---

### 401 Unauthorized
```json
{
  "status": 401,
  "message": "Unauthenticated.",
  "meta": null,
  "data": []
}
```

**السبب:**
- عدم إرسال Bearer Token
- Token غير صحيح أو منتهي الصلاحية

---

### 404 Not Found
```json
{
  "status": 404,
  "message": "العنصر غير موجود",
  "meta": null,
  "data": []
}
```

**السبب:**
- العنصر المطلوب غير موجود في قاعدة البيانات
- العنصر غير نشط (`is_active = false`)

---

### 500 Internal Server Error
```json
{
  "status": 500,
  "message": "خطأ في الخادم",
  "meta": null,
  "data": []
}
```

---

## ملاحظات مهمة

### 1. اللغة (Language)
- استخدم header `Accept-Language: ar` للحصول على النتائج بالعربية
- استخدم header `Accept-Language: en` للحصول على النتائج بالإنجليزية
- إذا لم تحدد اللغة، سيتم استخدام اللغة الافتراضية للتطبيق

### 2. التصنيفات النشطة فقط
- فقط التصنيفات النشطة (`is_active = true`) تظهر في النتائج
- إذا كان التصنيف غير نشط، ستحصل على خطأ 400

### 3. العناصر النشطة فقط
- فقط العناصر النشطة (`is_active = true`) تظهر في النتائج
- العناصر مرتبة حسب `sort_order`

### 4. استخدام العناصر في Travel Bag
- يمكن استخدام `item_id` لإضافة العنصر إلى شنطة السفر عبر endpoint:
  ```
  POST /api/travel-bag/add-item
  Body: {
    "item_id": 1,
    "quantity": 1,
    "bag_type_id": 1
  }
  ```

### 5. التحقق من الصحة
- `category_id` يجب أن يكون موجوداً في قاعدة البيانات
- التصنيف يجب أن يكون نشطاً (`is_active = true`)
- `category_id` يجب أن يكون integer

---

## أمثلة Postman

### Get All Categories
```
GET {{base_url}}/api/categories
Authorization: Bearer {{token}}
Accept-Language: ar
```

### Get Items by Category
```
GET {{base_url}}/api/categories/items?category_id=1
Authorization: Bearer {{token}}
Accept-Language: ar
```

---

## Integration with Travel Bag

بعد الحصول على العناصر من هذه الـ API، يمكنك إضافتها إلى شنطة السفر:

```bash
# 1. Get categories
GET /api/categories

# 2. Get items in a category
GET /api/categories/items?category_id=1

# 3. Add item to travel bag
POST /api/travel-bag/add-item
Body: {
  "item_id": 1,
  "quantity": 1,
  "bag_type_id": 1
}
```

---

## التصنيفات المتاحة (Default Categories)

1. **Boarding** (الصعود) - ID: 1
2. **Funds** (أموال) - ID: 2
3. **Personal Essentials** (أساسيات شخصية) - ID: 3
4. **Entertainment** (ترفيه) - ID: 4
5. **Electronics** (إلكترونيات) - ID: 5
6. **Clothing** (ملابس) - ID: 6
7. **Toiletries** (مستلزمات النظافة) - ID: 7

*ملاحظة: معرفات التصنيفات قد تختلف حسب قاعدة البيانات*

---

## المراجع

- [Travel Bag API Documentation](./TRAVEL_BAG_API_DOCUMENTATION.md)
- [API Base URL](https://seasonksa.com/api)

---

**آخر تحديث:** 2025-01-15

