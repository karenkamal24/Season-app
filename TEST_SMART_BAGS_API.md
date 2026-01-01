# 🧪 Smart Bags API - Testing Guide

## ✅ Pre-Testing Checklist

### 1. Fix Database
```bash
cd d:\season-app
php artisan migrate
```

### 2. Start Server
```bash
php artisan serve
```

### 3. Get Your Token

**Login Request:**
```bash
curl -X POST "http://localhost:8000/api/auth/login" \
  -H "Content-Type: application/json" \
  -d "{\"email\":\"your-email@example.com\",\"password\":\"your-password\"}"
```

**Save the token from response!**

---

## 📝 Test Cases

### ✅ TEST 1: Get All Bags

**Request:**
```bash
curl -X GET "http://localhost:8000/api/smart-bags" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

**Expected Result:**
- Status: 200 OK
- Response contains: `"success": true`
- Data is array of bags
- Each bag has: id, name, trip_type, items, etc.

**If Empty:**
```json
{
  "success": true,
  "data": [],
  "pagination": {
    "total": 0
  }
}
```

---

### ✅ TEST 2: Create Bag

**Request:**
```bash
curl -X POST "http://localhost:8000/api/smart-bags" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d "{
    \"name\": \"حقيبة اختبار\",
    \"trip_type\": \"سياحة\",
    \"duration\": 3,
    \"destination\": \"القاهرة\",
    \"departure_date\": \"2024-12-30\",
    \"max_weight\": 20
  }"
```

**Expected Result:**
- Status: 201 Created
- Response: `"success": true`
- Data contains: bag with id
- `total_weight` should be 0

**Copy the `id` from response for next tests!**

---

### ✅ TEST 3: Add Item (User enters name + weight)

**Request:**
```bash
curl -X POST "http://localhost:8000/api/smart-bags/1/items" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d "{
    \"name\": \"قميص\",
    \"weight\": 0.3,
    \"category\": \"ملابس\"
  }"
```

**Expected Result:**
- Status: 201 Created
- Response: `"success": true`
- Item created with: id, name="قميص", weight=0.3
- `message_ar`: "تم إضافة الغرض بنجاح"

**Copy the item `id` for next test!**

---

### ✅ TEST 4: Add Multiple Items

```bash
# Item 2: Laptop
curl -X POST "http://localhost:8000/api/smart-bags/1/items" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d "{\"name\":\"لابتوب\",\"weight\":2.5,\"category\":\"إلكترونيات\"}"

# Item 3: Shoes
curl -X POST "http://localhost:8000/api/smart-bags/1/items" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d "{\"name\":\"حذاء\",\"weight\":1.2,\"category\":\"أحذية\"}"

# Item 4: Multiple shirts
curl -X POST "http://localhost:8000/api/smart-bags/1/items" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d "{\"name\":\"قمصان\",\"weight\":0.25,\"category\":\"ملابس\",\"quantity\":5}"
```

**Expected:**
- All items added successfully
- Total bag weight = 0.3 + 2.5 + 1.2 + (0.25 * 5) = 5.25 kg

---

### ✅ TEST 5: Get Bag Details

**Request:**
```bash
curl -X GET "http://localhost:8000/api/smart-bags/1" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

**Expected Result:**
- Status: 200 OK
- Bag details with ALL items
- `total_weight`: 5.25
- `weight_percentage`: calculated
- `items_count`: 4
- `items` array with all 4 items

---

### ✅ TEST 6: Toggle Item Packed

**Request:**
```bash
curl -X POST "http://localhost:8000/api/smart-bags/1/items/1/toggle-packed" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

**Expected Result:**
- Status: 200 OK
- Item `packed` changed from `false` to `true`
- Message: "تم تحديث حالة التحزيم"

**Run Again:**
```bash
# Toggle again - should change back to false
curl -X POST "http://localhost:8000/api/smart-bags/1/items/1/toggle-packed" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

---

### ✅ TEST 7: Update Item

**Request:**
```bash
curl -X PUT "http://localhost:8000/api/smart-bags/1/items/1" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d "{
    \"name\": \"قميص أبيض محدث\",
    \"weight\": 0.35,
    \"notes\": \"قميص قطني\"
  }"
```

**Expected Result:**
- Status: 200 OK
- Item updated with new values
- Total bag weight recalculated automatically

---

### ✅ TEST 8: Delete Item

**Request:**
```bash
curl -X DELETE "http://localhost:8000/api/smart-bags/1/items/1" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**Expected Result:**
- Status: 200 OK
- Message: "تم حذف الغرض بنجاح"
- Total bag weight recalculated (decreased)

---

### ✅ TEST 9: Validation Tests

#### Test 9.1: Missing Name
```bash
curl -X POST "http://localhost:8000/api/smart-bags/1/items" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d "{\"weight\":0.5,\"category\":\"ملابس\"}"
```

**Expected:**
- Status: 400 or 422
- Error: "اسم الغرض مطلوب"

#### Test 9.2: Missing Weight
```bash
curl -X POST "http://localhost:8000/api/smart-bags/1/items" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d "{\"name\":\"قميص\",\"category\":\"ملابس\"}"
```

**Expected:**
- Status: 400 or 422
- Error: "وزن الغرض مطلوب"

#### Test 9.3: Invalid Category
```bash
curl -X POST "http://localhost:8000/api/smart-bags/1/items" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d "{\"name\":\"قميص\",\"weight\":0.5,\"category\":\"invalid\"}"
```

**Expected:**
- Status: 400 or 422
- Error: "الفئة يجب أن تكون: ملابس، أحذية..."

---

### ✅ TEST 10: Get All Bags Again

```bash
curl -X GET "http://localhost:8000/api/smart-bags" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**Expected:**
- Now shows the bag we created
- With updated item counts and weights

---

## 🎯 Quick Test Script (PowerShell)

Save this as `test-smart-bags.ps1`:

```powershell
# Configuration
$BASE_URL = "http://localhost:8000/api"
$TOKEN = "YOUR_TOKEN_HERE"

# Headers
$headers = @{
    "Authorization" = "Bearer $TOKEN"
    "Content-Type" = "application/json"
    "Accept" = "application/json"
}

Write-Host "1. Testing Get All Bags..." -ForegroundColor Cyan
Invoke-RestMethod -Uri "$BASE_URL/smart-bags" -Headers $headers -Method Get

Write-Host "`n2. Creating Test Bag..." -ForegroundColor Cyan
$bagData = @{
    name = "حقيبة اختبار"
    trip_type = "سياحة"
    duration = 3
    destination = "القاهرة"
    departure_date = "2024-12-30"
    max_weight = 20
} | ConvertTo-Json

$bag = Invoke-RestMethod -Uri "$BASE_URL/smart-bags" -Headers $headers -Method Post -Body $bagData
$bagId = $bag.data.id
Write-Host "Created bag with ID: $bagId" -ForegroundColor Green

Write-Host "`n3. Adding Item..." -ForegroundColor Cyan
$itemData = @{
    name = "قميص"
    weight = 0.3
    category = "ملابس"
} | ConvertTo-Json

$item = Invoke-RestMethod -Uri "$BASE_URL/smart-bags/$bagId/items" -Headers $headers -Method Post -Body $itemData
Write-Host "Item added: $($item.data.name)" -ForegroundColor Green

Write-Host "`n4. Getting Bag Details..." -ForegroundColor Cyan
$bagDetails = Invoke-RestMethod -Uri "$BASE_URL/smart-bags/$bagId" -Headers $headers -Method Get
Write-Host "Bag has $($bagDetails.data.items_count) items, total weight: $($bagDetails.data.total_weight) kg" -ForegroundColor Green

Write-Host "`n✅ All tests passed!" -ForegroundColor Green
```

**Run:**
```powershell
.\test-smart-bags.ps1
```

---

## 🐛 Troubleshooting

### Error: "Unauthenticated"
**Fix:** Make sure token is correct and not expired

### Error: "Column 'travel_bag_id' not found"
**Fix:** Run migration:
```bash
php artisan migrate
```

### Error: "Route not found"
**Fix:** Clear route cache:
```bash
php artisan route:clear
php artisan config:clear
```

### Error: "Class not found"
**Fix:** Rebuild autoload:
```bash
composer dump-autoload
```

---

## ✅ Success Criteria

After all tests:

✅ Can get all bags
✅ Can create bag
✅ Can add item with just name + weight
✅ Total weight updates automatically
✅ Can toggle packed status
✅ Can update item
✅ Can delete item
✅ Validation works correctly
✅ All responses in Arabic/English

---

## 📊 Expected Test Results Summary

| Test | Endpoint | Expected Status | Expected Result |
|------|----------|----------------|-----------------|
| 1 | GET /smart-bags | 200 | List of bags |
| 2 | POST /smart-bags | 201 | Bag created |
| 3 | POST /smart-bags/1/items | 201 | Item added |
| 4 | GET /smart-bags/1 | 200 | Bag with items |
| 5 | POST /toggle-packed | 200 | Packed toggled |
| 6 | PUT /items/1 | 200 | Item updated |
| 7 | DELETE /items/1 | 200 | Item deleted |
| 8 | POST (no name) | 422 | Validation error |

---

**If all tests pass, you're ready to go! 🚀**

**Check `SMART_BAGS_QUICK_START_GUIDE.md` for more examples!**

