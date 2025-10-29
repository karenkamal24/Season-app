# 📚 Vendor Services API Documentation

## 🔗 Base URL
```
http://your-domain.com/api
```

## 🔐 Authentication
All endpoints require authentication using **Sanctum Bearer Token**.

Include the token in the request header:
```
Authorization: Bearer {your_token_here}
```

## 🌍 Language Support
Include language preference in the header:
```
Accept-Language: ar   // For Arabic (default)
Accept-Language: en   // For English
```

---

## 📑 Table of Contents
1. [Get Service Types](#1-get-service-types)
2. [Get My Vendor Services](#2-get-my-vendor-services)
3. [Get Vendor Service Details](#3-get-vendor-service-details)
4. [Create Vendor Service](#4-create-vendor-service)
5. [Update Vendor Service](#5-update-vendor-service)
6. [Delete Vendor Service (Disable)](#6-delete-vendor-service-disable)
7. [Enable Vendor Service](#7-enable-vendor-service)
8. [Delete Vendor Service Permanently](#8-delete-vendor-service-permanently)

---

## 1️⃣ Get Service Types

Get all active service types available for vendors.

### Endpoint
```http
GET /api/vendor-services/service-types
```

### Headers
```json
{
  "Authorization": "Bearer {token}",
  "Accept-Language": "ar",
  "Accept": "application/json"
}
```

### Success Response (200 OK)

```json
{
  "status": 200,
  "message": "تم استرجاع أنواع الخدمات بنجاح",
  "meta": null,
  "data": [
    {
      "id": 1,
      "name": "نقل وتوصيل",
      "is_active": true
    },
    {
      "id": 2,
      "name": "مطاعم وكافيهات",
      "is_active": true
    },
    {
      "id": 3,
      "name": "صيانة وإصلاح",
      "is_active": true
    },
    {
      "id": 4,
      "name": "تنظيف",
      "is_active": true
    },
    {
      "id": 5,
      "name": "رعاية صحية",
      "is_active": true
    }
  ]
}
```

### Response with English Language
```http
GET /api/vendor-services/service-types
Accept-Language: en
```

```json
{
  "status": 200,
  "message": "Service types retrieved successfully",
  "meta": null,
  "data": [
    {
      "id": 1,
      "name": "Transportation & Delivery",
      "is_active": true
    },
    {
      "id": 2,
      "name": "Restaurants & Cafes",
      "is_active": true
    },
    {
      "id": 3,
      "name": "Maintenance & Repair",
      "is_active": true
    },
    {
      "id": 4,
      "name": "Cleaning Services",
      "is_active": true
    },
    {
      "id": 5,
      "name": "Healthcare",
      "is_active": true
    }
  ]
}
```

### Notes
- No authentication required for this endpoint
- Returns only active service types
- Language automatically switches based on `Accept-Language` header

---

## 2️⃣ Get My Vendor Services

Get all vendor services created by the authenticated user.

### Endpoint
```http
GET /api/vendor-services
```

### Headers
```json
{
  "Authorization": "Bearer {token}",
  "Accept-Language": "ar",
  "Accept": "application/json"
}
```

### Success Response (200 OK)

```json
{
  "status": 200,
  "message": "تم استرجاع خدمات المزود بنجاح",
  "meta": null,
  "data": [
    {
      "id": 1,
      "service_type_id": 1,
      "service_type_name": "نقل وتوصيل",
      "title": "خدمة توصيل سريع",
      "description": "نوفر خدمة توصيل سريعة وموثوقة في جميع أنحاء المدينة",
      "price": 50.00,
      "currency": "AED",
      "location": "دبي، الإمارات",
      "contact_phone": "+971501234567",
      "contact_email": "service@example.com",
      "is_active": true,
      "images": [
        "http://your-domain.com/storage/vendor_services/image1.jpg",
        "http://your-domain.com/storage/vendor_services/image2.jpg"
      ],
      "created_at": "2025-10-29T10:30:00Z"
    },
    {
      "id": 2,
      "service_type_id": 2,
      "service_type_name": "مطاعم وكافيهات",
      "title": "مطعم الأصالة",
      "description": "أشهى المأكولات العربية التقليدية",
      "price": 75.00,
      "currency": "AED",
      "location": "أبوظبي، الإمارات",
      "contact_phone": "+971507654321",
      "contact_email": "restaurant@example.com",
      "is_active": true,
      "images": [],
      "created_at": "2025-10-28T15:20:00Z"
    }
  ]
}
```

### Error Responses

#### 401 Unauthorized
```json
{
  "status": 401,
  "message": "Unauthenticated.",
  "meta": null,
  "data": []
}
```

---

## 3️⃣ Get Vendor Service Details

Get detailed information about a specific vendor service.

### Endpoint
```http
GET /api/vendor-services/{id}
```

### Path Parameters
| Parameter | Type    | Required | Description           |
|-----------|---------|----------|-----------------------|
| id        | integer | Yes      | Vendor Service ID     |

### Headers
```json
{
  "Authorization": "Bearer {token}",
  "Accept-Language": "ar",
  "Accept": "application/json"
}
```

### Success Response (200 OK)

```json
{
  "status": 200,
  "message": "تم استرجاع تفاصيل الخدمة بنجاح",
  "meta": null,
  "data": {
    "id": 1,
    "user_id": 5,
    "user_name": "أحمد محمد",
    "user_email": "ahmed@example.com",
    "user_phone": "+971501234567",
    "service_type_id": 1,
    "service_type_name": "نقل وتوصيل",
    "title": "خدمة توصيل سريع",
    "description": "نوفر خدمة توصيل سريعة وموثوقة في جميع أنحاء المدينة. نعمل 24/7 مع أفضل الأسعار",
    "price": 50.00,
    "currency": "AED",
    "location": "دبي، الإمارات",
    "latitude": 25.2048,
    "longitude": 55.2708,
    "contact_phone": "+971501234567",
    "contact_email": "service@example.com",
    "contact_whatsapp": "+971501234567",
    "website_url": "https://example.com",
    "is_active": true,
    "rating": 4.5,
    "total_reviews": 120,
    "images": [
      "http://your-domain.com/storage/vendor_services/image1.jpg",
      "http://your-domain.com/storage/vendor_services/image2.jpg",
      "http://your-domain.com/storage/vendor_services/image3.jpg"
    ],
    "working_hours": {
      "saturday": "09:00 - 18:00",
      "sunday": "09:00 - 18:00",
      "monday": "09:00 - 18:00",
      "tuesday": "09:00 - 18:00",
      "wednesday": "09:00 - 18:00",
      "thursday": "09:00 - 18:00",
      "friday": "Closed"
    },
    "created_at": "2025-10-29T10:30:00Z",
    "updated_at": "2025-10-29T14:15:00Z"
  }
}
```

### Error Responses

#### 404 Not Found
```json
{
  "status": 404,
  "message": "الخدمة غير موجودة",
  "meta": null,
  "data": []
}
```

#### 403 Forbidden
```json
{
  "status": 403,
  "message": "غير مصرح لك بعرض هذه الخدمة",
  "meta": null,
  "data": []
}
```

---

## 4️⃣ Create Vendor Service

Create a new vendor service.

### Endpoint
```http
POST /api/vendor-services
```

### Headers
```json
{
  "Authorization": "Bearer {token}",
  "Accept-Language": "ar",
  "Content-Type": "multipart/form-data",
  "Accept": "application/json"
}
```

### Request Body (Form Data)

| Field             | Type    | Required | Description                           | Example                          |
|-------------------|---------|----------|---------------------------------------|----------------------------------|
| service_type_id   | integer | Yes      | Service type ID                       | 1                                |
| title             | string  | Yes      | Service title (max: 255)              | "خدمة توصيل سريع"                |
| description       | text    | Yes      | Service description                   | "نوفر خدمة توصيل..."             |
| price             | decimal | Yes      | Service price                         | 50.00                            |
| currency          | string  | No       | Currency code (default: AED)          | "AED"                            |
| location          | string  | Yes      | Service location                      | "دبي، الإمارات"                  |
| latitude          | decimal | No       | Location latitude                     | 25.2048                          |
| longitude         | decimal | No       | Location longitude                    | 55.2708                          |
| contact_phone     | string  | Yes      | Contact phone number                  | "+971501234567"                  |
| contact_email     | email   | No       | Contact email                         | "service@example.com"            |
| contact_whatsapp  | string  | No       | WhatsApp number                       | "+971501234567"                  |
| website_url       | url     | No       | Website URL                           | "https://example.com"            |
| is_active         | boolean | No       | Service status (default: true)        | true                             |
| images[]          | file    | No       | Service images (max 5, 5MB each)      | [image1.jpg, image2.jpg]         |
| working_hours     | json    | No       | Working hours as JSON object          | See example below                |

### Working Hours JSON Format
```json
{
  "saturday": "09:00 - 18:00",
  "sunday": "09:00 - 18:00",
  "monday": "09:00 - 18:00",
  "tuesday": "09:00 - 18:00",
  "wednesday": "09:00 - 18:00",
  "thursday": "09:00 - 18:00",
  "friday": "Closed"
}
```

### Example Request (cURL)

```bash
curl -X POST "http://your-domain.com/api/vendor-services" \
  -H "Authorization: Bearer {token}" \
  -H "Accept-Language: ar" \
  -F "service_type_id=1" \
  -F "title=خدمة توصيل سريع" \
  -F "description=نوفر خدمة توصيل سريعة وموثوقة" \
  -F "price=50.00" \
  -F "currency=AED" \
  -F "location=دبي، الإمارات" \
  -F "latitude=25.2048" \
  -F "longitude=55.2708" \
  -F "contact_phone=+971501234567" \
  -F "contact_email=service@example.com" \
  -F "images[]=@/path/to/image1.jpg" \
  -F "images[]=@/path/to/image2.jpg"
```

### Success Response (201 Created)

```json
{
  "status": 201,
  "message": "تم إنشاء الخدمة بنجاح",
  "meta": null,
  "data": {
    "id": 15,
    "service_type_id": 1,
    "service_type_name": "نقل وتوصيل",
    "title": "خدمة توصيل سريع",
    "description": "نوفر خدمة توصيل سريعة وموثوقة",
    "price": 50.00,
    "currency": "AED",
    "location": "دبي، الإمارات",
    "latitude": 25.2048,
    "longitude": 55.2708,
    "contact_phone": "+971501234567",
    "contact_email": "service@example.com",
    "is_active": true,
    "images": [
      "http://your-domain.com/storage/vendor_services/abc123.jpg",
      "http://your-domain.com/storage/vendor_services/def456.jpg"
    ],
    "created_at": "2025-10-29T16:30:00Z"
  }
}
```

### Validation Error Response (422 Unprocessable Entity)

```json
{
  "status": 422,
  "message": "خطأ في البيانات المدخلة",
  "meta": null,
  "data": {
    "errors": {
      "title": ["حقل العنوان مطلوب"],
      "price": ["حقل السعر يجب أن يكون رقم"],
      "contact_phone": ["حقل رقم الهاتف مطلوب"],
      "images.0": ["الصورة يجب أن لا تتجاوز 5 ميجابايت"]
    }
  }
}
```

---

## 5️⃣ Update Vendor Service

Update an existing vendor service.

### Endpoint
```http
POST /api/vendor-services/{id}
```
**Note:** Use POST with `_method=PUT` for file uploads, or use PUT without files.

### Path Parameters
| Parameter | Type    | Required | Description           |
|-----------|---------|----------|-----------------------|
| id        | integer | Yes      | Vendor Service ID     |

### Headers
```json
{
  "Authorization": "Bearer {token}",
  "Accept-Language": "ar",
  "Content-Type": "multipart/form-data",
  "Accept": "application/json"
}
```

### Request Body (Form Data)

All fields are **optional** - only include fields you want to update.

| Field             | Type    | Required | Description                           |
|-------------------|---------|----------|---------------------------------------|
| _method           | string  | Yes*     | "PUT" (only if using POST)            |
| service_type_id   | integer | No       | Service type ID                       |
| title             | string  | No       | Service title                         |
| description       | text    | No       | Service description                   |
| price             | decimal | No       | Service price                         |
| currency          | string  | No       | Currency code                         |
| location          | string  | No       | Service location                      |
| latitude          | decimal | No       | Location latitude                     |
| longitude         | decimal | No       | Location longitude                    |
| contact_phone     | string  | No       | Contact phone number                  |
| contact_email     | email   | No       | Contact email                         |
| contact_whatsapp  | string  | No       | WhatsApp number                       |
| website_url       | url     | No       | Website URL                           |
| is_active         | boolean | No       | Service status                        |
| images[]          | file    | No       | New service images                    |
| remove_images[]   | integer | No       | IDs of images to remove               |
| working_hours     | json    | No       | Working hours                         |

### Example Request (Update Title and Price)

```bash
curl -X POST "http://your-domain.com/api/vendor-services/15" \
  -H "Authorization: Bearer {token}" \
  -H "Accept-Language: ar" \
  -F "_method=PUT" \
  -F "title=خدمة توصيل سريع - محدثة" \
  -F "price=60.00"
```

### Example Request (Add New Images and Remove Old Ones)

```bash
curl -X POST "http://your-domain.com/api/vendor-services/15" \
  -H "Authorization: Bearer {token}" \
  -F "_method=PUT" \
  -F "images[]=@/path/to/new_image1.jpg" \
  -F "remove_images[]=1" \
  -F "remove_images[]=3"
```

### Success Response (200 OK)

```json
{
  "status": 200,
  "message": "تم تحديث خدمة البائع بنجاح وهي بانتظار موافقة الإدارة",
  "meta": null,
  "data": {
    "id": 15,
    "service_type_id": 1,
    "service_type_name": "نقل وتوصيل",
    "title": "خدمة توصيل سريع - محدثة",
    "description": "نوفر خدمة توصيل سريعة وموثوقة",
    "price": 60.00,
    "currency": "AED",
    "location": "دبي، الإمارات",
    "contact_phone": "+971501234567",
    "status": "pending",
    "is_active": true,
    "images": [
      "http://your-domain.com/storage/vendor_services/new_image1.jpg"
    ],
    "updated_at": "2025-10-29T17:45:00Z"
  }
}
```

### Important Notes
⚠️ **Status Change:** When updating an approved service, the status will automatically change to "pending" and require admin approval again. This ensures all changes are reviewed before going live.

### Error Responses

#### 403 Forbidden
```json
{
  "status": 403,
  "message": "غير مصرح لك بتحديث هذه الخدمة",
  "meta": null,
  "data": []
}
```

---

## 6️⃣ Delete Vendor Service (Disable)

Disable a vendor service (soft delete). The service can be re-enabled later.

### Endpoint
```http
DELETE /api/vendor-services/{id}
```

### Path Parameters
| Parameter | Type    | Required | Description           |
|-----------|---------|----------|-----------------------|
| id        | integer | Yes      | Vendor Service ID     |

### Headers
```json
{
  "Authorization": "Bearer {token}",
  "Accept-Language": "ar",
  "Accept": "application/json"
}
```

### Success Response (200 OK)

```json
{
  "status": 200,
  "message": "تم تعطيل الخدمة بنجاح",
  "meta": null,
  "data": []
}
```

### Error Responses

#### 403 Forbidden
```json
{
  "status": 403,
  "message": "غير مصرح لك بحذف هذه الخدمة",
  "meta": null,
  "data": []
}
```

#### 404 Not Found
```json
{
  "status": 404,
  "message": "الخدمة غير موجودة",
  "meta": null,
  "data": []
}
```

---

## 7️⃣ Enable Vendor Service

Re-enable (restore) a disabled vendor service.

### Endpoint
```http
POST /api/vendor-services/{id}/enable
```

### Path Parameters
| Parameter | Type    | Required | Description           |
|-----------|---------|----------|-----------------------|
| id        | integer | Yes      | Vendor Service ID     |

### Headers
```json
{
  "Authorization": "Bearer {token}",
  "Accept-Language": "ar",
  "Accept": "application/json"
}
```

### Example Request

```bash
curl -X POST "http://your-domain.com/api/vendor-services/15/enable" \
  -H "Authorization: Bearer {token}" \
  -H "Accept-Language: ar"
```

### Success Response (200 OK)

```json
{
  "status": 200,
  "message": "تم تفعيل خدمة البائع بنجاح وهي بانتظار موافقة الإدارة",
  "meta": null,
  "data": {
    "id": 15,
    "service_type_id": 1,
    "service_type_name": "نقل وتوصيل",
    "title": "خدمة توصيل سريع",
    "description": "نوفر خدمة توصيل سريعة وموثوقة",
    "price": 50.00,
    "currency": "AED",
    "location": "دبي، الإمارات",
    "contact_phone": "+971501234567",
    "is_active": true,
    "status": "pending",
    "images": [
      "http://your-domain.com/storage/vendor_services/abc123.jpg"
    ],
    "updated_at": "2025-10-29T18:30:00Z"
  }
}
```

### Success Response (English)
```json
{
  "status": 200,
  "message": "Vendor service enabled successfully and is pending admin approval.",
  "meta": null,
  "data": {
    "id": 15,
    "service_type_id": 1,
    "service_type_name": "Transportation & Delivery",
    "title": "Fast Delivery Service",
    "description": "We provide fast and reliable delivery",
    "price": 50.00,
    "currency": "AED",
    "location": "Dubai, UAE",
    "contact_phone": "+971501234567",
    "is_active": true,
    "status": "pending",
    "images": [
      "http://your-domain.com/storage/vendor_services/abc123.jpg"
    ],
    "updated_at": "2025-10-29T18:30:00Z"
  }
}
```

### Error Responses

#### 403 Forbidden (Already Active)
```json
{
  "status": 403,
  "message": "هذه الخدمة مفعلة بالفعل",
  "meta": null,
  "data": []
}
```

#### 403 Forbidden (Not Owner)
```json
{
  "status": 403,
  "message": "غير مصرح لك بتفعيل هذه الخدمة",
  "meta": null,
  "data": []
}
```

#### 404 Not Found
```json
{
  "status": 404,
  "message": "الخدمة غير موجودة",
  "meta": null,
  "data": []
}
```

### Notes
- Only disabled services can be re-enabled
- The service will return to "pending" status and require admin approval again
- Only the service owner can enable/disable their own services
- Use this endpoint after disabling a service if you want to reactivate it

---

## 8️⃣ Delete Vendor Service Permanently

Permanently delete a vendor service from the database. **This action cannot be undone!**

### Endpoint
```http
DELETE /api/vendor-services/{id}/forceDelete
```

### Path Parameters
| Parameter | Type    | Required | Description           |
|-----------|---------|----------|-----------------------|
| id        | integer | Yes      | Vendor Service ID     |

### Headers
```json
{
  "Authorization": "Bearer {token}",
  "Accept-Language": "ar",
  "Accept": "application/json"
}
```

### Example Request

```bash
curl -X DELETE "http://your-domain.com/api/vendor-services/15/forceDelete" \
  -H "Authorization: Bearer {token}" \
  -H "Accept-Language: ar"
```

### Success Response (200 OK)

```json
{
  "status": 200,
  "message": "تم حذف خدمة البائع نهائياً",
  "meta": null,
  "data": []
}
```

### Success Response (English)
```json
{
  "status": 200,
  "message": "Vendor service deleted permanently.",
  "meta": null,
  "data": []
}
```

### Error Responses

#### 403 Forbidden (Not Owner)
```json
{
  "status": 403,
  "message": "غير مصرح لك بحذف هذه الخدمة",
  "meta": null,
  "data": []
}
```

#### 404 Not Found
```json
{
  "status": 404,
  "message": "الخدمة غير موجودة",
  "meta": null,
  "data": []
}
```

### Important Notes
⚠️ **WARNING: This action is irreversible!**

- The service will be **permanently deleted** from the database
- All associated images will be deleted from storage
- Commercial register document will be deleted from storage
- The service **cannot be recovered** after deletion
- Only the service owner can permanently delete their own services
- Use this endpoint with caution

### Difference Between Delete Methods

| Feature | Disable (DELETE /{id}) | Permanent Delete (DELETE /{id}/forceDelete) |
|---------|------------------------|---------------------------------------------|
| Action | Changes status to "disabled" | Completely removes from database |
| Reversible | ✅ Yes (can be re-enabled) | ❌ No (cannot be recovered) |
| Images | Kept in storage | Deleted from storage |
| Data | Kept in database | Removed from database |
| Use Case | Temporary deactivation | Final removal |

**Recommendation:** Use disable (DELETE /{id}) for temporary deactivation. Only use permanent delete when you're absolutely sure you want to remove the service forever.

---

## 🔧 Common Error Responses

### 401 Unauthorized
Missing or invalid authentication token.
```json
{
  "status": 401,
  "message": "Unauthenticated.",
  "meta": null,
  "data": []
}
```

### 403 Forbidden
User doesn't have permission to perform this action.
```json
{
  "status": 403,
  "message": "غير مصرح لك بتنفيذ هذا الإجراء",
  "meta": null,
  "data": []
}
```

### 404 Not Found
Resource not found.
```json
{
  "status": 404,
  "message": "المورد المطلوب غير موجود",
  "meta": null,
  "data": []
}
```

### 422 Validation Error
Invalid input data.
```json
{
  "status": 422,
  "message": "خطأ في البيانات المدخلة",
  "meta": null,
  "data": {
    "errors": {
      "field_name": ["Error message here"]
    }
  }
}
```

### 500 Server Error
Internal server error.
```json
{
  "status": 500,
  "message": "حدث خطأ في الخادم",
  "meta": null,
  "data": []
}
```

---

## 📝 Notes for Frontend Developers

### 1. **Image Uploads**
- Maximum 5 images per service
- Each image must be ≤ 5MB
- Supported formats: JPG, JPEG, PNG, GIF, WEBP
- Use `multipart/form-data` content type
- Send images as array: `images[]`

### 2. **Language Switching**
- Use `Accept-Language` header
- Supported values: `ar` (Arabic), `en` (English)
- Default is Arabic if header is not provided

### 3. **Authentication**
- All endpoints (except service types) require authentication
- Use Sanctum Bearer token in Authorization header
- Token format: `Bearer {your_token_here}`

### 4. **Updating with Images**
- Use POST method with `_method=PUT`
- To remove images, send their IDs in `remove_images[]`
- New images can be added via `images[]`

### 5. **Working Hours**
- Send as JSON string or object
- Days in English: saturday, sunday, monday, etc.
- Format: "HH:MM - HH:MM" or "Closed"

### 6. **Location Data**
- `latitude` and `longitude` are optional but recommended
- Helps in map integration and location-based search
- Use decimal format with precision

---

## 🧪 Testing Examples

### JavaScript (Axios)

#### Get Service Types
```javascript
axios.get('/api/vendor-services/service-types', {
  headers: {
    'Accept-Language': 'ar'
  }
})
.then(response => console.log(response.data))
.catch(error => console.error(error));
```

#### Create Service
```javascript
const formData = new FormData();
formData.append('service_type_id', 1);
formData.append('title', 'خدمة توصيل سريع');
formData.append('description', 'نوفر خدمة توصيل سريعة');
formData.append('price', 50.00);
formData.append('location', 'دبي، الإمارات');
formData.append('contact_phone', '+971501234567');

// Add images
if (imageFiles.length > 0) {
  imageFiles.forEach(file => {
    formData.append('images[]', file);
  });
}

axios.post('/api/vendor-services', formData, {
  headers: {
    'Authorization': `Bearer ${token}`,
    'Accept-Language': 'ar',
    'Content-Type': 'multipart/form-data'
  }
})
.then(response => console.log(response.data))
.catch(error => console.error(error.response.data));
```

#### Update Service
```javascript
const formData = new FormData();
formData.append('_method', 'PUT');
formData.append('title', 'عنوان محدث');
formData.append('price', 60.00);

axios.post(`/api/vendor-services/${serviceId}`, formData, {
  headers: {
    'Authorization': `Bearer ${token}`,
    'Accept-Language': 'ar'
  }
})
.then(response => console.log(response.data))
.catch(error => console.error(error));
```

### React Example (with useState)

```jsx
import { useState } from 'react';
import axios from 'axios';

function CreateServiceForm() {
  const [formData, setFormData] = useState({
    service_type_id: '',
    title: '',
    description: '',
    price: '',
    location: '',
    contact_phone: ''
  });
  const [images, setImages] = useState([]);
  const [loading, setLoading] = useState(false);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);

    const data = new FormData();
    Object.keys(formData).forEach(key => {
      data.append(key, formData[key]);
    });

    images.forEach(image => {
      data.append('images[]', image);
    });

    try {
      const response = await axios.post('/api/vendor-services', data, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Accept-Language': 'ar'
        }
      });
      console.log('Success:', response.data);
      // Handle success (redirect, show message, etc.)
    } catch (error) {
      console.error('Error:', error.response.data);
      // Handle error (show validation errors, etc.)
    } finally {
      setLoading(false);
    }
  };

  return (
    <form onSubmit={handleSubmit}>
      {/* Form fields here */}
    </form>
  );
}
```

---

## 📞 Support

For any questions or issues, please contact:
- **Backend Team**: backend@seasonapp.com
- **Technical Lead**: tech@seasonapp.com

---

**Last Updated**: October 29, 2025  
**API Version**: 1.0  
**Document Version**: 1.0

