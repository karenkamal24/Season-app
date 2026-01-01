# ⚡ Quick Setup - Item Categories for Smart Bags

## 🎯 What You Need

User wants to:
1. Enter **name** ✅
2. Enter **weight** ✅  
3. **Select category** from `item_categories` table ✅

---

## 🚀 Setup (3 Steps)

### Step 1: Run Migrations

```bash
cd d:\season-app
php artisan migrate
```

**This will:**
- Update `bag_items` table
- Add `item_category_id` foreign key
- Remove old enum `category` column

### Step 2: Seed Categories

```bash
php artisan db:seed --class=ItemCategorySeeder
```

**This adds 10 categories:**
1. Clothing (ملابس)
2. Shoes (أحذية)
3. Electronics (إلكترونيات)
4. Medicine & Care (أدوية وعناية)
5. Documents (مستندات)
6. Toiletries (أدوات نظافة)
7. Accessories (إكسسوارات)
8. Books & Entertainment (كتب وترفيه)
9. Food & Snacks (طعام ووجبات خفيفة)
10. Other (أخرى)

### Step 3: Test

```bash
# Get categories
curl http://localhost:8000/api/item-categories

# Add item with category
curl -X POST "http://localhost:8000/api/smart-bags/1/items" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "قميص",
    "weight": 0.3,
    "item_category_id": 1
  }'
```

---

## 📡 API Endpoints

### 1. Get All Categories (No Auth)

```
GET /api/item-categories
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "ملابس",
      "name_ar": "ملابس",
      "name_en": "Clothing",
      "icon": "shirt",
      "icon_color": "#3B82F6"
    }
  ]
}
```

### 2. Add Item to Bag (Auth Required)

```
POST /api/smart-bags/{bagId}/items
```

**Request:**
```json
{
  "name": "قميص أبيض",
  "weight": 0.3,
  "item_category_id": 1
}
```

**Response:**
```json
{
  "success": true,
  "message_ar": "تم إضافة الغرض بنجاح",
  "data": {
    "id": 1,
    "name": "قميص أبيض",
    "weight": 0.30,
    "item_category_id": 1,
    "category": {
      "id": 1,
      "name": "ملابس",
      "icon": "shirt",
      "icon_color": "#3B82F6"
    }
  }
}
```

---

## 🎨 Frontend Example

### HTML Form

```html
<form id="addItemForm">
  <!-- Name (user enters) -->
  <input type="text" name="name" placeholder="اسم الغرض" required />
  
  <!-- Weight (user enters) -->
  <input type="number" step="0.01" name="weight" placeholder="الوزن" required />
  
  <!-- Category (user selects) -->
  <select name="item_category_id" required>
    <option value="">اختر الفئة</option>
    <!-- Will be populated from API -->
  </select>
  
  <button type="submit">إضافة</button>
</form>
```

### JavaScript

```javascript
// 1. Load categories on page load
async function loadCategories() {
  const res = await fetch('/api/item-categories');
  const data = await res.json();
  
  const select = document.querySelector('[name="item_category_id"]');
  data.data.forEach(cat => {
    const option = new Option(cat.name, cat.id);
    select.add(option);
  });
}

// 2. Submit form
document.getElementById('addItemForm').onsubmit = async (e) => {
  e.preventDefault();
  const form = new FormData(e.target);
  
  const res = await fetch(`/api/smart-bags/${bagId}/items`, {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      name: form.get('name'),
      weight: parseFloat(form.get('weight')),
      item_category_id: parseInt(form.get('item_category_id'))
    })
  });
  
  const result = await res.json();
  alert(result.message_ar);
};

// Load categories when page loads
loadCategories();
```

---

## ✅ Validation

### Valid Request ✅
```json
{
  "name": "قميص",
  "weight": 0.3,
  "item_category_id": 1
}
```

### Invalid - Missing Category ❌
```json
{
  "name": "قميص",
  "weight": 0.3
}
```
**Error:** "فئة الغرض مطلوبة"

### Invalid - Wrong Category ID ❌
```json
{
  "name": "قميص",
  "weight": 0.3,
  "item_category_id": 999
}
```
**Error:** "الفئة المحددة غير موجودة"

---

## 🎯 Complete Flow

1. **User opens add item form**
2. **System loads categories** from `/api/item-categories`
3. **User fills:**
   - Name: "قميص أبيض"
   - Weight: 0.3 kg
   - Selects: "ملابس" (id: 1)
4. **System sends** to `/api/smart-bags/1/items`
5. **Item added** with category details
6. **UI shows** item with category icon & color

---

## 📊 What Changed

### Before:
```json
{
  "name": "قميص",
  "weight": 0.3,
  "category": "ملابس"  // ❌ Hardcoded enum
}
```

### After:
```json
{
  "name": "قميص",
  "weight": 0.3,
  "item_category_id": 1  // ✅ Foreign key to table
}
```

---

## 🎁 Benefits

✅ Dynamic categories (add from admin)
✅ Icons & colors for UI
✅ Multi-language support
✅ Can activate/deactivate
✅ Custom sorting
✅ Easier to manage

---

## 📝 Summary

**What user does:**
1. Enters name ✅
2. Enters weight ✅
3. Selects category from dropdown ✅

**What system does:**
1. Loads categories from `item_categories` table
2. Validates `item_category_id` exists
3. Saves item with foreign key
4. Returns item with full category details (name, icon, color)

---

**That's it! Run the commands and test! 🚀**

```bash
php artisan migrate
php artisan db:seed --class=ItemCategorySeeder
curl http://localhost:8000/api/item-categories
```

