# 🚀 دليل البدء السريع - Postman

## 📥 1. استيراد الملفات إلى Postman

### الخطوة 1: استيراد Collection
1. افتح Postman
2. اضغط على **Import** في الأعلى
3. اسحب ملف `Groups_API_Collection.postman_collection.json`
4. أو اضغط **Choose Files** واختر الملف
5. سيظهر لك Collection اسمه **"Season App - Groups API"**

### الخطوة 2: استيراد Environment
1. في Postman، اضغط على أيقونة **Environment** (التروس) في الأعلى
2. اضغط **Import**
3. اسحب ملف `Groups_API_Environment.postman_environment.json`
4. اضغط على Environment الجديد لتفعيله

---

## ⚙️ 2. إعداد Environment Variables

### المتغيرات الأساسية:

| Variable | القيمة الافتراضية | الوصف |
|----------|-------------------|--------|
| `base_url` | `http://localhost:8000/api` | عنوان API |
| `user1_token` | (فارغ) | Token للمستخدم الأول (Owner) |
| `user2_token` | (فارغ) | Token للمستخدم الثاني (Member) |
| `group_id` | (فارغ) | معرف المجموعة |
| `invite_code` | (فارغ) | كود الدعوة |
| `alert_id` | (فارغ) | معرف إشارة SOS |

### كيفية تحديث المتغيرات:

**الطريقة الأولى: يدوياً**
1. اضغط على أيقونة **Environment** في الأعلى
2. اختر Environment الذي استوردته
3. غير القيم في عمود **CURRENT VALUE**

**الطريقة الثانية: تلقائياً من Response** (موصى بها)
- عند تسجيل الدخول، انسخ الـ `token` من الرد
- الصقه في `user1_token` أو `user2_token`

---

## 🎯 3. ترتيب التنفيذ الصحيح

### المرحلة 1: التحضير
```
1. Authentication → Register User 1 (Owner)
   → انسخ token من الرد وضعه في user1_token

2. Authentication → Verify OTP User 1
   → انسخ token الجديد وضعه في user1_token

3. Authentication → Register User 2 (Member)
   → انسخ token من الرد وضعه في user2_token

4. Authentication → Verify OTP User 2
   → انسخ token الجديد وضعه في user2_token
```

### المرحلة 2: إنشاء المجموعة
```
5. Groups - CRUD → Create New Group
   → انسخ id من الرد وضعه في group_id
   → انسخ invite_code من الرد وضعه في invite_code
```

### المرحلة 3: انضمام الأعضاء
```
6. Join & Members → Join Group
   (استخدم user2_token)
   
7. Join & Members → Get Group Members
   (شاهد كل الأعضاء)
```

### المرحلة 4: تتبع المواقع
```
8. Location Tracking → Update Location - User 1 (Center)
9. Location Tracking → Update Location - User 2 (Within Range)
10. Location Tracking → Update Location - User 2 (OUT OF RANGE)
    ← سيتم إرسال إشعار تلقائي!
```

### المرحلة 5: حالات الطوارئ
```
11. SOS Alerts → Send SOS Alert
    → انسخ id من الرد وضعه في alert_id
    ← سيتم إرسال إشعار طوارئ فوراً!

12. SOS Alerts → Resolve SOS Alert
```

---

## 📁 4. شرح Folders في Collection

### 📂 1. Authentication
يحتوي على:
- تسجيل المستخدم الأول
- التحقق من OTP للمستخدم الأول
- تسجيل المستخدم الثاني
- التحقق من OTP للمستخدم الثاني

### 📂 2. Groups - CRUD
العمليات الأساسية:
- **GET** `/groups` - عرض كل مجموعاتي
- **POST** `/groups` - إنشاء مجموعة جديدة
- **GET** `/groups/{id}` - تفاصيل مجموعة
- **PUT** `/groups/{id}` - تحديث مجموعة
- **DELETE** `/groups/{id}` - حذف مجموعة

### 📂 3. Join & Members
إدارة الأعضاء:
- **GET** `/groups/invite/{code}` - معلومات الدعوة (Public)
- **POST** `/groups/join` - الانضمام للمجموعة
- **GET** `/groups/{id}/members` - عرض الأعضاء
- **POST** `/groups/{id}/leave` - مغادرة المجموعة
- **DELETE** `/groups/{id}/members/{userId}` - إزالة عضو

### 📂 4. Location Tracking
تتبع المواقع:
- تحديث موقع المستخدم الأول (مركز المجموعة)
- تحديث موقع المستخدم الثاني داخل النطاق
- تحديث موقع المستخدم الثاني خارج النطاق (يرسل إشعار)

### 📂 5. SOS Alerts
إشارات الطوارئ:
- إرسال إشارة SOS
- إغلاق إشارة SOS

### 📂 6. Test Scenarios
سيناريوهات جاهزة للاختبار السريع

---

## 🔑 5. نصائح مهمة

### ✅ Token Management
```
- بعد كل تسجيل دخول، احفظ الـ token في Environment
- لا تنسى تحديث الـ tokens بعد Verify OTP
- تأكد من استخدام Token الصحيح (user1 أو user2)
```

### ✅ IDs Management
```
- بعد إنشاء مجموعة، احفظ:
  - group_id
  - invite_code
  
- بعد إرسال SOS، احفظ:
  - alert_id
```

### ✅ Testing Flow
```
1. ابدأ من Authentication
2. أنشئ مجموعة من User 1
3. انضم بـ User 2
4. جرب تحديث المواقع
5. جرب SOS
```

---

## 🧪 6. سيناريو اختبار كامل (خطوة بخطوة)

### السيناريو: رحلة عائلية

```bash
# 1. سجل المستخدم الأول (الأب)
POST /auth/register
{
  "name": "أحمد محمد",
  "email": "ahmed@test.com",
  ...
}
# احفظ token → user1_token

# 2. تحقق من OTP
POST /auth/verify-otp
# احفظ token الجديد → user1_token

# 3. سجل المستخدم الثاني (الأم)
POST /auth/register
{
  "name": "سارة أحمد",
  "email": "sara@test.com",
  ...
}
# احفظ token → user2_token

# 4. تحقق من OTP
POST /auth/verify-otp
# احفظ token الجديد → user2_token

# 5. أنشئ مجموعة من الأب
POST /groups
Authorization: Bearer {{user1_token}}
{
  "name": "رحلة دبي - العائلة",
  "safety_radius": 100
}
# احفظ group_id و invite_code

# 6. الأم تنضم للمجموعة
POST /groups/join
Authorization: Bearer {{user2_token}}
{
  "invite_code": "{{invite_code}}"
}

# 7. الأب يحدث موقعه
POST /groups/{{group_id}}/location
Authorization: Bearer {{user1_token}}
{
  "latitude": 25.2048,
  "longitude": 55.2708
}

# 8. الأم تحدث موقعها (قريبة)
POST /groups/{{group_id}}/location
Authorization: Bearer {{user2_token}}
{
  "latitude": 25.2050,
  "longitude": 55.2710
}

# 9. الأم تبتعد (خارج النطاق)
POST /groups/{{group_id}}/location
Authorization: Bearer {{user2_token}}
{
  "latitude": 25.2150,
  "longitude": 55.2800
}
# ← الأب يتلقى إشعار تلقائي

# 10. الأم ترسل SOS
POST /groups/{{group_id}}/sos
Authorization: Bearer {{user2_token}}
{
  "latitude": 25.2150,
  "longitude": 55.2800,
  "message": "أنا تائهة! أحتاج مساعدة"
}
# احفظ alert_id
# ← الأب يتلقى إشعار طوارئ فوري

# 11. الأب يشاهد تفاصيل المجموعة
GET /groups/{{group_id}}
Authorization: Bearer {{user1_token}}
# يرى موقع الأم وإشارة SOS

# 12. بعد إيجاد الأم، يتم إغلاق SOS
POST /groups/{{group_id}}/sos/{{alert_id}}/resolve
Authorization: Bearer {{user1_token}}
```

---

## 📊 7. فهم الردود (Responses)

### ✅ Success Response
```json
{
  "status": 200,
  "message": "تم بنجاح",
  "data": { ... }
}
```

### ❌ Error Response
```json
{
  "status": 400,
  "message": "رسالة الخطأ",
  "errors": { ... }
}
```

### 🚨 Special Cases

**Out of Range Notification:**
```json
{
  "title": "تنبيه: عضو خارج النطاق",
  "body": "سارة أحمد تجاوز المسافة المحددة (100متر)"
}
```

**SOS Notification:**
```json
{
  "title": "🚨 إشارة SOS - طوارئ",
  "body": "سارة أحمد يحتاج المساعدة!"
}
```

---

## 🔧 8. Troubleshooting

### المشكلة: 401 Unauthorized
**الحل:**
- تأكد من وجود token في Environment
- تأكد من تحديث token بعد verify-otp
- تأكد من تفعيل Environment الصحيح

### المشكلة: 404 Not Found
**الحل:**
- تأكد من وجود group_id في Environment
- تأكد من أن المجموعة موجودة (لم يتم حذفها)

### المشكلة: 403 Forbidden
**الحل:**
- تأكد من أنك عضو في المجموعة
- بعض العمليات للـ Owner فقط (Update, Delete)

### المشكلة: "Already a member"
**الحل:**
- هذا المستخدم انضم للمجموعة مسبقاً
- جرب مع مستخدم آخر

---

## 📝 9. متطلبات قبل الاختبار

### ✅ Checklist:

- [ ] Laravel Server يعمل (`php artisan serve`)
- [ ] Database موجودة
- [ ] Migrations تم تنفيذها
- [ ] Firebase معد (للإشعارات)
- [ ] Postman مثبت
- [ ] Collection مستورد
- [ ] Environment مستورد ومفعّل

---

## 🎯 10. الخطوات التالية

بعد الاختبار على Postman:

1. **تكامل مع Mobile App:**
   - استخدم نفس الـ endpoints
   - أضف Location Services
   - أضف QR Scanner
   - أضف FCM للإشعارات

2. **اختبار الإشعارات:**
   - احفظ FCM tokens في جدول users
   - جرب الإشعارات الحقيقية

3. **اختبار Edge Cases:**
   - ماذا يحدث إذا Owner غادر؟
   - ماذا يحدث إذا حُذفت المجموعة؟
   - اختبار حذف أعضاء

---

## 📚 مراجع إضافية

- **API Documentation:** `GROUPS_API_DOCUMENTATION.md`
- **Test Scenarios:** `POSTMAN_TEST_SCENARIOS.md`
- **Quick Reference:** `GROUPS_QUICK_REFERENCE.md`
- **Setup Guide:** `GROUPS_SETUP_GUIDE.md`

---

## 🎉 جاهز للتجربة!

ابدأ الآن:
1. استورد Collection و Environment
2. فعّل Environment
3. ابدأ من folder "1. Authentication"
4. اتبع الترتيب

**Good luck! 🚀**

