# 📚 توثيق API - التعبئة الذكية بالذكاء الاصطناعي
## دليل سريع للـ 3 APIs الرئيسية

---

## 📋 الـ APIs

| الطريقة | الـ Endpoint | الوصف |
|---------|--------------|-------|
| `GET` | `/api/smart-bags/ai/categories` | الحصول على فئات التعبئة من AI |
| `GET` | `/api/smart-bags/ai/suggest-items?category={name}` | الحصول على اقتراحات العناصر من AI |
| `POST` | `/api/smart-bags/{bagId}/ai/add-item` | إضافة عنصر من AI للحقيبة |

---

## 🔐 المصادقة

جميع الـ endpoints تحتاج Bearer token:

```
Authorization: Bearer YOUR_API_TOKEN
```

---

## 🌐 اللغة

استخدم `Accept-Language` header:

```
Accept-Language: ar  (عربي)
Accept-Language: en  (إنجليزي)
```

---

## 1️⃣ الحصول على الفئات من AI

### الطلب
```
GET {{url}}/api/smart-bags/ai/categories
```

### Headers
```
Authorization: Bearer YOUR_TOKEN
Accept-Language: ar
```

### الاستجابة
```json
{
  "success": true,
  "message": "AI categories generated successfully",
  "data": {
    "categories": [
      { "name": "الملابس" },
      { "name": "مستلزمات النظافة" },
      { "name": "الإلكترونيات" },
      { "name": "المستندات" },
      { "name": "الأدوية" },
      { "name": "الإكسسوارات" },
      { "name": "الطعام والوجبات الخفيفة" },
      { "name": "الترفيه" }
    ],
    "language": "ar"
  }
}
```

---

## 2️⃣ الحصول على اقتراحات العناصر من AI

### الطلب
```
GET {{url}}/api/smart-bags/ai/suggest-items?category=الملابس
```

### Headers
```
Authorization: Bearer YOUR_TOKEN
Accept-Language: ar
```

### المعاملات
- `category` (مطلوب): اسم الفئة من الخطوة السابقة

### الاستجابة
```json
{
  "success": true,
  "message": "AI items suggested successfully",
  "data": {
    "category": "الملابس",
    "items": [
      {
        "name": "قميص",
        "weight": 0.15,
        "weight_grams": 150
      },
      {
        "name": "بنطال",
        "weight": 0.5,
        "weight_grams": 500
      },
      {
        "name": "ملابس داخلية",
        "weight": 0.05,
        "weight_grams": 50
      },
      {
        "name": "جوارب",
        "weight": 0.04,
        "weight_grams": 40
      }
    ],
    "language": "ar"
  }
}
```

**ملاحظة:**
- `weight` بالكيلوجرام (kg)
- `weight_grams` بالجرام (للرجوع)

---

## 3️⃣ إضافة عنصر من AI للحقيبة

### الطلب
```
POST {{url}}/api/smart-bags/2/ai/add-item
```

### Headers
```
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json
```

### Body
```json
{
  "item_name": "بنطلون",
  "weight": 0.20,
  "essential": true,
  "quantity": 2
}
```

### الحقول
| الحقل | النوع | مطلوب | الوصف |
|-------|------|-------|-------|
| `item_name` | string | **نعم** | اسم العنصر (من AI) |
| `weight` | float | **نعم** | الوزن بالكيلوجرام (0 - 999.99) |
| `essential` | boolean | لا | هل العنصر ضروري؟ (افتراضي: `false`) |
| `quantity` | integer | لا | الكمية (افتراضي: `1`) |

### الاستجابة (201)
```json
{
  "success": true,
  "message": "AI item added successfully",
  "data": {
    "item": {
      "id": 123,
      "name": "بنطلون",
      "weight": 0.2,
      "total_weight": 0.4,
      "essential": true,
      "packed": false,
      "quantity": 2
    },
    "bag": {
      "current_weight": 5.45,
      "max_weight": 20.0,
      "weight_percentage": 27.25
    }
  }
}
```

### الأخطاء

**400 - الوزن تجاوز الحد:**
```json
{
  "success": false,
  "message": "Cannot add more items. Weight limit exceeded."
}
```

**404 - الحقيبة غير موجودة:**
```json
{
  "success": false,
  "message": "Bag not found"
}
```

**422 - خطأ في البيانات:**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "item_name": ["اسم الغرض مطلوب"],
    "weight": ["وزن الغرض مطلوب"]
  }
}
```

---

## 🔄 مثال على التدفق الكامل

### الخطوة 1: الحصول على الفئات
```http
GET {{url}}/api/smart-bags/ai/categories
Authorization: Bearer YOUR_TOKEN
Accept-Language: ar
```

### الخطوة 2: الحصول على العناصر عند اختيار فئة
```http
GET {{url}}/api/smart-bags/ai/suggest-items?category=الملابس
Authorization: Bearer YOUR_TOKEN
Accept-Language: ar
```

### الخطوة 3: عرض Dialog عند اختيار عنصر
- اسم العنصر: "بنطلون"
- الوزن: 0.20 kg (قابل للتعديل)
- Essential: true/false
- الكمية: 2
- زر "إضافة"

### الخطوة 4: إضافة العنصر
```http
POST {{url}}/api/smart-bags/2/ai/add-item
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "item_name": "بنطلون",
  "weight": 0.20,
  "essential": true,
  "quantity": 2
}
```

---


1. **الوزن:** جميع الأوزان بالكيلوجرام (kg)
2. **اللغة:** أرسل `Accept-Language` header دائماً
3. **Essential:** المستخدم يقرر عند الإضافة
4. **Category:** لا حاجة لإرسال `item_category_id` - كل شيء من AI
5. **التحقق:** النظام يتحقق من عدم تجاوز الوزن الأقصى

---

**آخر تحديث:** 10 يناير 2026

