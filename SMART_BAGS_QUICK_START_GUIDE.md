# 🚀 Smart Bags API - Quick Start Guide

## ✅ Setup Checklist

Before using the API, make sure:

### 1. Run the Migration to Fix Database

```bash
# Run the migration to update bag_items table
php artisan migrate

# This will:
# - Rename 'travel_bag_id' to 'bag_id'
# - Add required columns (name, weight, category, etc.)
```

### 2. Start the Server

```bash
php artisan serve
```

Server will run on: `http://localhost:8000`

---

## 🔐 Authentication

First, get your authentication token:

```bash
curl -X POST "http://localhost:8000/api/auth/login" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "your-email@example.com",
    "password": "your-password"
  }'
```

**Response:**
```json
{
  "success": true,
  "data": {
    "token": "1|xxxxxxxxxxxxxxxxxxxxx",
    "user": { ... }
  }
}
```

**Save this token!** You'll need it for all requests.

---

## 📦 1. Get All Bags

**Endpoint:** `GET /api/smart-bags`

### Basic Request

```bash
curl -X GET "http://localhost:8000/api/smart-bags" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept-Language: ar"
```

### With Filters

```bash
# Get only in-progress bags
curl -X GET "http://localhost:8000/api/smart-bags?status=in_progress" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"

# Get upcoming trips only
curl -X GET "http://localhost:8000/api/smart-bags?upcoming=true" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"

# Get business trips, sorted by date
curl -X GET "http://localhost:8000/api/smart-bags?trip_type=عمل&sort_by=departure_date&sort_order=desc" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Success Response

```json
{
  "success": true,
  "message": "Bags retrieved successfully",
  "message_ar": "تم جلب الحقائب بنجاح",
  "data": [
    {
      "id": 1,
      "name": "حقيبة رحلة دبي",
      "trip_type": "عمل",
      "duration": 4,
      "destination": "دبي",
      "departure_date": "2024-12-25",
      "max_weight": 20.00,
      "total_weight": 10.50,
      "weight_percentage": 52.50,
      "remaining_weight": 9.50,
      "is_overweight": false,
      "days_until_departure": 5,
      "status": "in_progress",
      "items_count": 8,
      "items": [
        {
          "id": 1,
          "name": "لابتوب",
          "weight": 2.30,
          "category": "إلكترونيات",
          "essential": true,
          "packed": true,
          "quantity": 1
        }
      ]
    }
  ],
  "pagination": {
    "total": 5,
    "per_page": 15,
    "current_page": 1,
    "last_page": 1
  }
}
```

---

## ➕ 2. Create New Bag

**Endpoint:** `POST /api/smart-bags`

### Simple Bag (No Items)

```bash
curl -X POST "http://localhost:8000/api/smart-bags" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -H "Accept-Language: ar" \
  -d '{
    "name": "حقيبة رحلة القاهرة",
    "trip_type": "سياحة",
    "duration": 5,
    "destination": "القاهرة",
    "departure_date": "2024-12-30",
    "max_weight": 23
  }'
```

### Bag with Initial Items

```bash
curl -X POST "http://localhost:8000/api/smart-bags" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -H "Accept-Language: ar" \
  -d '{
    "name": "حقيبة رحلة دبي",
    "trip_type": "عمل",
    "duration": 4,
    "destination": "دبي",
    "departure_date": "2024-12-25",
    "max_weight": 20,
    "status": "draft",
    "items": [
      {
        "name": "بدلة رسمية",
        "weight": 2.4,
        "category": "ملابس",
        "essential": true,
        "quantity": 2
      },
      {
        "name": "لابتوب",
        "weight": 2.3,
        "category": "إلكترونيات",
        "essential": true,
        "quantity": 1
      }
    ]
  }'
```

### Required Fields

| Field | Type | Example | Required |
|-------|------|---------|----------|
| `name` | string | "حقيبة رحلة دبي" | ✅ Yes |
| `trip_type` | enum | "عمل", "سياحة", "عائلية", "علاج" | ✅ Yes |
| `duration` | integer | 4 | ✅ Yes |
| `destination` | string | "دبي" | ✅ Yes |
| `departure_date` | date | "2024-12-25" | ✅ Yes |
| `max_weight` | decimal | 20 | ✅ Yes |
| `status` | enum | "draft", "in_progress", "completed" | No (default: draft) |
| `items` | array | [...] | No |

---

## 📝 3. Add Item to Bag

**Endpoint:** `POST /api/smart-bags/{bagId}/items`

This is what you want! User enters **name** and **weight** to add item.

### Request

```bash
curl -X POST "http://localhost:8000/api/smart-bags/1/items" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -H "Accept-Language: ar" \
  -d '{
    "name": "قميص أبيض",
    "weight": 0.3,
    "category": "ملابس",
    "quantity": 3
  }'
```

### Minimal Request (Only Required Fields)

```bash
curl -X POST "http://localhost:8000/api/smart-bags/1/items" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "حذاء رياضي",
    "weight": 1.2,
    "category": "أحذية"
  }'
```

### Request Fields

| Field | Type | Required | Example | Default |
|-------|------|----------|---------|---------|
| `name` | string | ✅ Yes | "قميص أبيض" | - |
| `weight` | decimal | ✅ Yes | 0.3 | - |
| `category` | enum | ✅ Yes | See below | - |
| `essential` | boolean | No | true | false |
| `packed` | boolean | No | true | false |
| `quantity` | integer | No | 3 | 1 |
| `notes` | string | No | "تذكر القميص الأزرق" | null |

### Categories (choose one):

- `ملابس` - Clothing
- `أحذية` - Shoes
- `إلكترونيات` - Electronics
- `أدوية وعناية` - Medicine & Care
- `مستندات` - Documents
- `أخرى` - Other

### Success Response

```json
{
  "success": true,
  "message": "Item added successfully",
  "message_ar": "تم إضافة الغرض بنجاح",
  "data": {
    "id": 13,
    "name": "قميص أبيض",
    "weight": 0.30,
    "total_weight": 0.90,
    "category": "ملابس",
    "category_en": "Clothing",
    "essential": false,
    "packed": false,
    "quantity": 3,
    "notes": null,
    "created_at": "2024-12-20T12:00:00Z",
    "updated_at": "2024-12-20T12:00:00Z"
  }
}
```

**Note:** The bag's `total_weight` is automatically updated!

---

## ✏️ 4. Update Item

**Endpoint:** `PUT /api/smart-bags/{bagId}/items/{itemId}`

```bash
curl -X PUT "http://localhost:8000/api/smart-bags/1/items/13" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "قميص أبيض (قطن)",
    "weight": 0.25,
    "quantity": 4
  }'
```

---

## ✅ 5. Mark Item as Packed

**Endpoint:** `POST /api/smart-bags/{bagId}/items/{itemId}/toggle-packed`

```bash
curl -X POST "http://localhost:8000/api/smart-bags/1/items/13/toggle-packed" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

This will toggle the `packed` status from `false` to `true` or vice versa.

---

## 🗑️ 6. Delete Item

**Endpoint:** `DELETE /api/smart-bags/{bagId}/items/{itemId}`

```bash
curl -X DELETE "http://localhost:8000/api/smart-bags/1/items/13" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

---

## 🔍 7. Get Bag Details

**Endpoint:** `GET /api/smart-bags/{bagId}`

```bash
curl -X GET "http://localhost:8000/api/smart-bags/1" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

This returns complete bag details with all items and latest AI analysis.

---

## 🤖 8. Analyze Bag with AI

**Endpoint:** `POST /api/smart-bags/{bagId}/analyze`

```bash
curl -X POST "http://localhost:8000/api/smart-bags/1/analyze" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json"
```

This will:
- Analyze your bag contents
- Suggest missing items
- Identify unnecessary items
- Optimize weight
- Generate smart alerts

---

## 📱 Complete User Flow Example

### Scenario: User Creating a Business Trip to Dubai

```bash
# 1. Login
TOKEN=$(curl -X POST "http://localhost:8000/api/auth/login" \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password"}' \
  | jq -r '.data.token')

# 2. Create Bag
BAG_ID=$(curl -X POST "http://localhost:8000/api/smart-bags" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "حقيبة رحلة دبي",
    "trip_type": "عمل",
    "duration": 4,
    "destination": "دبي",
    "departure_date": "2024-12-25",
    "max_weight": 20
  }' | jq -r '.data.id')

echo "Created bag with ID: $BAG_ID"

# 3. Add Items - User enters name and weight
curl -X POST "http://localhost:8000/api/smart-bags/$BAG_ID/items" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "بدلة رسمية",
    "weight": 2.4,
    "category": "ملابس"
  }'

curl -X POST "http://localhost:8000/api/smart-bags/$BAG_ID/items" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "لابتوب",
    "weight": 2.3,
    "category": "إلكترونيات"
  }'

curl -X POST "http://localhost:8000/api/smart-bags/$BAG_ID/items" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "قمصان",
    "weight": 0.3,
    "category": "ملابس",
    "quantity": 5
  }'

# 4. Get bag details
curl -X GET "http://localhost:8000/api/smart-bags/$BAG_ID" \
  -H "Authorization: Bearer $TOKEN"

# 5. Mark items as packed
curl -X POST "http://localhost:8000/api/smart-bags/$BAG_ID/items/1/toggle-packed" \
  -H "Authorization: Bearer $TOKEN"

# 6. Analyze with AI
curl -X POST "http://localhost:8000/api/smart-bags/$BAG_ID/analyze" \
  -H "Authorization: Bearer $TOKEN"
```

---

## 🎯 Testing Checklist

### ✅ Test 1: Get All Bags
```bash
curl -X GET "http://localhost:8000/api/smart-bags" \
  -H "Authorization: Bearer YOUR_TOKEN"
```
**Expected:** List of all user's bags

### ✅ Test 2: Create Bag
```bash
curl -X POST "http://localhost:8000/api/smart-bags" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test Bag",
    "trip_type": "سياحة",
    "duration": 3,
    "destination": "Test City",
    "departure_date": "2024-12-30",
    "max_weight": 20
  }'
```
**Expected:** Success response with bag ID

### ✅ Test 3: Add Item (Name + Weight)
```bash
curl -X POST "http://localhost:8000/api/smart-bags/1/items" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test Item",
    "weight": 1.5,
    "category": "أخرى"
  }'
```
**Expected:** Item added, total_weight updated

### ✅ Test 4: Get Bag Details
```bash
curl -X GET "http://localhost:8000/api/smart-bags/1" \
  -H "Authorization: Bearer YOUR_TOKEN"
```
**Expected:** Bag with all items

### ✅ Test 5: Toggle Packed
```bash
curl -X POST "http://localhost:8000/api/smart-bags/1/items/1/toggle-packed" \
  -H "Authorization: Bearer YOUR_TOKEN"
```
**Expected:** Item packed status toggled

---

## ❌ Common Errors & Solutions

### Error: "Unauthenticated"
**Solution:** Make sure you're sending the Bearer token in the Authorization header

### Error: "اسم الغرض مطلوب" (Name required)
**Solution:** The `name` field is required when adding an item

### Error: "وزن الغرض مطلوب" (Weight required)
**Solution:** The `weight` field is required when adding an item

### Error: "فئة الغرض مطلوبة" (Category required)
**Solution:** Choose a valid category from the list

### Error: "SQLSTATE[42S22]: Column not found: 1054 Unknown column 'bag_items.travel_bag_id'"
**Solution:** Run the migration:
```bash
php artisan migrate
```

---

## 📊 Postman Collection

### Import this into Postman:

1. **Environment Variables:**
```json
{
  "base_url": "http://localhost:8000/api",
  "token": "YOUR_TOKEN_HERE",
  "bag_id": "1",
  "item_id": "1"
}
```

2. **Requests:**
- GET All Bags: `{{base_url}}/smart-bags`
- Create Bag: `POST {{base_url}}/smart-bags`
- Add Item: `POST {{base_url}}/smart-bags/{{bag_id}}/items`
- Toggle Packed: `POST {{base_url}}/smart-bags/{{bag_id}}/items/{{item_id}}/toggle-packed`

---

## 🎨 Frontend Example (React/Vue/Angular)

### JavaScript/TypeScript

```javascript
// Add Item Function
async function addItemToBag(bagId, itemData) {
  const response = await fetch(`/api/smart-bags/${bagId}/items`, {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json',
      'Accept-Language': 'ar'
    },
    body: JSON.stringify({
      name: itemData.name,        // User enters this
      weight: itemData.weight,     // User enters this
      category: itemData.category,
      quantity: itemData.quantity || 1
    })
  });
  
  const data = await response.json();
  
  if (data.success) {
    console.log('Item added:', data.data);
    // Update UI with new item and updated total weight
  } else {
    console.error('Error:', data.message_ar || data.message);
  }
}

// Usage
addItemToBag(1, {
  name: "قميص أبيض",
  weight: 0.3,
  category: "ملابس",
  quantity: 3
});
```

### Form Example (HTML)

```html
<form id="addItemForm">
  <input type="text" name="name" placeholder="اسم الغرض" required>
  <input type="number" name="weight" step="0.01" placeholder="الوزن (كجم)" required>
  <select name="category" required>
    <option value="ملابس">ملابس</option>
    <option value="أحذية">أحذية</option>
    <option value="إلكترونيات">إلكترونيات</option>
    <option value="أدوية وعناية">أدوية وعناية</option>
    <option value="مستندات">مستندات</option>
    <option value="أخرى">أخرى</option>
  </select>
  <input type="number" name="quantity" value="1" min="1">
  <button type="submit">إضافة</button>
</form>

<script>
document.getElementById('addItemForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  const formData = new FormData(e.target);
  
  await addItemToBag(bagId, {
    name: formData.get('name'),
    weight: parseFloat(formData.get('weight')),
    category: formData.get('category'),
    quantity: parseInt(formData.get('quantity'))
  });
});
</script>
```

---

## 🚀 You're Ready!

Everything is working! You can now:

✅ **Get all bags** - `GET /api/smart-bags`
✅ **Create bags** - `POST /api/smart-bags`
✅ **Add items** - User enters name + weight - `POST /api/smart-bags/{id}/items`
✅ **Update items** - `PUT /api/smart-bags/{id}/items/{itemId}`
✅ **Delete items** - `DELETE /api/smart-bags/{id}/items/{itemId}`
✅ **Toggle packed** - `POST /api/smart-bags/{id}/items/{itemId}/toggle-packed`
✅ **Analyze with AI** - `POST /api/smart-bags/{id}/analyze`

---

**Need help? Check the full documentation in `API_DOCUMENTATION_SMART_BAGS.md`**

**Happy Coding! 🎒✈️**

