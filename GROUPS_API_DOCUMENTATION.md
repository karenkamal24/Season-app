# 📱 Groups System API Documentation
## خاصية عدم الضياع - Season App

---

## 🎯 Overview

نظام كامل لإدارة المجموعات وتتبع المواقع مع:
- ✅ إنشاء وإدارة المجموعات
- ✅ الانضمام عبر QR Code أو Invite Code
- ✅ تتبع المواقع في الوقت الفعلي
- ✅ إشعارات تلقائية عند الخروج من النطاق
- ✅ إشارات SOS للطوارئ

---

## 📊 قاعدة البيانات

### Tables Created:

1. **groups** - المجموعات
2. **group_members** - الأعضاء
3. **group_locations** - تتبع المواقع
4. **group_sos_alerts** - إشارات الطوارئ

---

## 🚀 API Endpoints

### Base URL
```
http://your-domain.com/api
```

### Authentication
كل الـ endpoints تحتاج `Authorization: Bearer TOKEN`

---

## 📋 1. إدارة المجموعات

### 1.1 Get All User's Groups
```
GET /api/groups
```

**Response:**
```json
{
  "status": 200,
  "message": "تم جلب المجموعات بنجاح",
  "data": [
    {
      "id": 1,
      "name": "رحلة دبي - العائلة",
      "description": "مجموعة العائلة رحلة دبي 2025",
      "owner_id": 1,
      "invite_code": "SEASON-ZAKE01",
      "qr_code": "base64_encoded_qr_code",
      "safety_radius": 100,
      "notifications_enabled": true,
      "is_active": true,
      "members_count": 3,
      "out_of_range_count": 1,
      "created_at": "2025-10-27T10:00:00Z"
    }
  ]
}
```

---

### 1.2 Create New Group
```
POST /api/groups
```

**Request Body:**
```json
{
  "name": "رحلة دبي - العائلة",
  "description": "مجموعة العائلة رحلة دبي 2025",
  "safety_radius": 100,
  "notifications_enabled": true
}
```

**Validation Rules:**
- `name`: required, string, max:255
- `description`: nullable, string, max:1000
- `safety_radius`: nullable, integer, min:50, max:5000 (default: 100)
- `notifications_enabled`: nullable, boolean (default: true)

**Response:**
```json
{
  "status": 201,
  "message": "تم إنشاء المجموعة بنجاح",
  "data": {
    "id": 1,
    "name": "رحلة دبي - العائلة",
    "invite_code": "SEASON-ZAKE01",
    "qr_code": "base64_encoded_qr_code",
    "owner": {
      "id": 1,
      "name": "مستخدم تجريبي"
    }
  }
}
```

---

### 1.3 Get Group Details
```
GET /api/groups/{id}
```

**Response:**
```json
{
  "status": 200,
  "message": "تم جلب بيانات المجموعة بنجاح",
  "data": {
    "id": 1,
    "name": "رحلة دبي - العائلة",
    "members_count": 3,
    "out_of_range_count": 1,
    "members": [
      {
        "id": 1,
        "name": "مستخدم تجريبي",
        "role": "owner",
        "is_within_radius": true,
        "latest_location": {
          "latitude": 25.2048,
          "longitude": 55.2708,
          "distance_from_center": 0
        }
      },
      {
        "id": 2,
        "name": "أحمد",
        "role": "member",
        "is_within_radius": false,
        "out_of_range_count": 1,
        "latest_location": {
          "latitude": 25.2150,
          "longitude": 55.2800,
          "distance_from_center": 150.5
        }
      }
    ],
    "active_sos_alerts": []
  }
}
```

---

### 1.4 Update Group
```
PUT /api/groups/{id}
```

**Request Body:**
```json
{
  "name": "رحلة دبي - العائلة المحدثة",
  "safety_radius": 200
}
```

**Note:** Only the group owner can update

---

### 1.5 Delete Group
```
DELETE /api/groups/{id}
```

**Note:** Only the group owner can delete

---

## 👥 2. الانضمام والأعضاء

### 2.1 Join Group (by Invite Code)
```
POST /api/groups/join
```

**Request Body:**
```json
{
  "invite_code": "SEASON-ZAKE01"
}
```

**Response:**
```json
{
  "status": 200,
  "message": "تم الانضمام للمجموعة بنجاح",
  "data": {
    "id": 1,
    "name": "رحلة دبي - العائلة",
    "members_count": 4
  }
}
```

---

### 2.2 Get Invite Details (for QR Scan)
```
GET /api/groups/invite/{inviteCode}
```

**No authentication required** - Public endpoint

**Response:**
```json
{
  "status": 200,
  "message": "تم جلب معلومات الدعوة بنجاح",
  "data": {
    "id": 1,
    "name": "رحلة دبي - العائلة",
    "description": "مجموعة العائلة رحلة دبي 2025",
    "owner": "مستخدم تجريبي",
    "members_count": 3,
    "invite_code": "SEASON-ZAKE01"
  }
}
```

---

### 2.3 Leave Group
```
POST /api/groups/{id}/leave
```

**Response:**
```json
{
  "status": 200,
  "message": "تم مغادرة المجموعة بنجاح"
}
```

**Note:** Owner cannot leave. Must delete group or transfer ownership first.

---

### 2.4 Remove Member (Owner Only)
```
DELETE /api/groups/{groupId}/members/{userId}
```

**Response:**
```json
{
  "status": 200,
  "message": "تم إزالة العضو من المجموعة بنجاح"
}
```

---

### 2.5 Get Group Members
```
GET /api/groups/{id}/members
```

**Response:**
```json
{
  "status": 200,
  "message": "تم جلب أعضاء المجموعة بنجاح",
  "data": [
    {
      "id": 1,
      "user": {
        "id": 1,
        "name": "مستخدم تجريبي",
        "avatar": "avatar_url"
      },
      "role": "owner",
      "status": "active",
      "is_within_radius": true,
      "out_of_range_count": 0,
      "latest_location": {
        "latitude": 25.2048,
        "longitude": 55.2708,
        "distance_from_center": 0,
        "is_within_radius": true
      }
    }
  ]
}
```

---

## 📍 3. تتبع المواقع

### 3.1 Update Location
```
POST /api/groups/{id}/location
```

**Request Body:**
```json
{
  "latitude": 25.2048,
  "longitude": 55.2708
}
```

**Validation:**
- `latitude`: required, numeric, between:-90,90
- `longitude`: required, numeric, between:-180,180

**Response:**
```json
{
  "status": 200,
  "message": "تم تحديث الموقع بنجاح",
  "data": {
    "id": 123,
    "latitude": 25.2048,
    "longitude": 55.2708,
    "distance_from_center": 50.5,
    "is_within_radius": true,
    "created_at": "2025-10-27T10:30:00Z"
  }
}
```

**Auto Notifications:**
- إذا خرج العضو من النطاق، يتم إرسال إشعار تلقائي لجميع الأعضاء
- يتم حساب المسافة من مركز المجموعة

---

## 🚨 4. إشارات الطوارئ (SOS)

### 4.1 Send SOS Alert
```
POST /api/groups/{id}/sos
```

**Request Body:**
```json
{
  "latitude": 25.2048,
  "longitude": 55.2708,
  "message": "أحتاج المساعدة!"
}
```

**Response:**
```json
{
  "status": 200,
  "message": "تم إرسال إشارة SOS بنجاح",
  "data": {
    "id": 1,
    "group_id": 1,
    "user": {
      "id": 1,
      "name": "أحمد"
    },
    "message": "أحتاج المساعدة!",
    "latitude": 25.2048,
    "longitude": 55.2708,
    "status": "active",
    "created_at": "2025-10-27T10:35:00Z"
  }
}
```

**Auto Notifications:**
- يتم إرسال إشعار push لجميع أعضاء المجموعة فوراً
- الإشعار يحتوي على الموقع والرسالة

---

### 4.2 Resolve SOS Alert
```
POST /api/groups/{groupId}/sos/{alertId}/resolve
```

**Response:**
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

## 🔔 Push Notifications

### Types of Notifications:

#### 1. Out of Range Notification
```json
{
  "title": "تنبيه: عضو خارج النطاق",
  "body": "أحمد تجاوز المسافة المحددة (100متر)",
  "data": {
    "type": "out_of_range",
    "group_id": "1",
    "user_id": "2",
    "user_name": "أحمد"
  }
}
```

#### 2. SOS Alert Notification
```json
{
  "title": "🚨 إشارة SOS - طوارئ",
  "body": "أحمد يحتاج المساعدة! أحتاج المساعدة!",
  "data": {
    "type": "sos_alert",
    "group_id": "1",
    "alert_id": "1",
    "user_id": "2",
    "latitude": "25.2048",
    "longitude": "55.2708"
  }
}
```

---

## 🧪 Testing Examples

### Example 1: Create Group and Invite Members

```bash
# 1. Create group
curl -X POST http://localhost:8000/api/groups \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "رحلة دبي - العائلة",
    "description": "مجموعة العائلة رحلة دبي 2025",
    "safety_radius": 100
  }'

# Response will include invite_code

# 2. Other users join using invite code
curl -X POST http://localhost:8000/api/groups/join \
  -H "Authorization: Bearer USER2_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "invite_code": "SEASON-ZAKE01"
  }'
```

---

### Example 2: Track Location

```bash
# Update location periodically (every 30 seconds)
curl -X POST http://localhost:8000/api/groups/1/location \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "latitude": 25.2048,
    "longitude": 55.2708
  }'

# If user goes out of range, all members get notification automatically
```

---

### Example 3: Send SOS

```bash
curl -X POST http://localhost:8000/api/groups/1/sos \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "latitude": 25.2048,
    "longitude": 55.2708,
    "message": "أحتاج المساعدة فوراً!"
  }'

# All group members receive push notification immediately
```

---

## 📱 Mobile App Integration

### Recommended Flow:

1. **On App Launch:**
   - Get user's location
   - Update location for all active groups

2. **Background Location Updates:**
   - Update location every 30-60 seconds
   - Use background service / WorkManager

3. **Handle Notifications:**
   - Listen to FCM notifications
   - Show alert for out_of_range
   - Show emergency alert for SOS
   - Open map when notification clicked

4. **QR Code Scanning:**
   - Scan QR → Extract invite_code
   - Call `/api/groups/invite/{code}` to show preview
   - User confirms → Call `/api/groups/join`

---

## 🔧 Database Schema

### groups
```sql
id, name, description, owner_id, invite_code (unique), qr_code,
safety_radius (default: 100), notifications_enabled (default: true),
is_active (default: true), created_at, updated_at
```

### group_members
```sql
id, group_id, user_id, role (owner/member), status (active/left/removed),
is_within_radius (boolean), out_of_range_count (integer),
joined_at, last_location_update, created_at, updated_at
```

### group_locations
```sql
id, group_id, user_id, latitude, longitude,
distance_from_center (meters), is_within_radius (boolean),
created_at, updated_at
```

### group_sos_alerts
```sql
id, group_id, user_id, message, latitude, longitude,
status (active/resolved/cancelled), resolved_at,
created_at, updated_at
```

---

## ⚙️ Configuration

### Safety Radius Options:
- Minimum: 50 meters
- Maximum: 5000 meters
- Default: 100 meters

### Location Update Frequency:
- Recommended: Every 30-60 seconds when app is active
- Background: Every 5 minutes (to save battery)

---

## 🎯 Features Summary

✅ **إنشاء المجموعات** - مع QR Code تلقائي  
✅ **الانضمام بسهولة** - عبر QR أو كود  
✅ **تتبع المواقع** - Real-time location tracking  
✅ **إشعارات ذكية** - تلقائية عند الخروج من النطاق  
✅ **إشارات SOS** - للطوارئ مع إشعار فوري  
✅ **إدارة الأعضاء** - إضافة وإزالة  
✅ **حساب المسافات** - Haversine formula  
✅ **أمان كامل** - Authentication required  

---

## 🚀 Getting Started

1. **Run Migrations:**
```bash
php artisan migrate
```

2. **Test with Postman:**
- Import the API collection
- Set Bearer token
- Test endpoints

3. **Integrate with Mobile:**
- Implement location services
- Setup FCM for notifications
- Add QR scanner

---

**تم! 🎉 النظام جاهز للاستخدام**

