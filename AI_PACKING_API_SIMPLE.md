# 🤖 AI-Powered Smart Packing API - Quick Reference
## توثيق سريع لـ APIs التعبئة الذكية بالذكاء الاصطناعي

---

## 📋 Endpoints Overview

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/smart-bags/ai/categories` | الحصول على فئات التعبئة من AI |
| `GET` | `/api/smart-bags/ai/suggest-items?category={name}` | الحصول على اقتراحات العناصر من AI |
| `POST` | `/api/smart-bags/{bagId}/ai/add-item` | إضافة عنصر من AI للحقيبة |

---

## 🔐 Authentication

جميع الـ endpoints تحتاج Bearer token:

```
Authorization: Bearer YOUR_API_TOKEN
```

---

## 🌐 Language Support

الـ API يدعم العربية والإنجليزية عبر `Accept-Language` header:

```
Accept-Language: ar  (للعربية)
Accept-Language: en  (للإنجليزية)
```

---

## 1️⃣ Get AI Categories

### Endpoint
```
GET {{url}}/api/smart-bags/ai/categories
```

### Headers
```
Authorization: Bearer YOUR_TOKEN
Accept-Language: ar
```

### Response (200 OK)
```json
{
  "success": true,
  "message": "AI categories generated successfully",
  "data": {
    "categories": [
      {
        "name": "الملابس"
      },
      {
        "name": "مستلزمات النظافة"
      },
      {
        "name": "الإلكترونيات"
      },
      {
        "name": "المستندات"
      },
      {
        "name": "الأدوية"
      },
      {
        "name": "الإكسسوارات"
      },
      {
        "name": "الطعام والوجبات الخفيفة"
      },
      {
        "name": "الترفيه"
      }
    ],
    "language": "ar"
  }
}
```

### English Response
```json
{
  "success": true,
  "message": "AI categories generated successfully",
  "data": {
    "categories": [
      {
        "name": "Clothing"
      },
      {
        "name": "Toiletries"
      },
      {
        "name": "Electronics"
      },
      {
        "name": "Documents"
      },
      {
        "name": "Medications"
      },
      {
        "name": "Accessories"
      },
      {
        "name": "Food & Snacks"
      },
      {
        "name": "Entertainment"
      }
    ],
    "language": "en"
  }
}
```

---

## 2️⃣ Get AI Suggested Items

### Endpoint
```
GET {{url}}/api/smart-bags/ai/suggest-items?category=الملابس
```

### Headers
```
Authorization: Bearer YOUR_TOKEN
Accept-Language: ar
```

### Query Parameters
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `category` | string | **Yes** | اسم الفئة (من AI categories) |

### Response (200 OK)
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
      },
      {
        "name": "تيشيرت",
        "weight": 0.2,
        "weight_grams": 200
      },
      {
        "name": "جاكيت",
        "weight": 0.8,
        "weight_grams": 800
      },
      {
        "name": "حذاء رياضي",
        "weight": 0.6,
        "weight_grams": 600
      },
      {
        "name": "شورت",
        "weight": 0.12,
        "weight_grams": 120
      },
      {
        "name": "قميص طويل",
        "weight": 0.25,
        "weight_grams": 250
      },
      {
        "name": "جينز",
        "weight": 0.6,
        "weight_grams": 600
      }
    ],
    "language": "ar"
  }
}
```

### Notes
- `weight` بالكيلوجرام (kg)
- `weight_grams` بالجرام (للرجوع)

---

## 3️⃣ Add AI Item to Bag

### Endpoint
```
POST {{url}}/api/smart-bags/{bagId}/ai/add-item
```

### Headers
```
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json
```

### Path Parameters
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `bagId` | integer | **Yes** | رقم الحقيبة |

### Request Body
```json
{
  "item_name": "بنطلون",
  "weight": 0.20,
  "essential": true,
  "quantity": 2
}
```

### Body Parameters
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `item_name` | string | **Yes** | اسم العنصر (من AI suggestions) |
| `weight` | float | **Yes** | الوزن بالكيلوجرام (0 - 999.99) |
| `essential` | boolean | No | هل العنصر ضروري؟ (افتراضي: `false`) |
| `quantity` | integer | No | الكمية (افتراضي: `1`) |

### Response (201 Created)
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
      "quantity": 2,
      "item_category_id": null,
      "category": null,
      "notes": null,
      "created_at": "2026-01-10T20:30:00+00:00",
      "updated_at": "2026-01-10T20:30:00+00:00"
    },
    "bag": {
      "current_weight": 5.45,
      "max_weight": 20.0,
      "weight_percentage": 27.25
    }
  }
}
```

### Error Responses

**400 Bad Request - Weight Exceeded:**
```json
{
  "success": false,
  "message": "Cannot add more items. Weight limit exceeded."
}
```

**404 Not Found - Bag Not Found:**
```json
{
  "success": false,
  "message": "Bag not found"
}
```

**422 Validation Error:**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "item_name": [
      "اسم الغرض مطلوب"
    ],
    "weight": [
      "وزن الغرض مطلوب"
    ]
  }
}
```

---

## 🔄 Complete Flow Example

### Step 1: Get Categories
```http
GET {{url}}/api/smart-bags/ai/categories
Authorization: Bearer YOUR_TOKEN
Accept-Language: ar
```

**Response:** List of categories like `["الملابس", "مستلزمات النظافة", ...]`

### Step 2: Get Items for Selected Category
```http
GET {{url}}/api/smart-bags/ai/suggest-items?category=الملابس
Authorization: Bearer YOUR_TOKEN
Accept-Language: ar
```

**Response:** List of items with names and weights

### Step 3: User Selects Item → Show Dialog
- Item name: "بنطلون"
- Weight: 0.20 kg (editable)
- Essential toggle: true/false
- Quantity: 2
- Add button

### Step 4: Add Item to Bag
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

**Response:** Item added successfully with updated bag weight

---


## 📝 Notes

1. **Weight Units:** جميع الأوزان بالكيلوجرام (kg)
2. **Language:** أرسل `Accept-Language` header دائماً للحصول على النتائج باللغة المطلوبة
3. **Essential Flag:** المستخدم يقرر إذا كان العنصر ضروري عند الإضافة
4. **Category:** لا حاجة لإرسال `item_category_id` - كل شيء من AI
5. **Weight Validation:** النظام يتحقق من عدم تجاوز الوزن الأقصى للحقيبة

---

## 🚀 Quick Test

### Test with cURL

**1. Get Categories:**
```bash
curl -X GET "{{url}}/api/smart-bags/ai/categories" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept-Language: ar"
```

**2. Get Items:**
```bash
curl -X GET "{{url}}/api/smart-bags/ai/suggest-items?category=الملابس" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept-Language: ar"
```

**3. Add Item:**
```bash
curl -X POST "{{url}}/api/smart-bags/2/ai/add-item" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "item_name": "بنطلون",
    "weight": 0.20,
    "essential": true,
    "quantity": 2
  }'
```

---

**Last Updated:** January 10, 2026  
**API Version:** 1.0

