# 🟢🔴 ميزة Online/Offline Status للمستخدمين

## 📝 نظرة عامة

تم إضافة ميزة عرض حالة المستخدم (متصل/غير متصل) في التطبيق بشكل تلقائي! يظهر للمستخدمين في المجموعات من هو متصل حالياً ومن غير متصل.

---

## ✨ كيف يعمل النظام

### 🟢 Online (متصل)
```
User يُعتبر متصل إذا:
- تم تحديث last_active_at خلال آخر 5 دقائق
- يظهر status = "online"
- يظهر last_seen = "متصل الآن"
```

### 🔴 Offline (غير متصل)
```
User يُعتبر غير متصل إذا:
- مر أكثر من 5 دقائق على last_active_at
- يظهر status = "offline"
- يظهر last_seen = "نشط منذ X دقيقة/ساعة/يوم"
```

---

## 🔧 التغييرات التقنية

### 1. قاعدة البيانات

**Migration:** `2025_10_28_192221_add_last_active_at_to_users_table.php`

تم إضافة حقل جديد في جدول `users`:

```sql
last_active_at TIMESTAMP NULL
```

**الغرض:**
- تخزين آخر وقت كان فيه المستخدم نشطاً
- حساب ما إذا كان متصل أو غير متصل

---

### 2. Middleware: UpdateUserLastActive

**File:** `app/Http/Middleware/UpdateUserLastActive.php`

```php
public function handle(Request $request, Closure $next): Response
{
    // Update last_active_at for authenticated users
    if ($request->user()) {
        $request->user()->update([
            'last_active_at' => now(),
        ]);
    }

    return $next($request);
}
```

**الوظيفة:**
- يعمل تلقائياً مع كل طلب API
- يحدث حقل `last_active_at` لآخر وقت نشاط
- مسجل في `bootstrap/app.php` كـ global middleware

---

### 3. Model: User

**File:** `app/Models/User.php`

#### تم إضافة 3 Accessors:

#### 1. `is_online` (Boolean)
```php
public function getIsOnlineAttribute(): bool
{
    if (!$this->last_active_at) {
        return false;
    }
    
    return $this->last_active_at->diffInMinutes(now()) < 5;
}
```

**يُرجع:** `true` إذا نشط خلال آخر 5 دقائق، `false` otherwise

---

#### 2. `status` (String)
```php
public function getStatusAttribute(): string
{
    return $this->is_online ? 'online' : 'offline';
}
```

**يُرجع:** `"online"` أو `"offline"`

---

#### 3. `last_seen` (String)
```php
public function getLastSeenAttribute(): ?string
{
    if (!$this->last_active_at) {
        return null;
    }

    if ($this->is_online) {
        return 'متصل الآن';
    }

    $minutes = $this->last_active_at->diffInMinutes(now());
    
    if ($minutes < 60) {
        return "نشط منذ {$minutes} دقيقة";
    }

    $hours = $this->last_active_at->diffInHours(now());
    if ($hours < 24) {
        return "نشط منذ {$hours} ساعة";
    }

    $days = $this->last_active_at->diffInDays(now());
    return "نشط منذ {$days} يوم";
}
```

**يُرجع:**
- `"متصل الآن"` - إذا online
- `"نشط منذ 10 دقيقة"` - إذا offline منذ أقل من ساعة
- `"نشط منذ 3 ساعة"` - إذا offline منذ أقل من يوم
- `"نشط منذ 2 يوم"` - إذا offline منذ أكثر من يوم

---

### 4. Resources (API Response)

#### UserResource
```php
return [
    'id' => $this->id,
    'name' => $this->name,
    'email' => $this->email,
    // ... other fields
    'is_online' => $this->is_online,        // true/false
    'status' => $this->status,               // "online"/"offline"
    'last_seen' => $this->last_seen,         // "متصل الآن" or "نشط منذ X"
    'last_active_at' => $this->last_active_at?->toIso8601String(),
];
```

#### GroupMemberResource
```php
return [
    'id' => $this->id,
    'name' => $this->name,
    // ... other fields
    'is_online' => $this->is_online,        // حالة المستخدم
    'user_status' => $this->status,         // "online"/"offline"
    'last_seen' => $this->last_seen,        // "متصل الآن"
    'role' => $this->pivot->role,           // دور في المجموعة
    'status' => $this->pivot->status,       // حالة العضوية
    // ... other fields
];
```

**ملاحظة:** 
- `user_status` = حالة اتصال المستخدم (online/offline)
- `status` = حالة العضوية في المجموعة (active/left/removed)

---

## 📊 API Examples

### Example 1: Get User Profile

```bash
GET /api/user
Authorization: Bearer TOKEN
```

**Response:**
```json
{
  "status": 200,
  "message": "تم جلب البيانات بنجاح",
  "data": {
    "id": 1,
    "name": "أحمد محمد",
    "email": "ahmed@example.com",
    "is_online": true,
    "status": "online",
    "last_seen": "متصل الآن",
    "last_active_at": "2025-10-28T19:25:00+00:00"
  }
}
```

---

### Example 2: Get Group Members

```bash
GET /api/groups/1/members
Authorization: Bearer TOKEN
```

**Response:**
```json
{
  "status": 200,
  "message": "تم جلب أعضاء المجموعة بنجاح",
  "data": [
    {
      "id": 1,
      "name": "الأب",
      "avatar": "avatar1.jpg",
      "is_online": true,
      "user_status": "online",
      "last_seen": "متصل الآن",
      "role": "owner",
      "status": "active",
      "is_within_radius": true
    },
    {
      "id": 2,
      "name": "الابن",
      "avatar": "avatar2.jpg",
      "is_online": false,
      "user_status": "offline",
      "last_seen": "نشط منذ 15 دقيقة",
      "role": "member",
      "status": "active",
      "is_within_radius": false
    }
  ]
}
```

---

### Example 3: Get Group Details

```bash
GET /api/groups/1
Authorization: Bearer TOKEN
```

**Response:**
```json
{
  "status": 200,
  "message": "تم جلب بيانات المجموعة بنجاح",
  "data": {
    "id": 1,
    "name": "رحلة دبي - العائلة",
    "members": [
      {
        "id": 1,
        "name": "الأب",
        "is_online": true,
        "user_status": "online",
        "last_seen": "متصل الآن",
        "role": "owner",
        "latest_location": {
          "latitude": 25.2048,
          "longitude": 55.2708,
          "distance_from_center": 0
        }
      },
      {
        "id": 2,
        "name": "الأم",
        "is_online": true,
        "user_status": "online",
        "last_seen": "متصل الآن",
        "role": "member"
      },
      {
        "id": 3,
        "name": "الطفل",
        "is_online": false,
        "user_status": "offline",
        "last_seen": "نشط منذ 8 دقائق",
        "role": "member",
        "is_within_radius": false
      }
    ]
  }
}
```

---

## 🎨 عرض الحالة في التطبيق

### في Flutter:

```dart
class MemberCard extends StatelessWidget {
  final Member member;

  @override
  Widget build(BuildContext context) {
    return ListTile(
      leading: Stack(
        children: [
          // Avatar
          CircleAvatar(
            backgroundImage: NetworkImage(member.avatar),
          ),
          // Online indicator
          Positioned(
            right: 0,
            bottom: 0,
            child: Container(
              width: 12,
              height: 12,
              decoration: BoxDecoration(
                color: member.isOnline ? Colors.green : Colors.grey,
                shape: BoxShape.circle,
                border: Border.all(color: Colors.white, width: 2),
              ),
            ),
          ),
        ],
      ),
      title: Text(member.name),
      subtitle: Text(
        member.lastSeen,
        style: TextStyle(
          color: member.isOnline ? Colors.green : Colors.grey,
          fontSize: 12,
        ),
      ),
    );
  }
}
```

---

### في React Native:

```jsx
const MemberCard = ({ member }) => {
  return (
    <View style={styles.card}>
      <View style={styles.avatarContainer}>
        <Image source={{ uri: member.avatar }} style={styles.avatar} />
        <View 
          style={[
            styles.statusDot, 
            { backgroundColor: member.isOnline ? '#4CAF50' : '#9E9E9E' }
          ]} 
        />
      </View>
      
      <View style={styles.info}>
        <Text style={styles.name}>{member.name}</Text>
        <Text 
          style={[
            styles.lastSeen, 
            { color: member.isOnline ? '#4CAF50' : '#9E9E9E' }
          ]}
        >
          {member.lastSeen}
        </Text>
      </View>
    </View>
  );
};
```

---

## ⚙️ إعدادات قابلة للتخصيص

### تغيير مدة Online (حالياً: 5 دقائق)

في `app/Models/User.php`:

```php
// لتغيير من 5 دقائق إلى 3 دقائق:
public function getIsOnlineAttribute(): bool
{
    if (!$this->last_active_at) {
        return false;
    }
    
    return $this->last_active_at->diffInMinutes(now()) < 3; // هنا
}

// لتغيير إلى 10 دقائق:
return $this->last_active_at->diffInMinutes(now()) < 10;
```

---

### تعطيل تحديث الحالة التلقائي

في `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->append(\App\Http\Middleware\SetLocaleFromHeader::class);
    // احذف هذا السطر لتعطيل التحديث التلقائي:
    // $middleware->append(\App\Http\Middleware\UpdateUserLastActive::class);
})
```

---

## 🔋 الأداء

### Middleware Performance

**التحديث التلقائي:**
- يحدث مع كل API request
- عملية سريعة جداً (UPDATE query واحد)
- لا يؤثر على الأداء بشكل ملحوظ

**إذا كان لديك concerns:**

#### Option 1: استخدم Queue
```php
// في Middleware
if ($request->user()) {
    dispatch(function () use ($request) {
        $request->user()->update(['last_active_at' => now()]);
    })->afterResponse();
}
```

#### Option 2: Cache لمدة دقيقة
```php
if ($request->user()) {
    $cacheKey = "user_last_active_{$request->user()->id}";
    
    if (!Cache::has($cacheKey)) {
        $request->user()->update(['last_active_at' => now()]);
        Cache::put($cacheKey, true, 60); // 60 seconds
    }
}
```

---

## 📱 تجربة المستخدم

### سيناريو: مجموعة عائلية

```
👨 الأب: 🟢 متصل الآن (Owner)
👩 الأم: 🟢 متصل الآن (Member)
👦 الطفل 1: 🔴 نشط منذ 3 دقائق (Member, خارج النطاق)
👧 الطفل 2: 🟢 متصل الآن (Member)
```

**في التطبيق يظهر:**
- نقطة خضراء 🟢 بجانب الأعضاء المتصلين
- نقطة رمادية 🔴 بجانب الأعضاء غير المتصلين
- نص "متصل الآن" أو "نشط منذ X"
- يتحدث تلقائياً كل ما User يفتح التطبيق أو يستخدمه

---

## 🧪 اختبار الميزة

### Test Case 1: User يفتح التطبيق

```bash
# 1. User login
POST /api/login
{
  "email": "user@example.com",
  "password": "password"
}

# 2. تلقائياً: last_active_at يتحدث
# Result: is_online = true ✅

# 3. Get profile
GET /api/user
# Response: 
# {
#   "is_online": true,
#   "status": "online",
#   "last_seen": "متصل الآن"
# }
```

---

### Test Case 2: User يخرج من التطبيق

```bash
# 1. User نشط الآن
GET /api/user
# Result: is_online = true

# 2. User يغلق التطبيق
# ... مرور 6 دقائق بدون activity

# 3. User آخر يشوف المجموعة
GET /api/groups/1/members
# Result for this user:
# {
#   "is_online": false,
#   "status": "offline",
#   "last_seen": "نشط منذ 6 دقيقة"
# }
```

---

### Test Case 3: User يرجع بعد فترة

```bash
# 1. User كان offline منذ ساعة
# last_active_at = 2025-10-28 18:00:00

# 2. User يفتح التطبيق
GET /api/groups
# تلقائياً: last_active_at = now()

# 3. User آخر يشوف المجموعة
GET /api/groups/1/members
# Result:
# {
#   "is_online": true,
#   "status": "online",
#   "last_seen": "متصل الآن"
# } ✅
```

---

## 📊 Database Queries

### التحقق من الحالة يدوياً:

```sql
-- شوف كل المستخدمين المتصلين (آخر 5 دقائق)
SELECT 
    id, 
    name, 
    last_active_at,
    TIMESTAMPDIFF(MINUTE, last_active_at, NOW()) as minutes_ago
FROM users
WHERE last_active_at >= NOW() - INTERVAL 5 MINUTE;

-- شوف آخر نشاط لكل user
SELECT 
    id, 
    name, 
    last_active_at,
    CASE 
        WHEN last_active_at >= NOW() - INTERVAL 5 MINUTE THEN 'online'
        ELSE 'offline'
    END as status
FROM users
ORDER BY last_active_at DESC;
```

---

## 🎯 الفوائد

| الميزة | الفائدة |
|--------|---------|
| 🟢 **معرفة من متصل** | سهولة التواصل مع الأعضاء المتصلين |
| 📍 **دمج مع الموقع** | معرفة من متصل ومكانه في نفس الوقت |
| ⏱️ **Last Seen** | معرفة آخر نشاط للأعضاء |
| 🔄 **تحديث تلقائي** | بدون جهد من المطور |
| 💾 **بيانات تاريخية** | يمكن تحليل نشاط المستخدمين |

---

## 🔒 الخصوصية

### ملاحظات مهمة:

1. **الحالة مرئية لجميع أعضاء المجموعة**
   - يمكن لأي عضو رؤية حالة الأعضاء الآخرين

2. **إمكانية إخفاء الحالة** (مستقبلاً)
   - يمكن إضافة إعداد `hide_online_status` في `users` table
   - يمكن للمستخدم إخفاء حالته

3. **البيانات المحفوظة**
   - فقط `last_active_at` يُحفظ
   - لا يتم حفظ سجل كامل للنشاط

---

## 📚 الملفات المعدلة

```
✅ database/migrations/2025_10_28_192221_add_last_active_at_to_users_table.php
✅ app/Http/Middleware/UpdateUserLastActive.php
✅ app/Models/User.php
✅ app/Http/Resources/UserResource.php
✅ app/Http/Resources/GroupMemberResource.php
✅ bootstrap/app.php
✅ ONLINE_OFFLINE_STATUS_FEATURE.md (هذا الملف)
```

---

## ✅ الخلاصة

### ما تم إنجازه:

✅ **إضافة حقل `last_active_at`** في جدول users
✅ **Middleware تلقائي** يحدث الحالة مع كل request
✅ **Accessors ذكية** لحساب online/offline/last_seen
✅ **تحديث APIs** لعرض الحالة في جميع الـ endpoints
✅ **توثيق كامل** مع أمثلة للاستخدام

---

## 🚀 جاهز للاستخدام!

النظام يعمل الآن بشكل كامل وتلقائي:
- ✅ كل user يفتح التطبيق → يتحدث last_active_at
- ✅ APIs تعرض حالة online/offline
- ✅ يظهر "متصل الآن" أو "نشط منذ X"
- ✅ جاهز للعرض في التطبيق Mobile

**ابدأ بعرض الحالة في التطبيق! 🎉**

---

تاريخ التحديث: 28 أكتوبر 2025

