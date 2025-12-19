# 📍 Geographical Guides API Documentation
## الأدلة الجغرافية - Season App

---

## 🎯 Overview

نظام كامل للأدلة الجغرافية يسمح للمستخدمين (التجار) بإنشاء خدمات جغرافية والبحث عنها حسب:
- ✅ الدولة والمدينة
- ✅ التصنيف والتصنيف الفرعي
- ✅ البحث والفلترة المتقدمة

---

## 🚀 API Endpoints

### Base URL
```
http://your-domain.com/api
```

### Headers
جميع الـ endpoints تدعم:
- `Accept-Language: ar` أو `en` (اختياري - الافتراضي: en)
- `Accept-Country: KSA` (مطلوب في بعض الـ endpoints - كود الدولة)

---

## 📋 1. Get Cities by Country

### Endpoint
```
GET /api/Location/cities
```

### Headers
```
Accept-Country: KSA
Accept-Language: ar
```

### Response
```json
{
  "status": 200,
  "message": "تم جلب المدن بنجاح",
  "data": [
    {
      "id": 1,
      "name_ar": "الرياض",
      "name_en": "Riyadh",
      "name": "الرياض",
      "country_id": 1
    },
    {
      "id": 2,
      "name_ar": "جدة",
      "name_en": "Jeddah",
      "name": "جدة",
      "country_id": 1
    }
  ]
}
```

**Note:** إذا لم يتم إرسال `Accept-Country` header، سيتم إرجاع جميع المدن.

---

## 📋 2. Get Geographical Categories

### Endpoint
```
GET /api/geographical-categories
```

### Headers (Optional)
```
Accept-Language: ar
```

### Response
```json
{
  "status": 200,
  "message": "Geographical categories fetched successfully",
  "data": [
    {
      "id": 1,
      "name_ar": "المطاعم والمقاهي",
      "name_en": "Restaurants & Cafes",
      "name": "المطاعم والمقاهي",
      "icon": "http://example.com/storage/icons/restaurant.png",
      "is_active": true,
      "sub_categories": [
        {
          "id": 1,
          "geographical_category_id": 1,
          "name_ar": "مطاعم عربية",
          "name_en": "Arabic Restaurants",
          "name": "مطاعم عربية",
          "is_active": true
        }
      ]
    }
  ]
}
```

### Get Single Category
```
GET /api/geographical-categories/{id}
```

---

## 📋 3. Get Geographical Sub-Categories

### Endpoint
```
GET /api/geographical-sub-categories
```

### Query Parameters (Optional)
- `geographical_category_id` - Filter by category ID

### Example
```
GET /api/geographical-sub-categories?geographical_category_id=1
```

### Response
```json
{
  "status": 200,
  "message": "Geographical sub-categories fetched successfully",
  "data": [
    {
      "id": 1,
      "geographical_category_id": 1,
      "name_ar": "مطاعم عربية",
      "name_en": "Arabic Restaurants",
      "name": "مطاعم عربية",
      "is_active": true
    }
  ]
}
```

### Get Single Sub-Category
```
GET /api/geographical-sub-categories/{id}
```

---

## 📋 4. Get Geographical Guides (Search/Filter)

### Endpoint
```
GET /api/geographical-guides
```

### Headers
```
Accept-Country: KSA
Accept-Language: ar
```

### Query Parameters (All Optional)
- `city_id` - Filter by city ID
- `geographical_category_id` - Filter by category ID
- `geographical_sub_category_id` - Filter by sub-category ID

### Example Request
```
GET /api/geographical-guides?city_id=1&geographical_category_id=1&geographical_sub_category_id=1
Headers:
  Accept-Country: KSA
  Accept-Language: ar
```

### Response
```json
{
  "status": 200,
  "message": "تم جلب الأدلة الجغرافية بنجاح.",
  "meta": null,
  "data": [
    {
      "id": 1,
      "user": {
        "id": 1,
        "name": "Ahmed Ali",
        "email": "ahmed@example.com"
      },
      "category": {
        "id": 1,
        "name_ar": "المطاعم والمقاهي",
        "name_en": "Restaurants & Cafes",
        "name": "المطاعم والمقاهي",
        "icon": "http://example.com/storage/icons/restaurant.png"
      },
      "sub_category": {
        "id": 1,
        "name_ar": "مطاعم عربية",
        "name_en": "Arabic Restaurants",
        "name": "مطاعم عربية"
      },
      "service_name": "مطعم الشام",
      "description": "مطعم يقدم الأكلات الشامية الأصيلة",
      "phone_1": "+966501234567",
      "phone_2": "+966501234568",
      "country": {
        "id": 1,
        "name_ar": "السعودية",
        "name_en": "Saudi Arabia",
        "name": "السعودية",
        "code": "KSA"
      },
      "city": {
        "id": 1,
        "name_ar": "الرياض",
        "name_en": "Riyadh",
        "name": "الرياض"
      },
      "address": "شارع الملك فهد، الرياض",
      "latitude": "24.71360000",
      "longitude": "46.67530000",
      "website": "https://example.com",
      "commercial_register": "http://example.com/storage/geographical_guides/commercial_registers/abc123.pdf",
      "is_active": true,
      "status": "موافق عليها",
      "created_at": "2025-12-15 23:55:37",
      "updated_at": "2025-12-15 23:55:37"
    }
  ]
}
```

**Note:** 
- يتم إرجاع الأدلة الموافق عليها فقط (`status: approved`)
- إذا تم إرسال `Accept-Country` header، سيتم فلترة النتائج حسب الدولة
- يمكن استخدام أي مجموعة من الفلاتر (city_id, category_id, sub_category_id)

---

## 📋 5. Get My Geographical Guides (User's Own Services)

### Endpoint
```
GET /api/geographical-guides/my-services
```

### Authentication
**Required:** `Authorization: Bearer TOKEN`

### Headers
```
Authorization: Bearer YOUR_TOKEN
Accept-Language: ar
```

### Response
```json
{
  "status": 200,
  "message": "تم جلب الأدلة الجغرافية بنجاح",
  "data": [
    {
        "id": 1,
        "user": {
            "id": 1,
            "name": "Ahmed Ali",
            "email": "ahmed@example.com"
        },
        "category": {
            "id": 1,
            "name_ar": "المطاعم والمقاهي",
            "name_en": "Restaurants & Cafes",
            "name": "المطاعم والمقاهي",
            "icon": null
        },
        "sub_category": {
            "id": 1,
            "name_ar": "مطاعم عربية",
            "name_en": "Arabic Restaurants",
            "name": "مطاعم عربية"
        },
        "service_name": "مطعم الشام",
        "description": "مطعم يقدم الأكلات الشامية الأصيلة",
        "phone_1": "+966501234567",
        "phone_2": "+966501234568",
        "country": {
            "id": 1,
            "name_ar": "السعودية",
            "name_en": "Saudi Arabia",
            "name": "السعودية",
            "code": "KSA"
        },
        "city": {
            "id": 1,
            "name_ar": "الرياض",
            "name_en": "Riyadh",
            "name": "الرياض"
        },
        "address": "شارع الملك فهد، الرياض",
        "latitude": "24.71360000",
        "longitude": "46.67530000",
        "website": "https://example.com",
        "commercial_register": "http://example.com/storage/geographical_guides/commercial_registers/abc123.pdf",
        "is_active": true,
        "status": "قيد المراجعة",
        "created_at": "2025-12-15 23:55:37",
        "updated_at": "2025-12-15 23:55:37"
    }
  ]
}
```

**Note:** 
- Returns all guides for the authenticated user regardless of status (pending, approved, rejected)
- Shows the current status of each guide
- User can see all their services in one place

---

## 📋 6. Get Single Geographical Guide

### Endpoint
```
GET /api/geographical-guides/{id}
```

### Headers (Optional)
```
  Accept-Language: ar
Authorization: Bearer TOKEN (optional - if viewing own guide, can see any status)
```

### Response
```json
{
    "status": 200,
  "message": "Geographical guide fetched successfully",
  "data": {
            "id": 1,
            "user": {
                "id": 1,
                "name": "Ahmed Ali",
                "email": "ahmed@example.com"
            },
            "category": {
                "id": 1,
                "name_ar": "المطاعم والمقاهي",
                "name_en": "Restaurants & Cafes",
                "name": "المطاعم والمقاهي",
                "icon": null
            },
            "sub_category": {
                "id": 1,
                "name_ar": "مطاعم عربية",
                "name_en": "Arabic Restaurants",
                "name": "مطاعم عربية"
            },
            "service_name": "مطعم الشام",
            "description": "مطعم يقدم الأكلات الشامية الأصيلة",
            "phone_1": "+966501234567",
            "phone_2": "+966501234568",
            "country": {
                "id": 1,
                "name_ar": "السعودية",
                "name_en": "Saudi Arabia",
                "name": "السعودية",
                "code": "KSA"
            },
            "city": {
                "id": 1,
                "name_ar": "الرياض",
                "name_en": "Riyadh",
                "name": "الرياض"
            },
            "address": "شارع الملك فهد، الرياض",
            "latitude": "24.71360000",
            "longitude": "46.67530000",
            "website": "https://example.com",
            "commercial_register": "http://example.com/storage/geographical_guides/commercial_registers/abc123.pdf",
            "is_active": true,
            "status": "موافق عليها",
            "created_at": "2025-12-15 23:55:37",
            "updated_at": "2025-12-15 23:55:37"
        }
}
```

**Note:**
- Public endpoint - shows approved guides only
- If authenticated and viewing own guide, can see any status (pending, approved, rejected)

---

## 📋 7. Create Geographical Guide (Trader/Service Provider)

### Endpoint
```
POST /api/geographical-guides
```

### Authentication
**Required:** `Authorization: Bearer TOKEN`

### Headers
```
Authorization: Bearer YOUR_TOKEN
Content-Type: multipart/form-data
Accept-Language: ar
```

### Request Body (Form Data)
```json
{
  "geographical_category_id": 1,              // Required
  "geographical_sub_category_id": 1,          // Optional
  "service_name": "مطعم الشام",                // Required, max:255
  "description": "مطعم يقدم الأكلات الشامية",  // Optional, max:1000
  "phone_1": "+966501234567",                 // Optional, max:20
  "phone_2": "+966501234568",                 // Optional, max:20
  "country_id": 1,                            // Required
  "city_id": 1,                               // Required
  "address": "شارع الملك فهد، الرياض",         // Optional, max:500
  "latitude": "24.71360000",                  // Optional, numeric, -90 to 90
  "longitude": "46.67530000",                 // Optional, numeric, -180 to 180
  "website": "https://example.com",           // Optional, valid URL, max:255
  "commercial_register": "file"                // Optional, file (PDF, JPG, JPEG, PNG), max:5MB
}
```

### Validation Rules
- `geographical_category_id`: required, exists in geographical_categories
- `geographical_sub_category_id`: optional, must belong to the selected category
- `service_name`: required, string, max:255
- `description`: optional, string, max:1000
- `phone_1`, `phone_2`: optional, string, max:20
- `country_id`: required, exists in countries
- `city_id`: required, must belong to the selected country
- `address`: optional, string, max:500
- `latitude`: optional, numeric, between -90 and 90
- `longitude`: optional, numeric, between -180 and 180
- `website`: optional, valid URL, max:255
- `commercial_register`: optional, file, mimes:pdf,jpg,jpeg,png, max:5120KB

### Response (Success)
```json
{
  "status": 201,
  "message": "تم إنشاء الدليل الجغرافي بنجاح",
  "data": {
    "id": 1,
    "user": {
      "id": 1,
      "name": "Ahmed Ali",
      "email": "ahmed@example.com"
    },
    "category": {
      "id": 1,
      "name_ar": "المطاعم والمقاهي",
      "name_en": "Restaurants & Cafes",
      "name": "المطاعم والمقاهي",
      "icon": null
    },
    "sub_category": {
      "id": 1,
      "name_ar": "مطاعم عربية",
      "name_en": "Arabic Restaurants",
      "name": "مطاعم عربية"
    },
    "service_name": "مطعم الشام",
    "description": "مطعم يقدم الأكلات الشامية الأصيلة",
    "phone_1": "+966501234567",
    "phone_2": "+966501234568",
    "country": {
      "id": 1,
      "name_ar": "السعودية",
      "name_en": "Saudi Arabia",
      "name": "السعودية",
      "code": "KSA"
    },
    "city": {
      "id": 1,
      "name_ar": "الرياض",
      "name_en": "Riyadh",
      "name": "الرياض"
    },
    "address": "شارع الملك فهد، الرياض",
    "latitude": "24.71360000",
    "longitude": "46.67530000",
    "website": "https://example.com",
    "commercial_register": "http://example.com/storage/geographical_guides/commercial_registers/abc123.pdf",
    "is_active": true,
    "status": "قيد المراجعة",
    "created_at": "2025-12-15 23:55:37",
    "updated_at": "2025-12-15 23:55:37"
  }
}
```

### Important Notes
- ✅ بعد إنشاء الدليل، يتم تحديث `is_seller` للمستخدم إلى `true` تلقائياً
- ✅ الحالة (`status`) تكون دائماً `pending` (قيد المراجعة) - يحتاج موافقة من الإدارة
- ✅ الدليل لن يظهر في نتائج البحث حتى يتم الموافقة عليه (`status: approved`)
- ✅ يتم التحقق تلقائياً من أن المدينة تنتمي للدولة المحددة
- ✅ يتم التحقق تلقائياً من أن التصنيف الفرعي ينتمي للتصنيف المحدد

### Error Response
```json
{
  "status": 422,
  "message": "المدينة المحددة لا تنتمي إلى الدولة المحددة",
    "data": []
}
```

---

## 📋 8. Update Geographical Guide

### Endpoint
```
PUT /api/geographical-guides/{id}
```

### Authentication
**Required:** `Authorization: Bearer TOKEN`

### Headers
```
Authorization: Bearer YOUR_TOKEN
Content-Type: multipart/form-data
Accept-Language: ar
```

### Request Body (Form Data - All fields optional)
```json
{
  "geographical_category_id": 1,              // Optional
  "geographical_sub_category_id": 1,          // Optional
  "service_name": "مطعم الشام المحدث",          // Optional, max:255
  "description": "مطعم محدث يقدم الأكلات الشامية", // Optional, max:1000
  "phone_1": "+966501234567",                 // Optional, max:20
  "phone_2": "+966501234568",                 // Optional, max:20
  "country_id": 1,                            // Optional
  "city_id": 1,                               // Optional
  "address": "شارع الملك فهد، الرياض",         // Optional, max:500
  "latitude": "24.71360000",                  // Optional, numeric, -90 to 90
  "longitude": "46.67530000",                 // Optional, numeric, -180 to 180
  "website": "https://example.com",           // Optional, valid URL, max:255
  "commercial_register": "file"                // Optional, file (PDF, JPG, JPEG, PNG), max:5MB
}
```

### Response (Success)
```json
{
  "status": 200,
  "message": "تم تحديث الدليل الجغرافي بنجاح",
  "data": {
    "id": 1,
    "service_name": "مطعم الشام المحدث",
    "status": "قيد المراجعة",
    ...
  }
}
```

### Important Notes
- ✅ User can only update their own guides
- ✅ If guide was `approved` and is being edited, status automatically changes to `pending` (requires admin re-approval)
- ✅ Old commercial register file is deleted when uploading a new one
- ✅ All validation rules same as create endpoint
- ✅ Only send fields you want to update (partial update supported)

---

## 📋 9. Delete Geographical Guide

### Endpoint
```
DELETE /api/geographical-guides/{id}
```

### Authentication
**Required:** `Authorization: Bearer TOKEN`

### Headers
```
Authorization: Bearer YOUR_TOKEN
Accept-Language: ar
```

### Response (Success)
```json
{
  "status": 200,
  "message": "تم حذف الدليل الجغرافي بنجاح",
  "data": []
}
```

### Important Notes
- ✅ User can only delete their own guides
- ✅ Commercial register file is automatically deleted
- ✅ Guide is permanently deleted from database

---

## 🔄 Typical Flow for Mobile App

### Step 1: Get Cities
```
GET /api/Location/cities
Headers: Accept-Country: KSA
```
→ User selects a city

### Step 2: Get Categories
```
GET /api/geographical-categories
```
→ User selects a category

### Step 3: Get Sub-Categories (Optional)
```
GET /api/geographical-sub-categories?geographical_category_id=1
```
→ User selects a sub-category (optional)

### Step 4: Search Guides
```
GET /api/geographical-guides?city_id=1&geographical_category_id=1&geographical_sub_category_id=1
Headers: 
  Accept-Country: KSA
  Accept-Language: ar
```
→ Display results to user

### Step 5: Create Guide (Trader)
```
POST /api/geographical-guides
Headers: 
  Authorization: Bearer TOKEN
  Content-Type: multipart/form-data
Body: Form data with all required fields
```
→ Guide created with status "pending"

### Step 6: View My Services (Trader)
```
GET /api/geographical-guides/my-services
Headers: 
  Authorization: Bearer TOKEN
```
→ Display all user's guides with their statuses

### Step 7: Edit Service (Trader)
```
PUT /api/geographical-guides/{id}
Headers: 
  Authorization: Bearer TOKEN
  Content-Type: multipart/form-data
Body: Form data with fields to update
```
→ Guide updated, status changes to "pending" if it was "approved"

### Step 8: Delete Service (Trader)
```
DELETE /api/geographical-guides/{id}
Headers: 
  Authorization: Bearer TOKEN
```
→ Guide deleted permanently

---

## 📱 Mobile Integration Example

### Flutter/Dart Example

```dart
// 1. Get cities by country
Future<List<City>> getCities(String countryCode) async {
  final response = await http.get(
    Uri.parse('$baseUrl/api/Location/cities'),
    headers: {
      'Accept-Country': countryCode,
      'Accept-Language': 'ar',
    },
  );
  // Parse response...
}

// 2. Get categories
Future<List<Category>> getCategories() async {
  final response = await http.get(
    Uri.parse('$baseUrl/api/geographical-categories'),
    headers: {'Accept-Language': 'ar'},
  );
  // Parse response...
}

// 3. Search guides
Future<List<GeographicalGuide>> searchGuides({
  String? countryCode,
  int? cityId,
  int? categoryId,
  int? subCategoryId,
}) async {
  final queryParams = <String, String>{};
  if (cityId != null) queryParams['city_id'] = cityId.toString();
  if (categoryId != null) queryParams['geographical_category_id'] = categoryId.toString();
  if (subCategoryId != null) queryParams['geographical_sub_category_id'] = subCategoryId.toString();
  
  final uri = Uri.parse('$baseUrl/api/geographical-guides')
      .replace(queryParameters: queryParams);
  
  final response = await http.get(
    uri,
    headers: {
      if (countryCode != null) 'Accept-Country': countryCode,
      'Accept-Language': 'ar',
    },
  );
  // Parse response...
}

// 4. Get my services (trader)
Future<List<GeographicalGuide>> getMyServices({
  required String token,
}) async {
  final response = await http.get(
    Uri.parse('$baseUrl/api/geographical-guides/my-services'),
    headers: {
      'Authorization': 'Bearer $token',
      'Accept-Language': 'ar',
    },
  );
  // Parse response...
}

// 5. Get single guide
Future<GeographicalGuide> getGuide({
  int? guideId,
  String? token,
}) async {
  final headers = <String, String>{
    'Accept-Language': 'ar',
  };
  if (token != null) {
    headers['Authorization'] = 'Bearer $token';
  }
  
  final response = await http.get(
    Uri.parse('$baseUrl/api/geographical-guides/$guideId'),
    headers: headers,
  );
  // Parse response...
}

// 6. Create guide (trader)
Future<GeographicalGuide> createGuide({
  required String token,
  required int categoryId,
  int? subCategoryId,
  required String serviceName,
  String? description,
  String? phone1,
  String? phone2,
  required int countryId,
  required int cityId,
  String? address,
  double? latitude,
  double? longitude,
  String? website,
  File? commercialRegister,
}) async {
  final request = http.MultipartRequest(
    'POST',
    Uri.parse('$baseUrl/api/geographical-guides'),
  );
  
  request.headers.addAll({
    'Authorization': 'Bearer $token',
    'Accept-Language': 'ar',
  });
  
  request.fields.addAll({
    'geographical_category_id': categoryId.toString(),
    if (subCategoryId != null) 'geographical_sub_category_id': subCategoryId.toString(),
    'service_name': serviceName,
    if (description != null) 'description': description,
    if (phone1 != null) 'phone_1': phone1,
    if (phone2 != null) 'phone_2': phone2,
    'country_id': countryId.toString(),
    'city_id': cityId.toString(),
    if (address != null) 'address': address,
    if (latitude != null) 'latitude': latitude.toString(),
    if (longitude != null) 'longitude': longitude.toString(),
    if (website != null) 'website': website,
  });
  
  if (commercialRegister != null) {
    request.files.add(
      await http.MultipartFile.fromPath(
        'commercial_register',
        commercialRegister.path,
      ),
    );
  }
  
  final response = await request.send();
  // Parse response...
}

// 7. Update guide (trader)
Future<GeographicalGuide> updateGuide({
  required String token,
  required int guideId,
  int? categoryId,
  int? subCategoryId,
  String? serviceName,
  String? description,
  String? phone1,
  String? phone2,
  int? countryId,
  int? cityId,
  String? address,
  double? latitude,
  double? longitude,
  String? website,
  File? commercialRegister,
}) async {
  final request = http.MultipartRequest(
    'PUT',
    Uri.parse('$baseUrl/api/geographical-guides/$guideId'),
  );
  
  request.headers.addAll({
    'Authorization': 'Bearer $token',
    'Accept-Language': 'ar',
  });
  
  if (categoryId != null) request.fields['geographical_category_id'] = categoryId.toString();
  if (subCategoryId != null) request.fields['geographical_sub_category_id'] = subCategoryId.toString();
  if (serviceName != null) request.fields['service_name'] = serviceName;
  if (description != null) request.fields['description'] = description;
  if (phone1 != null) request.fields['phone_1'] = phone1;
  if (phone2 != null) request.fields['phone_2'] = phone2;
  if (countryId != null) request.fields['country_id'] = countryId.toString();
  if (cityId != null) request.fields['city_id'] = cityId.toString();
  if (address != null) request.fields['address'] = address;
  if (latitude != null) request.fields['latitude'] = latitude.toString();
  if (longitude != null) request.fields['longitude'] = longitude.toString();
  if (website != null) request.fields['website'] = website;
  
  if (commercialRegister != null) {
    request.files.add(
      await http.MultipartFile.fromPath(
        'commercial_register',
        commercialRegister.path,
      ),
    );
  }
  
  final response = await request.send();
  // Parse response...
}

// 8. Delete guide (trader)
Future<void> deleteGuide({
  required String token,
  required int guideId,
}) async {
  final response = await http.delete(
    Uri.parse('$baseUrl/api/geographical-guides/$guideId'),
    headers: {
      'Authorization': 'Bearer $token',
      'Accept-Language': 'ar',
    },
  );
  // Handle response...
}
```

---

## ⚠️ Important Notes

1. **Country Code Format**: Use uppercase (e.g., `KSA`, `UAE`, `EGY`)
2. **Language Support**: All endpoints support `Accept-Language: ar` or `en`
3. **Status Values**: 
   - `pending` = قيد المراجعة (Pending)
   - `approved` = موافق عليها (Approved)
   - `rejected` = مرفوضة (Rejected)
4. **File Upload**: Commercial register file must be PDF, JPG, JPEG, or PNG (max 5MB)
5. **Coordinates**: Latitude (-90 to 90), Longitude (-180 to 180)
6. **Approval Process**: New guides require admin approval before appearing in search results

---

## 🧪 Testing Examples

### cURL Examples

```bash
# 1. Get cities
curl -X GET "http://localhost:8000/api/Location/cities" \
  -H "Accept-Country: KSA" \
  -H "Accept-Language: ar"

# 2. Get categories
curl -X GET "http://localhost:8000/api/geographical-categories" \
  -H "Accept-Language: ar"

# 3. Search guides
curl -X GET "http://localhost:8000/api/geographical-guides?city_id=1&geographical_category_id=1" \
  -H "Accept-Country: KSA" \
  -H "Accept-Language: ar"

# 4. Get my services
curl -X GET "http://localhost:8000/api/geographical-guides/my-services" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept-Language: ar"

# 5. Get single guide
curl -X GET "http://localhost:8000/api/geographical-guides/1" \
  -H "Accept-Language: ar"

# 6. Create guide
curl -X POST "http://localhost:8000/api/geographical-guides" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept-Language: ar" \
  -F "geographical_category_id=1" \
  -F "geographical_sub_category_id=1" \
  -F "service_name=مطعم الشام" \
  -F "description=مطعم يقدم الأكلات الشامية" \
  -F "phone_1=+966501234567" \
  -F "country_id=1" \
  -F "city_id=1" \
  -F "address=شارع الملك فهد" \
  -F "latitude=24.7136" \
  -F "longitude=46.6753" \
  -F "website=https://example.com" \
  -F "commercial_register=@/path/to/file.pdf"

# 7. Update guide
curl -X PUT "http://localhost:8000/api/geographical-guides/1" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept-Language: ar" \
  -F "service_name=مطعم الشام المحدث" \
  -F "description=مطعم محدث يقدم الأكلات الشامية"

# 8. Delete guide
curl -X DELETE "http://localhost:8000/api/geographical-guides/1" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept-Language: ar"
```

---

## 📞 Support

For any questions or issues, please contact the backend team.

---

**Last Updated:** December 2025
