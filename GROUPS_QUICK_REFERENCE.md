# ⚡ مرجع سريع - نظام المجموعات

## 🚀 البدء السريع

### 1. تشغيل Migrations
```bash
php artisan migrate
```

### 2. إنشاء مجموعة
```bash
POST /api/groups
{
  "name": "رحلة دبي - العائلة",
  "safety_radius": 100
}
```

### 3. الانضمام
```bash
POST /api/groups/join
{
  "invite_code": "SEASON-ZAKE01"
}
```

### 4. تحديث الموقع
```bash
POST /api/groups/1/location
{
  "latitude": 25.2048,
  "longitude": 55.2708
}
```

### 5. إرسال SOS
```bash
POST /api/groups/1/sos
{
  "latitude": 25.2048,
  "longitude": 55.2708,
  "message": "أحتاج المساعدة!"
}
```

---

## 📋 كل الـ Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/groups` | جميع المجموعات |
| POST | `/api/groups` | إنشاء مجموعة |
| GET | `/api/groups/{id}` | تفاصيل المجموعة |
| PUT | `/api/groups/{id}` | تحديث المجموعة |
| DELETE | `/api/groups/{id}` | حذف المجموعة |
| POST | `/api/groups/join` | الانضمام |
| POST | `/api/groups/{id}/leave` | المغادرة |
| GET | `/api/groups/{id}/members` | الأعضاء |
| DELETE | `/api/groups/{groupId}/members/{userId}` | إزالة عضو |
| POST | `/api/groups/{id}/location` | تحديث الموقع |
| POST | `/api/groups/{id}/sos` | إرسال SOS |
| POST | `/api/groups/{groupId}/sos/{alertId}/resolve` | إغلاق SOS |
| GET | `/api/groups/invite/{code}` | معلومات الدعوة |

---

## 📁 الملفات المنشأة

### Database (4 ملفات)
- `2025_10_27_191340_create_groups_table.php`
- `2025_10_27_191341_create_group_members_table.php`
- `2025_10_27_191342_create_group_locations_table.php`
- `2025_10_27_191357_create_group_sos_alerts_table.php`

### Models (4 ملفات)
- `app/Models/Group.php`
- `app/Models/GroupMember.php`
- `app/Models/GroupLocation.php`
- `app/Models/GroupSosAlert.php`

### Service (1 ملف)
- `app/Services/GroupService.php`

### Resources (5 ملفات)
- `app/Http/Resources/GroupResource.php`
- `app/Http/Resources/GroupMemberResource.php`
- `app/Http/Resources/GroupLocationResource.php`
- `app/Http/Resources/GroupSosAlertResource.php`
- `app/Http/Resources/UserResource.php`

### Requests (5 ملفات)
- `app/Http/Requests/CreateGroupRequest.php`
- `app/Http/Requests/UpdateGroupRequest.php`
- `app/Http/Requests/JoinGroupRequest.php`
- `app/Http/Requests/UpdateLocationRequest.php`
- `app/Http/Requests/SendSosAlertRequest.php`

### Controller (1 ملف)
- `app/Http/Controllers/Api/Group/GroupController.php`

### Routes
- `routes/api.php` (updated)

### Documentation (3 ملفات)
- `GROUPS_API_DOCUMENTATION.md`
- `GROUPS_SETUP_GUIDE.md`
- `GROUPS_QUICK_REFERENCE.md` (هذا الملف)

---

## 🎯 Features الرئيسية

✅ **إدارة المجموعات** - إنشاء، تحديث، حذف  
✅ **الانضمام** - عبر QR Code أو Invite Code  
✅ **تتبع المواقع** - Real-time location tracking  
✅ **إشعارات تلقائية** - عند الخروج من النطاق  
✅ **إشارات SOS** - للطوارئ مع إشعار فوري  
✅ **حساب المسافات** - Haversine Formula  
✅ **QR Code** - يتم إنشاؤه تلقائياً  
✅ **Firebase Integration** - إشعارات push  

---

## 🔔 أنواع الإشعارات

### 1. Out of Range
```json
{
  "title": "تنبيه: عضو خارج النطاق",
  "body": "أحمد تجاوز المسافة المحددة (100متر)",
  "data": {
    "type": "out_of_range",
    "group_id": "1",
    "user_id": "2"
  }
}
```

### 2. SOS Alert
```json
{
  "title": "🚨 إشارة SOS - طوارئ",
  "body": "أحمد يحتاج المساعدة!",
  "data": {
    "type": "sos_alert",
    "group_id": "1",
    "alert_id": "1",
    "latitude": "25.2048",
    "longitude": "55.2708"
  }
}
```

---

## 📱 Mobile Integration

### Location Update (Every 30s)
```dart
Timer.periodic(Duration(seconds: 30), (timer) async {
  LocationData location = await Location().getLocation();
  await api.updateGroupLocation(groupId, location);
});
```

### Handle Push Notifications
```dart
FirebaseMessaging.onMessage.listen((message) {
  if (message.data['type'] == 'out_of_range') {
    showAlert('تنبيه', message.notification.body);
  } else if (message.data['type'] == 'sos_alert') {
    showEmergencyAlert(message.data);
  }
});
```

---

## 🧪 اختبار سريع

```bash
# 1. Create group
curl -X POST http://localhost:8000/api/groups \
  -H "Authorization: Bearer TOKEN" \
  -d '{"name":"Test Group","safety_radius":100}'

# 2. Join group
curl -X POST http://localhost:8000/api/groups/join \
  -H "Authorization: Bearer TOKEN2" \
  -d '{"invite_code":"SEASON-ABC123"}'

# 3. Update location
curl -X POST http://localhost:8000/api/groups/1/location \
  -H "Authorization: Bearer TOKEN" \
  -d '{"latitude":25.2048,"longitude":55.2708}'

# 4. Send SOS
curl -X POST http://localhost:8000/api/groups/1/sos \
  -H "Authorization: Bearer TOKEN" \
  -d '{"latitude":25.2048,"longitude":55.2708,"message":"Help!"}'
```

---

## ⚙️ Configuration

### Safety Radius
- Min: 50 meters
- Max: 5000 meters
- Default: 100 meters

### Location Update Frequency
- Active: Every 30-60 seconds
- Background: Every 5 minutes

---

## 🎯 المطلوب للتشغيل

1. ✅ Run migrations: `php artisan migrate`
2. ✅ Configure Firebase (already done)
3. ✅ Test with Postman
4. ✅ Integrate with mobile app

---

**🎉 جاهز للاستخدام!**

للمزيد من التفاصيل: `GROUPS_API_DOCUMENTATION.md`

