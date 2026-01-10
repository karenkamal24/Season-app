# API Differences: Digital Directory Categories vs Bag Items Categories
## الفروقات بين API التصنيفات للدليل الرقمي و API التصنيفات لعناصر الحقيبة

---

## 📋 جدول المحتويات

1. [نظرة عامة](#نظرة-عامة)
2. [الجدول المقارن](#الجدول-المقارن)
3. [التفاصيل](#التفاصيل)
4. [أمثلة الاستخدام](#أمثلة-الاستخدام)

---

## نظرة عامة

يوجد في النظام نوعان من التصنيفات:

1. **Digital Directory Categories** (تصنيفات الدليل الرقمي)
   - تستخدم للدليل الرقمي والتطبيقات (Food, Hotels, Transportation, etc.)
   - كل تصنيف يحتوي على تطبيقات مرتبطة به (Category Apps)

2. **Bag Items Categories** (تصنيفات عناصر الحقيبة)
   - تستخدم لتصنيف العناصر التي يمكن إضافتها في شنطة السفر (Boarding, Funds, Personal Essentials, etc.)
   - كل تصنيف يحتوي على عناصر مرتبطة به (Items)

---

## ⚠️ ملاحظة مهمة (Important Note)

**✅ تم حل التعارض (Conflict Resolved):**

تم تغيير مسارات Bag Items Categories لتجنب التعارض مع Digital Directory Categories:
- Digital Directory Categories: `/api/categories` (Public)
- Bag Items Categories: `/api/items/categories` (Protected) ✅

الآن كل API له مسار منفصل وواضح.

---

## الجدول المقارن

| الميزة | Digital Directory Categories | Bag Items Categories |
|--------|----------------------------|---------------------|
| **Base Route** | `/api/categories` | `/api/items/categories` ✅ |
| **Recommended Route** | `/api/digital-directory/categories` | `/api/items/categories` ✅ |
| **Authentication** | ❌ Public (لا يتطلب مصادقة) | ✅ Requires Authentication (يتطلب مصادقة) |
| **Purpose** | للدليل الرقمي والتطبيقات | لعناصر شنطة السفر |
| **Database Table** | `categories` | `item_categories` |
| **Model** | `Category` | `ItemCategory` |
| **Resource** | `CategoryResource` | `ItemCategoryResource` |
| **Controller** | `CategoryController` | `ItemController` |
| **Service** | `CategoryService` | `ItemService` |
| **Child Items** | Category Apps | Items |
| **Icon Field** | `icon_url` (accessor) | `icon` (direct field) |
| **Response ID Field** | `id` | `category_id` |
| **Show Single Category** | ✅ Available | ❌ Not available (commented out) |
| **Get Items/Apps by Category** | Via `/api/digital-directory/category-apps?category_id=X` | Via `/api/items?category_id=X` ✅ |
| **Headers Required** | `Accept-Language` | `Accept-Language` + `Authorization: Bearer Token` |
| **Country Filter** | ✅ Required for Category Apps (`Accept-Country`) | ❌ Not applicable |

---

## التفاصيل

### 1. المصادقة (Authentication)

#### Digital Directory Categories
```http
GET /api/categories
Accept-Language: ar
```
- **Public API**: لا يتطلب Bearer Token
- يمكن الوصول إليه بدون مصادقة

#### Bag Items Categories
```http
GET /api/items/categories
Authorization: Bearer YOUR_TOKEN
Accept-Language: ar
```
- **Protected API**: يتطلب Bearer Token
- يجب أن يكون المستخدم مسجل دخول

---

### 2. الـ Endpoints

#### Digital Directory Categories

1. **Get All Categories**
   ```
   GET /api/categories
   ```
   - Public
   - Returns: List of categories for digital directory

2. **Get Single Category**
   ```
   GET /api/categories/{id}
   ```
   - Public
   - Returns: Single category details

3. **Get Category Apps** (Related endpoint)
   ```
   GET /api/digital-directory/category-apps?category_id={id}
   Accept-Country: UAE|SAU|EGY
   ```
   - Public
   - Requires `Accept-Country` header
   - Returns: Apps related to the category

#### Bag Items Categories

1. **Get All Categories**
   ```
   GET /api/items/categories
   ```
   - Protected (requires auth)
   - Returns: List of item categories

2. **Get Items by Category**
   ```
   GET /api/items?category_id={id}
   ```
   - Protected (requires auth)
   - Returns: Items in the specified category

---

### 3. بنية البيانات (Response Structure)

#### Digital Directory Category Response
```json
{
  "status": 200,
  "message": "تم جلب التصنيفات بنجاح",
  "data": [
    {
      "id": 1,
      "name": "الطعام",
      "icon": "http://example.com/storage/categories/icons/food.png",
      "is_active": true
    }
  ]
}
```

**Fields:**
- `id`: معرف التصنيف
- `name`: اسم التصنيف (يعتمد على Accept-Language)
- `icon`: رابط كامل للأيقونة (icon_url accessor)
- `is_active`: حالة التصنيف

#### Bag Items Category Response
```json
{
  "status": 200,
  "message": "تم جلب فئات العناصر بنجاح",
  "data": [
    {
      "category_id": 1,
      "name": "الصعود",
      "icon": "https://cdn-icons-png.flaticon.com/512/190/190601.png"
    }
  ]
}
```

**Fields:**
- `category_id`: معرف التصنيف (ملاحظة: يستخدم `category_id` وليس `id`)
- `name`: اسم التصنيف (يعتمد على Accept-Language)
- `icon`: رابط الأيقونة (قد يكون URL خارجي أو مسار محلي)

---

### 4. قاعدة البيانات (Database)

#### Digital Directory Categories
**Table:** `categories`
```sql
- id
- name_ar
- name_en
- icon (file path)
- is_active
- created_at
- updated_at
```

**Model:** `App\Models\Category`
- Has `icon_url` accessor that returns full URL
- Related to `CategoryApp` model

#### Bag Items Categories
**Table:** `item_categories`
```sql
- id
- name_ar
- name_en
- icon (can be URL or path)
- icon_color
- sort_order
- is_active
- created_at
- updated_at
```

**Model:** `App\Models\ItemCategory`
- Has direct `icon` field
- Related to `Item` model
- Has `sort_order` for ordering

---

### 5. الاستخدام (Usage)

#### Digital Directory Categories
**الغرض:** عرض تصنيفات التطبيقات في الدليل الرقمي

**مثال الاستخدام:**
1. الحصول على جميع التصنيفات
2. عرض التصنيفات في واجهة المستخدم
3. عند اختيار تصنيف، جلب التطبيقات المتعلقة به
4. عرض التطبيقات مع فلترة حسب الدولة

**Flow:**
```
GET /api/categories 
  → User selects a category
  → GET /api/digital-directory/category-apps?category_id=X&Accept-Country=UAE
  → Display apps
```

#### Bag Items Categories
**الغرض:** عرض تصنيفات العناصر لإضافتها في شنطة السفر

**مثال الاستخدام:**
1. الحصول على جميع التصنيفات (بعد تسجيل الدخول)
2. عرض التصنيفات في واجهة المستخدم
3. عند اختيار تصنيف، جلب العناصر المتعلقة به
4. عرض العناصر للمستخدم لاختيارها وإضافتها للحقيبة

**Flow:**
```
GET /api/items/categories (with auth)
  → User selects a category
  → GET /api/items?category_id=X (with auth)
  → Display items
  → User selects items
  → POST /api/travel-bag/add-item (with auth)
```

---

### 6. الفلترة والترتيب (Filtering & Ordering)

#### Digital Directory Categories
- **Filtering:** By `is_active = true` only
- **Ordering:** Default database order (usually by ID)
- **Additional Filter:** Category Apps can be filtered by country (`Accept-Country` header)

#### Bag Items Categories
- **Filtering:** By `is_active = true` only
- **Ordering:** By `sort_order` field (explicit ordering)
- **No Country Filter:** Items are not country-specific

---

### 7. العلاقات (Relationships)

#### Digital Directory Categories
```
Category (1) ──→ (Many) CategoryApp
CategoryApp ──→ (Many) Country (many-to-many)
```

**Example:**
- Category: "Food"
- Category Apps: "Uber Eats", "Talabat", "Zomato"
- Each app can be available in multiple countries

#### Bag Items Categories
```
ItemCategory (1) ──→ (Many) Item
Item ──→ (Many) BagItem (when added to travel bag)
```

**Example:**
- ItemCategory: "Boarding"
- Items: "Passport", "Flight Ticket", "Boarding Pass"
- Items are universal (not country-specific)

---

## أمثلة الاستخدام

### مثال 1: Digital Directory Categories

#### Get All Categories (Public)
```bash
curl -X GET "https://seasonksa.com/api/categories" \
  -H "Accept-Language: ar"
```

#### Get Single Category (Public)
```bash
curl -X GET "https://seasonksa.com/api/categories/1" \
  -H "Accept-Language: ar"
```

#### Get Category Apps (Public - Requires Country)
```bash
curl -X GET "https://seasonksa.com/api/digital-directory/category-apps?category_id=1" \
  -H "Accept-Language: ar" \
  -H "Accept-Country: UAE"
```

---

### مثال 2: Bag Items Categories

#### Get All Categories (Protected)
```bash
curl -X GET "https://seasonksa.com/api/items/categories" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept-Language: ar"
```

#### Get Items by Category (Protected)
```bash
curl -X GET "https://seasonksa.com/api/items?category_id=1" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept-Language: ar"
```

---

## ملخص الاختلافات الرئيسية

### ✅ Digital Directory Categories
- ✅ Public API (لا يحتاج مصادقة)
- ✅ له endpoint للحصول على تصنيف واحد (`GET /api/categories/{id}`)
- ✅ يستخدم `id` في الـ response
- ✅ يستخدم `icon_url` accessor (رابط كامل)
- ✅ التطبيقات المرتبطة به قابلة للفلترة حسب الدولة
- ✅ مرتبط بـ `CategoryApp` model

### ✅ Bag Items Categories
- ✅ Protected API (يحتاج مصادقة)
- ❌ لا يوجد endpoint للحصول على تصنيف واحد
- ✅ يستخدم `category_id` في الـ response
- ✅ يستخدم `icon` مباشرة (قد يكون URL أو مسار)
- ✅ العناصر مرتبة حسب `sort_order`
- ✅ مرتبط بـ `Item` model
- ✅ العناصر تُستخدم لإضافتها في شنطة السفر

---

## الخلاصة

**Digital Directory Categories** و **Bag Items Categories** هما نظامان منفصلان تماماً:

1. **الغرض المختلف:**
   - Digital Directory → للتطبيقات والخدمات
   - Bag Items → للعناصر التي تُضاف في شنطة السفر

2. **الأمان:**
   - Digital Directory → Public (متاح للجميع)
   - Bag Items → Protected (للمستخدمين المسجلين فقط)

3. **البنية:**
   - Digital Directory → يركز على التطبيقات والبلدان
   - Bag Items → يركز على العناصر والأوزان

4. **الاستخدام:**
   - Digital Directory → عرض خدمات وتطبيقات حسب البلد
   - Bag Items → إدارة محتويات شنطة السفر

---

**آخر تحديث:** 2025-01-15

