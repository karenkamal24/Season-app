# 🔔 ميزة الإشعارات المتكررة للأعضاء خارج النطاق

## 📝 نظرة عامة

تم تحديث نظام الإشعارات ليرسل تنبيهات متكررة كل **دقيقتين** للأعضاء الذين يكونون خارج نطاق الأمان، مع إيقاف الإشعارات تلقائياً عند عودتهم داخل النطاق.

---

## ✨ كيف يعمل النظام

### 🔴 عضو خارج النطاق
```
1. العضو يخرج من نطاق الأمان (> safety_radius من المالك)
2. إرسال إشعار فوري لجميع الأعضاء
3. بعد دقيقتين → إشعار آخر
4. بعد دقيقتين → إشعار آخر
5. ... وهكذا كل دقيقتين طالما العضو خارج النطاق
```

### 🟢 عضو يعود داخل النطاق
```
1. العضو يعود داخل النطاق (<= safety_radius من المالك)
2. إيقاف الإشعارات فوراً ❌
3. لا مزيد من الإشعارات حتى يخرج مرة أخرى
```

---

## 🔧 التغييرات التقنية

### 1. قاعدة البيانات

**Migration:** `2025_10_28_185024_add_last_notification_sent_at_to_group_members_table.php`

تم إضافة حقل جديد في جدول `group_members`:

```sql
last_notification_sent_at TIMESTAMP NULL
```

**الغرض:**
- تتبع آخر وقت تم فيه إرسال إشعار للعضو
- حساب ما إذا مر دقيقتان أو أكثر منذ آخر إشعار

---

### 2. Model: GroupMember

تم تحديث Model لإضافة الحقل الجديد:

```php
protected $fillable = [
    // ... existing fields
    'last_notification_sent_at',
];

protected $casts = [
    // ... existing casts
    'last_notification_sent_at' => 'datetime',
];
```

---

### 3. Service: GroupService

#### Method: `updateLocation()`

**المنطق الجديد:**

```php
// إذا كان العضو خارج النطاق
if (!$isWithinRadius && $group->notifications_enabled) {
    $shouldSendNotification = false;
    
    // التحقق: هل هذه أول مرة أم مر دقيقتان؟
    if ($member->last_notification_sent_at === null) {
        // أول مرة خارج النطاق
        $shouldSendNotification = true;
    } else {
        // حساب الدقائق منذ آخر إشعار
        $minutesSinceLastNotification = now()->diffInMinutes($member->last_notification_sent_at);
        if ($minutesSinceLastNotification >= 2) {
            $shouldSendNotification = true;
        }
    }
    
    if ($shouldSendNotification) {
        $this->sendOutOfRangeNotification($group, $member->user, $distance);
        $updateData['last_notification_sent_at'] = now();
    }
}
// إذا عاد العضو داخل النطاق
else if ($isWithinRadius && $member->last_notification_sent_at !== null) {
    // إيقاف الإشعارات
    $updateData['last_notification_sent_at'] = null;
}
```

---

#### Method: `sendOutOfRangeNotification()`

تم تحديث الإشعار ليتضمن المسافة الحالية:

**الإشعار الجديد:**
```json
{
  "title": "تنبيه: عضو خارج النطاق",
  "body": "محمد خارج النطاق - المسافة: 150متر (النطاق الآمن: 100متر)",
  "data": {
    "type": "out_of_range",
    "group_id": "1",
    "user_id": "2",
    "user_name": "محمد",
    "distance": "150",
    "safety_radius": "100"
  }
}
```

---

## 📊 سيناريو كامل

### مثال: طفل يضيع في مركز تجاري

#### الوقت 14:00
```
✅ الأب (المالك) موقعه: (25.2048, 55.2708)
✅ الطفل موقعه: (25.2048, 55.2708)
📍 المسافة: 0 متر
🟢 داخل النطاق
```

#### الوقت 14:05
```
✅ الأب موقعه: (25.2048, 55.2708) - لم يتحرك
❌ الطفل موقعه: (25.2150, 55.2800)
📍 المسافة: 150 متر
🔴 خارج النطاق!
📱 إشعار فوري: "الطفل خارج النطاق - المسافة: 150متر"
⏰ last_notification_sent_at = 14:05
```

#### الوقت 14:06
```
❌ الطفل موقعه: (25.2160, 55.2810)
📍 المسافة: 180 متر
🔴 لا زال خارج النطاق
⏸️ لم يمر دقيقتان - لا إشعار
```

#### الوقت 14:07
```
❌ الطفل موقعه: (25.2170, 55.2820)
📍 المسافة: 200 متر
🔴 لا زال خارج النطاق
📱 إشعار جديد: "الطفل خارج النطاق - المسافة: 200متر"
⏰ last_notification_sent_at = 14:07
```

#### الوقت 14:09
```
❌ الطفل موقعه: (25.2180, 55.2830)
📍 المسافة: 220 متر
🔴 لا زال خارج النطاق
📱 إشعار جديد: "الطفل خارج النطاق - المسافة: 220متر"
⏰ last_notification_sent_at = 14:09
```

#### الوقت 14:10
```
✅ الطفل عاد موقعه: (25.2050, 55.2710)
📍 المسافة: 50 متر
🟢 عاد داخل النطاق!
✅ إيقاف الإشعارات
⏰ last_notification_sent_at = null
```

---

## 🎯 الفوائد

| الميزة | الفائدة |
|--------|---------|
| 🔄 **إشعارات متكررة** | تذكير مستمر بأن العضو لا زال خارج النطاق |
| ⏱️ **كل دقيقتين** | توازن بين التنبيه المستمر وعدم الإزعاج الزائد |
| 📍 **عرض المسافة** | معرفة مدى بُعد العضو عن المالك |
| 🔕 **إيقاف تلقائي** | توقف الإشعارات فوراً عند العودة |
| 🔋 **توفير الموارد** | عدم إرسال إشعارات غير ضرورية |

---

## 📱 تجربة المستخدم

### في التطبيق Mobile

#### الإشعار الأول (خروج من النطاق)
```
🔴 تنبيه: عضو خارج النطاق
محمد خارج النطاق - المسافة: 150متر (النطاق الآمن: 100متر)
[اضغط لعرض الموقع على الخريطة]
```

#### الإشعار بعد دقيقتين (لا زال خارج النطاق)
```
🔴 تنبيه: عضو خارج النطاق
محمد خارج النطاق - المسافة: 180متر (النطاق الآمن: 100متر)
[اضغط لعرض الموقع على الخريطة]
```

#### عند العودة داخل النطاق
```
🟢 محمد عاد داخل النطاق
المسافة الحالية: 50متر
✅ كل شيء آمن
```

---

## 🔧 إعدادات قابلة للتخصيص

### تغيير فترة الإشعارات (حالياً: دقيقتين)

في `GroupService.php`:

```php
// لتغيير الفترة من دقيقتين إلى 3 دقائق:
if ($minutesSinceLastNotification >= 3) {
    $shouldSendNotification = true;
}

// للإشعار كل دقيقة:
if ($minutesSinceLastNotification >= 1) {
    $shouldSendNotification = true;
}

// للإشعار كل 5 دقائق:
if ($minutesSinceLastNotification >= 5) {
    $shouldSendNotification = true;
}
```

---

## 🧪 اختبار الميزة

### Test Case 1: أول خروج من النطاق

```bash
# 1. Owner updates location (center)
POST /api/groups/1/location
{
  "latitude": 25.2048,
  "longitude": 55.2708
}

# 2. Member goes out of range (first time)
POST /api/groups/1/location
{
  "latitude": 25.2150,
  "longitude": 55.2800
}

# Result:
# ✅ distance_from_center ≈ 150m
# ✅ is_within_radius = false
# ✅ Notification sent immediately
# ✅ last_notification_sent_at = now()
```

---

### Test Case 2: لا زال خارج النطاق (لم يمر دقيقتان)

```bash
# After 1 minute, member updates location
POST /api/groups/1/location
{
  "latitude": 25.2160,
  "longitude": 55.2810
}

# Result:
# ✅ distance_from_center ≈ 180m
# ✅ is_within_radius = false
# ❌ No notification (only 1 minute passed)
# ⏸️ last_notification_sent_at = unchanged
```

---

### Test Case 3: مر دقيقتان - إشعار جديد

```bash
# After 2 minutes total, member updates location
POST /api/groups/1/location
{
  "latitude": 25.2170,
  "longitude": 55.2820
}

# Result:
# ✅ distance_from_center ≈ 200m
# ✅ is_within_radius = false
# ✅ Notification sent (2 minutes passed)
# ✅ last_notification_sent_at = now()
```

---

### Test Case 4: عودة داخل النطاق - إيقاف الإشعارات

```bash
# Member comes back in range
POST /api/groups/1/location
{
  "latitude": 25.2050,
  "longitude": 55.2710
}

# Result:
# ✅ distance_from_center ≈ 50m
# ✅ is_within_radius = true
# ✅ last_notification_sent_at = null (reset)
# 🔕 Notifications stopped
```

---

## 📊 Database Schema

### الحقل الجديد في `group_members`

```sql
CREATE TABLE group_members (
    id BIGINT UNSIGNED PRIMARY KEY,
    group_id BIGINT UNSIGNED,
    user_id BIGINT UNSIGNED,
    role VARCHAR(255),
    status VARCHAR(255),
    is_within_radius BOOLEAN,
    out_of_range_count INT,
    joined_at TIMESTAMP,
    last_location_update TIMESTAMP,
    last_notification_sent_at TIMESTAMP NULL,  -- ⭐ جديد
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

---

## 🎯 الخلاصة

### ما تم إنجازه:

✅ **إضافة حقل `last_notification_sent_at`** في جدول `group_members`
✅ **إرسال إشعارات متكررة كل دقيقتين** للأعضاء خارج النطاق
✅ **إيقاف الإشعارات تلقائياً** عند عودة العضو داخل النطاق
✅ **عرض المسافة الحالية** في الإشعار
✅ **تحسين تجربة المستخدم** مع إشعارات أكثر معلوماتية

---

## 🚀 الملفات المعدلة

```
✅ database/migrations/2025_10_28_185024_add_last_notification_sent_at_to_group_members_table.php
✅ app/Models/GroupMember.php
✅ app/Services/GroupService.php
✅ REPEATED_NOTIFICATIONS_FEATURE.md (هذا الملف)
```

---

## 💡 نصائح للاستخدام الأمثل

### 1. في التطبيق Mobile
- تأكد من تحديث الموقع بشكل منتظم (كل 30-60 ثانية)
- اعرض الإشعارات بشكل واضح مع صوت تنبيه
- أضف زر سريع للانتقال إلى الخريطة

### 2. للأداء
- الإشعارات ترسل فقط إذا `notifications_enabled = true`
- لا إشعارات إذا كان العضو داخل النطاق
- الحساب يتم عند كل `updateLocation()` فقط

### 3. للتخصيص
- يمكن تغيير الفترة (حالياً 2 دقيقة) حسب الحاجة
- يمكن تخصيص رسالة الإشعار
- يمكن إضافة صوت أو اهتزاز مختلف للإشعارات المتكررة

---

**✨ الميزة جاهزة ومفعّلة! ✨**

تاريخ التحديث: 28 أكتوبر 2025

