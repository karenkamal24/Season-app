# 🌐 إصلاح دعم Accept-Language Header

## ✅ التحديثات

تم تحديث `BagController` ليدعم الـ `Accept-Language` header بشكل صحيح!

---

## 🎯 كيف يعمل الآن؟

### 1️⃣ **Middleware يعمل تلقائياً**

في `bootstrap/app.php` السطر 26:
```php
$middleware->append(\App\Http\Middleware\SetLocaleFromHeader::class);
```

هذا الـ middleware بيقرأ الـ `Accept-Language` header ويضبط اللغة تلقائياً!

### 2️⃣ **BagController يستخدم LangHelper**

الآن جميع الـ responses تستخدم `LangHelper::msg()` بدلاً من النصوص الثابتة:

```php
// ❌ قبل
'message' => 'Bags retrieved successfully',
'message_ar' => 'تم جلب الحقائب بنجاح',

// ✅ بعد
'message' => LangHelper::msg('bags_retrieved'),
```

---

## 🎬 كيف تستخدمه؟

### في Postman:

#### 🇺🇸 للحصول على Response بالإنجليزية:

```http
GET /api/smart-bags
Authorization: Bearer YOUR_TOKEN
Accept-Language: en
```

**Response:**
```json
{
  "success": true,
  "message": "Bags retrieved successfully",
  "data": [...]
}
```

#### 🇸🇦 للحصول على Response بالعربية:

```http
GET /api/smart-bags
Authorization: Bearer YOUR_TOKEN
Accept-Language: ar
```

**Response:**
```json
{
  "success": true,
  "message": "تم جلب الحقائب بنجاح",
  "data": [...]
}
```

---

## 📋 جميع الرسائل المدعومة

تم إضافة الترجمات التالية في `LangHelper`:

| Key | English | العربية |
|-----|---------|---------|
| `bags_retrieved` | Bags retrieved successfully | تم جلب الحقائب بنجاح |
| `bag_retrieved` | Bag retrieved successfully | تم جلب الحقيبة بنجاح |
| `bag_created` | Bag created successfully | تم إنشاء الحقيبة بنجاح |
| `bag_updated` | Bag updated successfully | تم تحديث الحقيبة بنجاح |
| `bag_deleted` | Bag deleted successfully | تم حذف الحقيبة بنجاح |
| `bag_create_failed` | Failed to create bag | فشل في إنشاء الحقيبة |
| `bag_update_failed` | Failed to update bag | فشل في تحديث الحقيبة |
| `bag_delete_failed` | Failed to delete bag | فشل في حذف الحقيبة |
| `item_added` | Item added successfully | تم إضافة الغرض بنجاح |
| `item_updated` | Item updated successfully | تم تحديث الغرض بنجاح |
| `item_deleted` | Item deleted successfully | تم حذف الغرض بنجاح |
| `item_packed_updated` | Item packed status updated | تم تحديث حالة التحزيم |
| `item_add_failed` | Failed to add item | فشل في إضافة الغرض |
| `item_update_failed` | Failed to update item | فشل في تحديث الغرض |
| `item_delete_failed` | Failed to delete item | فشل في حذف الغرض |
| `item_packed_toggle_failed` | Failed to toggle packed status | فشل في تغيير حالة التحزيم |

---

## 🔧 الملفات المُعدّلة

### 1. **`app/Http/Controllers/Api/BagController.php`**

- ✅ إضافة `use App\Helpers\LangHelper;`
- ✅ تحديث جميع الـ responses (16 response)
- ✅ إزالة `message_ar` (لم يعد مطلوباً)

### 2. **`app/Helpers/LangHelper.php`**

- ✅ إضافة 16 رسالة جديدة
- ✅ دعم العربية والإنجليزية

### 3. **`bootstrap/app.php`**

- ✅ الـ middleware موجود مسبقاً وشغال!

---

## 🎯 أمثلة عملية

### مثال 1: Get All Bags

```bash
# بالعربية
curl -X GET "http://localhost:8000/api/smart-bags" \
  -H "Authorization: Bearer TOKEN" \
  -H "Accept-Language: ar"

# Response
{
  "success": true,
  "message": "تم جلب الحقائب بنجاح",
  "data": [...]
}
```

```bash
# بالإنجليزية
curl -X GET "http://localhost:8000/api/smart-bags" \
  -H "Authorization: Bearer TOKEN" \
  -H "Accept-Language: en"

# Response
{
  "success": true,
  "message": "Bags retrieved successfully",
  "data": [...]
}
```

### مثال 2: Create Bag

```bash
# بالعربية
curl -X POST "http://localhost:8000/api/smart-bags" \
  -H "Authorization: Bearer TOKEN" \
  -H "Accept-Language: ar" \
  -H "Content-Type: application/json" \
  -d '{...}'

# Response
{
  "success": true,
  "message": "تم إنشاء الحقيبة بنجاح",
  "data": {...}
}
```

### مثال 3: Add Item

```bash
# بالعربية
curl -X POST "http://localhost:8000/api/smart-bags/1/items" \
  -H "Authorization: Bearer TOKEN" \
  -H "Accept-Language: ar" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "لابتوب",
    "weight": 2.3,
    "item_category_id": 3
  }'

# Response
{
  "success": true,
  "message": "تم إضافة الغرض بنجاح",
  "data": {...}
}
```

---

## 📱 في Postman

### تحديث Headers:

في كل طلب، أضف:

```
Accept-Language: ar   (للعربية)
Accept-Language: en   (للإنجليزية)
```

أو استخدم Environment Variable:

```json
{
  "key": "language",
  "value": "ar"
}
```

ثم في Headers:
```
Accept-Language: {{language}}
```

---

## 🎨 الفرق الواضح

### ❌ قبل التحديث:

```json
{
  "success": true,
  "message": "Bags retrieved successfully",
  "message_ar": "تم جلب الحقائب بنجاح",
  "data": [...]
}
```
👆 يرسل الاتنين دائماً!

### ✅ بعد التحديث:

**مع `Accept-Language: en`:**
```json
{
  "success": true,
  "message": "Bags retrieved successfully",
  "data": [...]
}
```

**مع `Accept-Language: ar`:**
```json
{
  "success": true,
  "message": "تم جلب الحقائب بنجاح",
  "data": [...]
}
```
👆 يرسل رسالة واحدة فقط حسب اللغة!

---

## 🔄 اللغة الافتراضية

إذا لم ترسل `Accept-Language` header:
- اللغة الافتراضية: **الإنجليزية** (`en`)

---

## ✨ جاهز للاستخدام!

الآن جميع endpoints تدعم اللغتين! 🎉

### جرّب الآن في Postman:

1. افتح أي طلب
2. أضف Header: `Accept-Language: ar`
3. اضغط Send
4. شاهد الرسالة بالعربية! 🇸🇦

---

**ملاحظة:** هذا التحديث يعمل على جميع endpoints في `BagController`:
- ✅ Get All Bags
- ✅ Get Bag Details
- ✅ Create Bag
- ✅ Update Bag
- ✅ Delete Bag
- ✅ Add Item
- ✅ Update Item
- ✅ Delete Item
- ✅ Toggle Packed

---

**آخر تحديث:** يناير 2025  
**الإصدار:** 2.0 - دعم كامل للغات


