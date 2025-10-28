# 🚀 دليل التثبيت والاستخدام - نظام المجموعات

## ✅ ما تم إنشاؤه

تم إنشاء نظام كامل لإدارة المجموعات (خاصية عدم الضياع) يشمل:

---

## 📊 1. قاعدة البيانات (Migrations)

```
✅ database/migrations/2025_10_27_191340_create_groups_table.php
✅ database/migrations/2025_10_27_191341_create_group_members_table.php
✅ database/migrations/2025_10_27_191342_create_group_locations_table.php
✅ database/migrations/2025_10_27_191357_create_group_sos_alerts_table.php
```

---

## 🎯 2. Models

```
✅ app/Models/Group.php
✅ app/Models/GroupMember.php
✅ app/Models/GroupLocation.php
✅ app/Models/GroupSosAlert.php
```

**Features:**
- علاقات كاملة بين الـ Models
- Accessors & Mutators
- Scopes للاستعلامات المتكررة

---

## 🛠️ 3. Services

```
✅ app/Services/GroupService.php
```

**Contains:**
- إنشاء المجموعات مع QR Code تلقائي
- الانضمام والمغادرة
- تحديث المواقع مع حساب المسافات (Haversine Formula)
- إرسال إشارات SOS
- إرسال إشعارات تلقائية عند الخروج من النطاق

---

## 📤 4. Resources (API Responses)

```
✅ app/Http/Resources/GroupResource.php
✅ app/Http/Resources/GroupMemberResource.php
✅ app/Http/Resources/GroupLocationResource.php
✅ app/Http/Resources/GroupSosAlertResource.php
✅ app/Http/Resources/UserResource.php
```

---

## ✅ 5. Request Validation

```
✅ app/Http/Requests/CreateGroupRequest.php
✅ app/Http/Requests/UpdateGroupRequest.php
✅ app/Http/Requests/JoinGroupRequest.php
✅ app/Http/Requests/UpdateLocationRequest.php
✅ app/Http/Requests/SendSosAlertRequest.php
```

---

## 🎮 6. Controller

```
✅ app/Http/Controllers/Api/Group/GroupController.php
```

**Contains 13 Methods:**
1. `index()` - Get all user's groups
2. `store()` - Create new group
3. `show()` - Get group details
4. `update()` - Update group
5. `destroy()` - Delete group
6. `join()` - Join group by invite code
7. `leave()` - Leave group
8. `removeMember()` - Remove member (owner only)
9. `updateLocation()` - Update member location
10. `members()` - Get group members
11. `sendSos()` - Send SOS alert
12. `resolveSos()` - Resolve SOS alert
13. `inviteDetails()` - Get invite info (public)

---

## 🛣️ 7. API Routes

```
✅ routes/api.php
```

**13 Endpoints:**
```
GET    /api/groups
POST   /api/groups
GET    /api/groups/{id}
PUT    /api/groups/{id}
DELETE /api/groups/{id}
POST   /api/groups/join
POST   /api/groups/{id}/leave
GET    /api/groups/{id}/members
DELETE /api/groups/{groupId}/members/{userId}
POST   /api/groups/{id}/location
POST   /api/groups/{id}/sos
POST   /api/groups/{groupId}/sos/{alertId}/resolve
GET    /api/groups/invite/{inviteCode} (public)
```

---

## 📚 8. Documentation

```
✅ GROUPS_API_DOCUMENTATION.md - دليل APIs كامل
✅ GROUPS_SETUP_GUIDE.md - دليل التثبيت (هذا الملف)
```

---

## 🚀 خطوات التشغيل

### الخطوة 1: تشغيل Migrations

```bash
php artisan migrate
```

هذا سينشئ 4 جداول جديدة:
- `groups`
- `group_members`
- `group_locations`
- `group_sos_alerts`

---

### الخطوة 2: اختبار API

#### 1. إنشاء مجموعة

```bash
POST http://localhost:8000/api/groups
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "name": "رحلة دبي - العائلة",
  "description": "مجموعة العائلة رحلة دبي 2025",
  "safety_radius": 100
}
```

#### 2. الانضمام للمجموعة

```bash
POST http://localhost:8000/api/groups/join
Authorization: Bearer USER2_TOKEN
Content-Type: application/json

{
  "invite_code": "SEASON-ZAKE01"
}
```

#### 3. تحديث الموقع

```bash
POST http://localhost:8000/api/groups/1/location
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "latitude": 25.2048,
  "longitude": 55.2708
}
```

#### 4. إرسال إشارة SOS

```bash
POST http://localhost:8000/api/groups/1/sos
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "latitude": 25.2048,
  "longitude": 55.2708,
  "message": "أحتاج المساعدة!"
}
```

---

## 🔔 إعدادات Firebase (الإشعارات)

النظام مدمج بالكامل مع Firebase Cloud Messaging!

### إشعارات تلقائية:

1. **عند الخروج من النطاق:**
   - يتم إرسال إشعار لجميع الأعضاء
   - العنوان: "تنبيه: عضو خارج النطاق"
   - الرسالة: "أحمد تجاوز المسافة المحددة (100متر)"

2. **عند إرسال SOS:**
   - يتم إرسال إشعار فوري لجميع الأعضاء
   - العنوان: "🚨 إشارة SOS - طوارئ"
   - الرسالة: "أحمد يحتاج المساعدة!"

---

## 📱 ربط التطبيق Mobile

### 1. Location Tracking

```dart
// Flutter Example
import 'package:location/location.dart';

// Get location every 30 seconds
Timer.periodic(Duration(seconds: 30), (timer) async {
  LocationData location = await Location().getLocation();
  
  // Update location for all active groups
  await apiService.updateGroupLocation(
    groupId: groupId,
    latitude: location.latitude,
    longitude: location.longitude,
  );
});
```

### 2. Handle Push Notifications

```dart
// Handle notification when app is in foreground
FirebaseMessaging.onMessage.listen((RemoteMessage message) {
  if (message.data['type'] == 'out_of_range') {
    showAlert('تنبيه', message.notification.body);
  } else if (message.data['type'] == 'sos_alert') {
    showEmergencyAlert(message.data);
  }
});
```

### 3. QR Code Scanner

```dart
// Scan QR Code
String inviteCode = await scanner.scan();

// Get group info
var groupInfo = await apiService.getInviteDetails(inviteCode);

// Show preview dialog
showDialog(
  title: groupInfo['name'],
  content: 'Owner: ${groupInfo['owner']}\nMembers: ${groupInfo['members_count']}',
  actions: [
    TextButton(
      onPressed: () => joinGroup(inviteCode),
      child: Text('انضمام'),
    ),
  ],
);
```

---

## 🧪 اختبار كامل (Step by Step)

### السيناريو: رحلة عائلية

#### الخطوة 1: الأب ينشئ المجموعة
```bash
POST /api/groups
{
  "name": "رحلة دبي - العائلة",
  "safety_radius": 100
}
# Response: invite_code = "SEASON-ZAKE01"
```

#### الخطوة 2: الأم تنضم عبر QR Code
```bash
# 1. Scan QR code → get "SEASON-ZAKE01"
# 2. Get invite details
GET /api/groups/invite/SEASON-ZAKE01

# 3. Join group
POST /api/groups/join
{
  "invite_code": "SEASON-ZAKE01"
}
```

#### الخطوة 3: الأطفال ينضمون أيضاً
```bash
POST /api/groups/join
{
  "invite_code": "SEASON-ZAKE01"
}
```

#### الخطوة 4: الجميع يحدثون مواقعهم
```bash
# كل عضو يحدث موقعه كل 30 ثانية
POST /api/groups/1/location
{
  "latitude": 25.2048,
  "longitude": 55.2708
}
```

#### الخطوة 5: أحمد خرج من النطاق
```bash
# أحمد يحدث موقعه (بعيد 150 متر)
POST /api/groups/1/location
{
  "latitude": 25.2150,
  "longitude": 55.2800
}

# ✅ تلقائياً: يصل إشعار لكل الأعضاء:
# "تنبيه: أحمد تجاوز المسافة المحددة (100متر)"
```

#### الخطوة 6: أحمد يرسل SOS
```bash
POST /api/groups/1/sos
{
  "latitude": 25.2150,
  "longitude": 55.2800,
  "message": "ضعت! أحتاج المساعدة"
}

# ✅ تلقائياً: يصل إشعار فوري لكل الأعضاء:
# "🚨 إشارة SOS - طوارئ"
# "أحمد يحتاج المساعدة! ضعت! أحتاج المساعدة"
```

#### الخطوة 7: الأب يرى التفاصيل
```bash
GET /api/groups/1

# Response يحتوي على:
# - قائمة الأعضاء
# - من في النطاق ومن خارجه
# - إشارات SOS النشطة
# - آخر موقع لكل عضو
```

---

## 🎯 Features المدمجة

### 1. حساب المسافات التلقائي
- يستخدم Haversine Formula
- دقة عالية
- يحسب المسافة من موقع مالك المجموعة (Owner)
- مالك المجموعة هو مركز المجموعة دائماً

### 2. الإشعارات التلقائية
- مدمج مع Firebase
- إشعار عند الخروج من النطاق
- إشعار SOS فوري

### 3. QR Code تلقائي
- يتم إنشاؤه عند إنشاء المجموعة
- يحتوي على Invite Code
- جاهز للمسح

### 4. الأمان
- كل الـ endpoints محمية بـ auth:sanctum
- التحقق من الصلاحيات
- Owner فقط يمكنه التعديل/الحذف

### 5. Validation كاملة
- رسائل خطأ بالعربي
- التحقق من البيانات
- منع الـ Duplicate entries

---

## 📊 Database Relationships

```
User (users)
  ↓
  ├─ owns many Groups (owner_id)
  └─ belongs to many Groups through GroupMember
  
Group (groups)
  ↓
  ├─ has many GroupMembers
  ├─ has many GroupLocations
  ├─ has many GroupSosAlerts
  └─ belongs to User (owner)

GroupMember (group_members)
  ↓
  ├─ belongs to Group
  ├─ belongs to User
  └─ has one latest GroupLocation

GroupLocation (group_locations)
  ↓
  ├─ belongs to Group
  └─ belongs to User

GroupSosAlert (group_sos_alerts)
  ↓
  ├─ belongs to Group
  └─ belongs to User
```

---

## 🔧 Customization Options

### تغيير نطاق الأمان الافتراضي

في `GroupService.php`:
```php
'safety_radius' => $data['safety_radius'] ?? 100, // غير 100 للقيمة المطلوبة
```

### تغيير فترة تحديث الموقع

في التطبيق Mobile:
```dart
// للحفاظ على البطارية
Timer.periodic(Duration(seconds: 60), ...); // كل دقيقة

// للدقة العالية
Timer.periodic(Duration(seconds: 15), ...); // كل 15 ثانية
```

### تخصيص الإشعارات

في `GroupService.php` - method `sendOutOfRangeNotification()`:
```php
$firebaseService->sendToDevice(
    $member->user->fcm_token,
    'عنوان مخصص',  // غير هنا
    'رسالة مخصصة',  // وهنا
    ['type' => 'out_of_range']
);
```

---

## ⚡ Performance Tips

1. **استخدم Queues للإشعارات:**
```php
// في GroupService.php
dispatch(new SendGroupNotification($members, $data));
```

2. **Cache معلومات المجموعات:**
```php
Cache::remember("group_{$id}", 300, fn() => Group::with('members')->find($id));
```

3. **Index على الـ Database:**
```php
// تم إضافتها بالفعل في الـ migrations
$table->index(['group_id', 'user_id']);
$table->index('is_within_radius');
```

---

## 🐛 Troubleshooting

### المشكلة: "Class 'QrCode' not found"

**الحل:**
```bash
composer require simplesoftwareio/simple-qrcode
```

### المشكلة: Notifications لا تصل

**الحل:**
1. تأكد من تفعيل Firebase Cloud Messaging API
2. تحقق من FCM tokens في قاعدة البيانات
3. راجع `storage/logs/laravel.log`

### المشكلة: حساب المسافات غير دقيق

**الحل:**
- تأكد من أن latitude و longitude بالتنسيق الصحيح (decimal)
- استخدم GPS accuracy عالية في التطبيق

---

## 📞 الدعم

للمزيد من المعلومات:
- **API Documentation**: `GROUPS_API_DOCUMENTATION.md`
- **Logs**: `storage/logs/laravel.log`

---

## ✅ Checklist

قبل النشر، تأكد من:

- [ ] تشغيل `php artisan migrate`
- [ ] اختبار كل الـ endpoints
- [ ] تفعيل Firebase Cloud Messaging API
- [ ] إعداد Queue workers للإشعارات
- [ ] اختبار حساب المسافات
- [ ] اختبار QR Code scanning
- [ ] اختبار الإشعارات Push
- [ ] مراجعة الـ Logs

---

**🎉 كل شيء جاهز! النظام جاهز للاستخدام**

بالتوفيق! 🚀

