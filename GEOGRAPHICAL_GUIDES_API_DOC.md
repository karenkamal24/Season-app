# 📍 Geographical Guides API Documentation
## الأدلة الجغرافية - وثائق API

---

## 🔗 Base URL
```
/api/geographical-guides
```

---

## 🔐 Authentication
All authenticated endpoints require:
```
Authorization: Bearer YOUR_TOKEN
```

---

## 📋 Endpoints

### 1. Get My Services
Get all user's geographical guides (all statuses)

```
GET /api/geographical-guides/my-services
GET /api/geographical-guides/my-service  (alias)
```

**Authentication:** Required

**Response:**
```json
{
  "status": 200,
  "message": "تم جلب الأدلة الجغرافية بنجاح",
  "data": [
    {
      "id": 1,
      "service_name": "مطعم الشام",
      "status": "pending",
      ...
    }
  ]
}
```

---

### 2. Update Service
Update geographical guide details

```
PUT /api/geographical-guides/{id}
```

**Authentication:** Required

**Content-Type:** `multipart/form-data`

**Request Body (all fields optional):**
- `geographical_category_id`
- `geographical_sub_category_id`
- `service_name`
- `description`
- `phone_1`, `phone_2`
- `country_id`, `city_id`
- `address`
- `latitude`, `longitude`
- `website`
- `commercial_register` (file)

**Rules:**
- ✅ Can edit: `pending` or `rejected` services
- ❌ Cannot edit: `approved` services (returns 403)

**Response:**
```json
{
  "status": 200,
  "message": "تم تحديث الدليل الجغرافي بنجاح",
  "data": { ... }
}
```

**Error (403):**
```json
{
  "status": 403,
  "message": "لا يمكن تعديل الخدمة الموافق عليها. يرجى الاتصال بالإدارة."
}
```

---

### 3. Delete Service
Delete geographical guide

```
DELETE /api/geographical-guides/{id}
```

**Authentication:** Required

**Rules:**
- ✅ Can delete: `pending` or `rejected` services
- ❌ Cannot delete: `approved` services (returns 403)

**Response:**
```json
{
  "status": 200,
  "message": "تم حذف الدليل الجغرافي بنجاح",
  "data": []
}
```

**Error (403):**
```json
{
  "status": 403,
  "message": "لا يمكن حذف الخدمة الموافق عليها. يرجى الاتصال بالإدارة."
}
```

---

### 4. Get Single Service
View service details

```
GET /api/geographical-guides/{id}
```

**Authentication:** Optional

**Rules:**
- Public: Shows `approved` services only
- Authenticated + own service: Shows any status

---

### 5. Create Service
Create new geographical guide

```
POST /api/geographical-guides
```

**Authentication:** Required

**Content-Type:** `multipart/form-data`

**Required Fields:**
- `geographical_category_id`
- `service_name`
- `country_id`
- `city_id`

**Optional Fields:**
- `geographical_sub_category_id`
- `description`
- `phone_1`, `phone_2`
- `address`
- `latitude`, `longitude`
- `website`
- `commercial_register` (file)

**Note:** Status automatically set to `pending`

---

### 6. Search Services (Public)
Search and filter geographical guides

```
GET /api/geographical-guides
```

**Query Parameters:**
- `city_id`
- `geographical_category_id`
- `geographical_sub_category_id`

**Headers:**
- `Accept-Country: KSA` (optional - filters by country)
- `Accept-Language: ar` (optional)

**Response:** Only `approved` services

---

## 📊 Status Values

| Status | Arabic | Can Edit? | Can Delete? |
|--------|--------|-----------|-------------|
| `pending` | قيد المراجعة | ✅ Yes | ✅ Yes |
| `approved` | موافق عليها | ❌ No | ❌ No |
| `rejected` | مرفوضة | ✅ Yes | ✅ Yes |

---

## ⚠️ Error Codes

- **400** - Bad Request
- **401** - Unauthenticated
- **403** - Forbidden (trying to edit/delete approved service)
- **404** - Not Found
- **422** - Validation Error

---

## 📝 Example Requests

### Update Service
```bash
curl -X PUT "https://api.example.com/api/geographical-guides/1" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "service_name=مطعم الشام المحدث" \
  -F "description=وصف محدث"
```

### Delete Service
```bash
curl -X DELETE "https://api.example.com/api/geographical-guides/1" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

**Last Updated:** December 2025










