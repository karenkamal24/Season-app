# 🧪 سيناريوهات اختبار نظام المجموعات على Postman

## 📋 متطلبات التجربة

### 1️⃣ ستحتاج إلى:
- ✅ حسابين مستخدمين (User 1 & User 2) للتجربة الكاملة
- ✅ Bearer Tokens لكل مستخدم
- ✅ تطبيق Postman

---

## 🎯 السيناريوهات بالترتيب

---

## 📦 السيناريو 1: إعداد المستخدمين

### 1.1 تسجيل المستخدم الأول (Owner)
```http
POST http://localhost:8000/api/auth/register
Content-Type: application/json

{
  "name": "أحمد محمد",
  "email": "ahmed@test.com",
  "password": "12345678",
  "password_confirmation": "12345678",
  "phone": "966501234567",
  "country_id": 1,
  "city_id": 1
}
```

**حفظ:** `user1_token` من الرد

---

### 1.2 تحقق OTP للمستخدم الأول
```http
POST http://localhost:8000/api/auth/verify-otp
Content-Type: application/json

{
  "email": "ahmed@test.com",
  "otp": "123456"
}
```

**حفظ:** `user1_token` الجديد من الرد

---

### 1.3 تسجيل المستخدم الثاني (Member)
```http
POST http://localhost:8000/api/auth/register
Content-Type: application/json

{
  "name": "سارة أحمد",
  "email": "sara@test.com",
  "password": "12345678",
  "password_confirmation": "12345678",
  "phone": "966509876543",
  "country_id": 1,
  "city_id": 1
}
```

**حفظ:** `user2_token` من الرد

---

### 1.4 تحقق OTP للمستخدم الثاني
```http
POST http://localhost:8000/api/auth/verify-otp
Content-Type: application/json

{
  "email": "sara@test.com",
  "otp": "123456"
}
```

**حفظ:** `user2_token` الجديد من الرد

---

## 🏗️ السيناريو 2: إنشاء مجموعة

### 2.1 إنشاء مجموعة جديدة (User 1)
```http
POST http://localhost:8000/api/groups
Authorization: Bearer {{user1_token}}
Content-Type: application/json

{
  "name": "رحلة دبي - العائلة",
  "description": "مجموعة العائلة رحلة دبي 2025",
  "safety_radius": 100,
  "notifications_enabled": true
}
```

**الرد المتوقع:**
```json
{
  "status": 201,
  "message": "تم إنشاء المجموعة بنجاح",
  "data": {
    "id": 1,
    "name": "رحلة دبي - العائلة",
    "invite_code": "SEASON-ABC123",
    "qr_code": "base64_encoded_qr_code",
    "owner": {
      "id": 1,
      "name": "أحمد محمد"
    }
  }
}
```

**احفظ من الرد:**
- `group_id` → سيكون 1
- `invite_code` → مثلاً "SEASON-ABC123"

---

### 2.2 عرض كل مجموعات المستخدم الأول
```http
GET http://localhost:8000/api/groups
Authorization: Bearer {{user1_token}}
```

**الرد المتوقع:**
```json
{
  "status": 200,
  "message": "تم جلب المجموعات بنجاح",
  "data": [
    {
      "id": 1,
      "name": "رحلة دبي - العائلة",
      "members_count": 1,
      "out_of_range_count": 0
    }
  ]
}
```

---

## 👥 السيناريو 3: انضمام الأعضاء

### 3.1 معلومات الدعوة (اختياري - لا يحتاج Authentication)
```http
GET http://localhost:8000/api/groups/invite/SEASON-ABC123
```

**الرد المتوقع:**
```json
{
  "status": 200,
  "message": "تم جلب معلومات الدعوة بنجاح",
  "data": {
    "id": 1,
    "name": "رحلة دبي - العائلة",
    "description": "مجموعة العائلة رحلة دبي 2025",
    "owner": "أحمد محمد",
    "members_count": 1,
    "invite_code": "SEASON-ABC123"
  }
}
```

---

### 3.2 انضمام المستخدم الثاني للمجموعة
```http
POST http://localhost:8000/api/groups/join
Authorization: Bearer {{user2_token}}
Content-Type: application/json

{
  "invite_code": "SEASON-ABC123"
}
```

**الرد المتوقع:**
```json
{
  "status": 200,
  "message": "تم الانضمام للمجموعة بنجاح",
  "data": {
    "id": 1,
    "name": "رحلة دبي - العائلة",
    "members_count": 2
  }
}
```

---

### 3.3 عرض تفاصيل المجموعة مع الأعضاء
```http
GET http://localhost:8000/api/groups/1
Authorization: Bearer {{user1_token}}
```

**الرد المتوقع:**
```json
{
  "status": 200,
  "message": "تم جلب بيانات المجموعة بنجاح",
  "data": {
    "id": 1,
    "name": "رحلة دبي - العائلة",
    "members_count": 2,
    "out_of_range_count": 0,
    "members": [
      {
        "id": 1,
        "name": "أحمد محمد",
        "role": "owner",
        "is_within_radius": null,
        "latest_location": null
      },
      {
        "id": 2,
        "name": "سارة أحمد",
        "role": "member",
        "is_within_radius": null,
        "latest_location": null
      }
    ]
  }
}
```

---

### 3.4 عرض أعضاء المجموعة بالتفصيل
```http
GET http://localhost:8000/api/groups/1/members
Authorization: Bearer {{user1_token}}
```

---

## 📍 السيناريو 4: تتبع المواقع

### 4.1 المستخدم الأول يحدث موقعه (داخل النطاق)
```http
POST http://localhost:8000/api/groups/1/location
Authorization: Bearer {{user1_token}}
Content-Type: application/json

{
  "latitude": 25.2048,
  "longitude": 55.2708
}
```

**الرد المتوقع:**
```json
{
  "status": 200,
  "message": "تم تحديث الموقع بنجاح",
  "data": {
    "id": 1,
    "latitude": 25.2048,
    "longitude": 55.2708,
    "distance_from_center": 0,
    "is_within_radius": true,
    "created_at": "2025-10-27T10:30:00Z"
  }
}
```

---

### 4.2 المستخدم الثاني يحدث موقعه (داخل النطاق)
```http
POST http://localhost:8000/api/groups/1/location
Authorization: Bearer {{user2_token}}
Content-Type: application/json

{
  "latitude": 25.2050,
  "longitude": 55.2710
}
```

**الرد المتوقع:**
```json
{
  "status": 200,
  "message": "تم تحديث الموقع بنجاح",
  "data": {
    "id": 2,
    "latitude": 25.2050,
    "longitude": 55.2710,
    "distance_from_center": 28.5,
    "is_within_radius": true
  }
}
```

✅ **لن يتم إرسال إشعارات لأن الجميع داخل النطاق**

---

### 4.3 المستخدم الثاني يخرج من النطاق (خارج 100 متر)
```http
POST http://localhost:8000/api/groups/1/location
Authorization: Bearer {{user2_token}}
Content-Type: application/json

{
  "latitude": 25.2150,
  "longitude": 55.2800
}
```

**الرد المتوقع:**
```json
{
  "status": 200,
  "message": "تم تحديث الموقع بنجاح",
  "data": {
    "id": 3,
    "latitude": 25.2150,
    "longitude": 55.2800,
    "distance_from_center": 150.5,
    "is_within_radius": false
  }
}
```

🔔 **يتم إرسال إشعار push تلقائياً للمستخدم الأول:**
```json
{
  "title": "تنبيه: عضو خارج النطاق",
  "body": "سارة أحمد تجاوز المسافة المحددة (100متر)",
  "data": {
    "type": "out_of_range",
    "group_id": "1",
    "user_id": "2",
    "user_name": "سارة أحمد"
  }
}
```

---

### 4.4 عرض المجموعة بعد تحديث المواقع
```http
GET http://localhost:8000/api/groups/1
Authorization: Bearer {{user1_token}}
```

**الرد المتوقع:**
```json
{
  "status": 200,
  "message": "تم جلب بيانات المجموعة بنجاح",
  "data": {
    "id": 1,
    "name": "رحلة دبي - العائلة",
    "members_count": 2,
    "out_of_range_count": 1,
    "members": [
      {
        "id": 1,
        "name": "أحمد محمد",
        "role": "owner",
        "is_within_radius": true,
        "out_of_range_count": 0,
        "latest_location": {
          "latitude": 25.2048,
          "longitude": 55.2708,
          "distance_from_center": 0
        }
      },
      {
        "id": 2,
        "name": "سارة أحمد",
        "role": "member",
        "is_within_radius": false,
        "out_of_range_count": 1,
        "latest_location": {
          "latitude": 25.2150,
          "longitude": 55.2800,
          "distance_from_center": 150.5
        }
      }
    ]
  }
}
```

---

## 🚨 السيناريو 5: إشارات الطوارئ (SOS)

### 5.1 المستخدم الثاني يرسل إشارة SOS
```http
POST http://localhost:8000/api/groups/1/sos
Authorization: Bearer {{user2_token}}
Content-Type: application/json

{
  "latitude": 25.2150,
  "longitude": 55.2800,
  "message": "أحتاج المساعدة فوراً! ضعت الطريق"
}
```

**الرد المتوقع:**
```json
{
  "status": 200,
  "message": "تم إرسال إشارة SOS بنجاح",
  "data": {
    "id": 1,
    "group_id": 1,
    "user": {
      "id": 2,
      "name": "سارة أحمد"
    },
    "message": "أحتاج المساعدة فوراً! ضعت الطريق",
    "latitude": 25.2150,
    "longitude": 55.2800,
    "status": "active",
    "created_at": "2025-10-27T10:35:00Z"
  }
}
```

🚨 **يتم إرسال إشعار طوارئ فوراً للمستخدم الأول:**
```json
{
  "title": "🚨 إشارة SOS - طوارئ",
  "body": "سارة أحمد يحتاج المساعدة! أحتاج المساعدة فوراً! ضعت الطريق",
  "data": {
    "type": "sos_alert",
    "group_id": "1",
    "alert_id": "1",
    "user_id": "2",
    "latitude": "25.2150",
    "longitude": "55.2800"
  }
}
```

**احفظ:** `alert_id` → سيكون 1

---

### 5.2 عرض تفاصيل المجموعة مع الـ SOS النشط
```http
GET http://localhost:8000/api/groups/1
Authorization: Bearer {{user1_token}}
```

**سترى في الرد:**
```json
{
  "data": {
    "active_sos_alerts": [
      {
        "id": 1,
        "user": "سارة أحمد",
        "message": "أحتاج المساعدة فوراً! ضعت الطريق",
        "latitude": 25.2150,
        "longitude": 55.2800,
        "status": "active"
      }
    ]
  }
}
```

---

### 5.3 حل/إغلاق إشارة SOS (أي عضو في المجموعة)
```http
POST http://localhost:8000/api/groups/1/sos/1/resolve
Authorization: Bearer {{user1_token}}
```

**الرد المتوقع:**
```json
{
  "status": 200,
  "message": "تم إغلاق إشارة SOS",
  "data": {
    "id": 1,
    "status": "resolved",
    "resolved_at": "2025-10-27T10:40:00Z"
  }
}
```

---

## ✏️ السيناريو 6: تعديل المجموعة

### 6.1 تحديث بيانات المجموعة (Owner فقط)
```http
PUT http://localhost:8000/api/groups/1
Authorization: Bearer {{user1_token}}
Content-Type: application/json

{
  "name": "رحلة دبي - العائلة (محدثة)",
  "description": "مجموعة العائلة رحلة دبي 2025 - الإصدار المحدث",
  "safety_radius": 200,
  "notifications_enabled": true
}
```

**الرد المتوقع:**
```json
{
  "status": 200,
  "message": "تم تحديث المجموعة بنجاح",
  "data": {
    "id": 1,
    "name": "رحلة دبي - العائلة (محدثة)",
    "safety_radius": 200
  }
}
```

---

### 6.2 محاولة تحديث المجموعة من غير Owner (يجب أن تفشل)
```http
PUT http://localhost:8000/api/groups/1
Authorization: Bearer {{user2_token}}
Content-Type: application/json

{
  "name": "محاولة تغيير الاسم"
}
```

**الرد المتوقع:**
```json
{
  "status": 404,
  "message": "No query results for model"
}
```

---

## 👋 السيناريو 7: المغادرة والإزالة

### 7.1 إزالة عضو من المجموعة (Owner فقط)
```http
DELETE http://localhost:8000/api/groups/1/members/2
Authorization: Bearer {{user1_token}}
```

**الرد المتوقع:**
```json
{
  "status": 200,
  "message": "تم إزالة العضو من المجموعة بنجاح"
}
```

---

### 7.2 المستخدم الثاني ينضم مرة أخرى
```http
POST http://localhost:8000/api/groups/join
Authorization: Bearer {{user2_token}}
Content-Type: application/json

{
  "invite_code": "SEASON-ABC123"
}
```

---

### 7.3 المستخدم الثاني يغادر المجموعة بنفسه
```http
POST http://localhost:8000/api/groups/1/leave
Authorization: Bearer {{user2_token}}
```

**الرد المتوقع:**
```json
{
  "status": 200,
  "message": "تم مغادرة المجموعة بنجاح"
}
```

---

### 7.4 محاولة Owner مغادرة المجموعة (يجب أن تفشل)
```http
POST http://localhost:8000/api/groups/1/leave
Authorization: Bearer {{user1_token}}
```

**الرد المتوقع:**
```json
{
  "status": 500,
  "message": "Owner cannot leave the group. Please delete the group or transfer ownership first."
}
```

---

## 🗑️ السيناريو 8: حذف المجموعة

### 8.1 حذف المجموعة (Owner فقط)
```http
DELETE http://localhost:8000/api/groups/1
Authorization: Bearer {{user1_token}}
```

**الرد المتوقع:**
```json
{
  "status": 200,
  "message": "تم حذف المجموعة بنجاح"
}
```

---

### 8.2 محاولة عرض المجموعة المحذوفة (يجب أن تفشل)
```http
GET http://localhost:8000/api/groups/1
Authorization: Bearer {{user1_token}}
```

**الرد المتوقع:**
```json
{
  "status": 404,
  "message": "No query results for model"
}
```

---

## 🎬 سيناريو كامل متقدم (Full Flow)

### المثال: رحلة عائلية لمجموعة من 3 أشخاص

#### 1. إنشاء مجموعة من الأب
```http
POST /api/groups
Authorization: Bearer {{father_token}}

{
  "name": "رحلة العائلة - متحف دبي",
  "description": "رحلة يوم الجمعة",
  "safety_radius": 150
}
```

#### 2. الأم تنضم
```http
POST /api/groups/join
Authorization: Bearer {{mother_token}}

{
  "invite_code": "SEASON-XYZ789"
}
```

#### 3. الابن ينضم
```http
POST /api/groups/join
Authorization: Bearer {{son_token}}

{
  "invite_code": "SEASON-XYZ789"
}
```

#### 4. الجميع يحدثون مواقعهم كل 30 ثانية
```http
# الأب
POST /api/groups/1/location
Authorization: Bearer {{father_token}}
{"latitude": 25.2048, "longitude": 55.2708}

# الأم
POST /api/groups/1/location
Authorization: Bearer {{mother_token}}
{"latitude": 25.2049, "longitude": 55.2709}

# الابن
POST /api/groups/1/location
Authorization: Bearer {{son_token}}
{"latitude": 25.2050, "longitude": 55.2710}
```

#### 5. الابن يبتعد كثيراً (خارج النطاق)
```http
POST /api/groups/1/location
Authorization: Bearer {{son_token}}

{
  "latitude": 25.2200,
  "longitude": 55.2850
}
```
🔔 **الأب والأم يتلقون إشعار تلقائي**

#### 6. الابن يرسل SOS
```http
POST /api/groups/1/sos
Authorization: Bearer {{son_token}}

{
  "latitude": 25.2200,
  "longitude": 55.2850,
  "message": "أنا تائه! لا أجد المتحف"
}
```
🚨 **الأب والأم يتلقون إشعار طوارئ فوري**

#### 7. الأب يشاهد تفاصيل المجموعة
```http
GET /api/groups/1
Authorization: Bearer {{father_token}}
```
**يرى:**
- الابن خارج النطاق
- رسالة SOS نشطة
- موقع الابن الحالي

#### 8. بعد إيجاد الابن، يتم إغلاق الـ SOS
```http
POST /api/groups/1/sos/1/resolve
Authorization: Bearer {{father_token}}
```

---

## 🔧 نصائح للاختبار

### ✅ Best Practices:

1. **حفظ الـ Tokens:**
   - احفظها كـ Environment Variables في Postman
   - `{{user1_token}}`, `{{user2_token}}`

2. **حفظ المعرفات:**
   - `{{group_id}}`
   - `{{invite_code}}`
   - `{{alert_id}}`

3. **تجربة الإحداثيات:**
   - استخدم Google Maps لإحداثيات حقيقية
   - احسب المسافة: https://www.movable-type.co.uk/scripts/latlong.html

4. **تجربة الإشعارات:**
   - تأكد من إعداد Firebase
   - احفظ FCM Tokens في جدول `users`

5. **اختبار Edge Cases:**
   - محاولة انضمام لمجموعة مرتين
   - محاولة Owner مغادرة المجموعة
   - محاولة عضو عادي تحديث المجموعة
   - محاولة الوصول لمجموعة محذوفة

---

## 📊 ملخص الـ Endpoints

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/api/groups` | ✅ | كل مجموعاتي |
| POST | `/api/groups` | ✅ | إنشاء مجموعة |
| GET | `/api/groups/{id}` | ✅ | تفاصيل مجموعة |
| PUT | `/api/groups/{id}` | ✅ | تحديث مجموعة (Owner) |
| DELETE | `/api/groups/{id}` | ✅ | حذف مجموعة (Owner) |
| POST | `/api/groups/join` | ✅ | انضمام لمجموعة |
| POST | `/api/groups/{id}/leave` | ✅ | مغادرة مجموعة |
| GET | `/api/groups/{id}/members` | ✅ | أعضاء المجموعة |
| DELETE | `/api/groups/{groupId}/members/{userId}` | ✅ | إزالة عضو (Owner) |
| POST | `/api/groups/{id}/location` | ✅ | تحديث الموقع |
| POST | `/api/groups/{id}/sos` | ✅ | إرسال SOS |
| POST | `/api/groups/{groupId}/sos/{alertId}/resolve` | ✅ | إغلاق SOS |
| GET | `/api/groups/invite/{code}` | ❌ | معلومات الدعوة (Public) |

---

## 🎯 أهم النقاط

✅ **التحقق من Auth:**
- كل الـ endpoints تحتاج `Bearer Token` ما عدا `/invite/{code}`

✅ **الصلاحيات:**
- **Owner فقط:** Update, Delete, Remove Members
- **أي عضو:** View, Update Location, Send SOS, Resolve SOS

✅ **الإشعارات التلقائية:**
- عند الخروج من النطاق → إشعار لكل الأعضاء
- عند إرسال SOS → إشعار طوارئ لكل الأعضاء

✅ **حساب المسافات:**
- يستخدم Haversine Formula
- المسافة بالأمتار

---

## 🚀 بدء سريع (Quick Start)

```bash
# 1. سجل مستخدمين
POST /api/auth/register (User 1)
POST /api/auth/register (User 2)

# 2. أنشئ مجموعة
POST /api/groups (User 1)

# 3. انضم للمجموعة
POST /api/groups/join (User 2 - use invite_code from step 2)

# 4. حدث المواقع
POST /api/groups/1/location (User 1)
POST /api/groups/1/location (User 2 - use coordinates far away)

# 5. أرسل SOS
POST /api/groups/1/sos (User 2)

# 6. اعرض المجموعة
GET /api/groups/1 (User 1)
```

---

## 🎉 تمت!

**الآن جاهز لتجربة كامل النظام على Postman!**

للمزيد من التفاصيل، راجع:
- `GROUPS_API_DOCUMENTATION.md` - وثائق شاملة
- `GROUPS_QUICK_REFERENCE.md` - مرجع سريع
- `routes/api.php` - كل الـ Routes

---

**Good luck! 🚀**

