# 📱 اختبار نظام المجموعات على Postman

## 🎯 نظرة عامة

هذا الدليل الشامل لاختبار نظام **عدم الضياع** (Groups System) باستخدام Postman.

---

## 📦 الملفات المتاحة

### 1. ملفات Postman (جاهزة للاستيراد)
- ✅ **Groups_API_Collection.postman_collection.json** - Collection كامل بكل الـ endpoints
- ✅ **Groups_API_Environment.postman_environment.json** - Environment variables جاهز

### 2. الأدلة والوثائق
- 📘 **POSTMAN_QUICK_START.md** - دليل البدء السريع (ابدأ من هنا!)
- 📗 **POSTMAN_TEST_SCENARIOS.md** - سيناريوهات اختبار مفصلة بالأمثلة
- 📙 **GROUPS_API_DOCUMENTATION.md** - وثائق API كاملة
- 📕 **GROUPS_QUICK_REFERENCE.md** - مرجع سريع

---

## 🚀 البدء السريع (3 خطوات)

### الخطوة 1: استيراد الملفات
```
1. افتح Postman
2. Import → اسحب "Groups_API_Collection.postman_collection.json"
3. Import → اسحب "Groups_API_Environment.postman_environment.json"
4. فعّل Environment من القائمة العلوية
```

### الخطوة 2: إعداد المتغيرات
```
1. اضغط على أيقونة Environment (العين)
2. غيّر base_url إذا لزم الأمر (الافتراضي: http://localhost:8000/api)
3. اترك باقي المتغيرات فارغة (سنملأها من الردود)
```

### الخطوة 3: ابدأ الاختبار
```
1. افتح Collection "Season App - Groups API"
2. اتبع الترتيب:
   → 1. Authentication
   → 2. Groups - CRUD
   → 3. Join & Members
   → 4. Location Tracking
   → 5. SOS Alerts
```

---

## 📋 الترتيب الموصى به

### المرحلة 1: إعداد المستخدمين (5 دقائق)
```
✅ Register User 1 → احفظ token في user1_token
✅ Verify OTP User 1 → حدّث user1_token
✅ Register User 2 → احفظ token في user2_token
✅ Verify OTP User 2 → حدّث user2_token
```

### المرحلة 2: إنشاء وإدارة المجموعة (3 دقائق)
```
✅ Create Group → احفظ group_id و invite_code
✅ Get All Groups
✅ Get Group Details
```

### المرحلة 3: الأعضاء (3 دقائق)
```
✅ Get Invite Details
✅ Join Group (User 2)
✅ Get Group Members
```

### المرحلة 4: تتبع المواقع (5 دقائق)
```
✅ Update Location - User 1 (Center)
✅ Update Location - User 2 (Within Range)
✅ Update Location - User 2 (OUT OF RANGE) 🔔 إشعار!
✅ View Group (شاهد out_of_range_count)
```

### المرحلة 5: حالات الطوارئ (3 دقائق)
```
✅ Send SOS Alert → احفظ alert_id 🚨 إشعار طوارئ!
✅ View Group (شاهد active_sos_alerts)
✅ Resolve SOS Alert
```

---

## 🎯 السيناريوهات الرئيسية

### 🔵 السيناريو 1: رحلة عائلية عادية
1. الأب ينشئ مجموعة "رحلة دبي"
2. الأم والأطفال ينضمون
3. الجميع يحدثون مواقعهم بشكل دوري
4. الجميع داخل النطاق ✅

### 🟡 السيناريو 2: طفل يبتعد عن العائلة
1. الطفل يحدث موقعه
2. المسافة > 100 متر
3. الوالدان يتلقون إشعار تلقائي 🔔
4. يمكنهم رؤية موقعه على الخريطة

### 🔴 السيناريو 3: حالة طوارئ
1. الطفل يرسل إشارة SOS 🚨
2. جميع أفراد العائلة يتلقون إشعار فوري
3. الإشعار يحتوي على الموقع والرسالة
4. بعد حل المشكلة، يتم إغلاق الـ SOS

---

## 📊 جدول الـ Endpoints

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| **Authentication** |
| POST | `/auth/register` | ❌ | تسجيل مستخدم |
| POST | `/auth/verify-otp` | ❌ | تحقق OTP |
| **Groups** |
| GET | `/groups` | ✅ | كل مجموعاتي |
| POST | `/groups` | ✅ | إنشاء مجموعة |
| GET | `/groups/{id}` | ✅ | تفاصيل مجموعة |
| PUT | `/groups/{id}` | ✅ | تحديث (Owner) |
| DELETE | `/groups/{id}` | ✅ | حذف (Owner) |
| **Members** |
| GET | `/groups/invite/{code}` | ❌ | معلومات دعوة |
| POST | `/groups/join` | ✅ | انضمام |
| GET | `/groups/{id}/members` | ✅ | عرض أعضاء |
| POST | `/groups/{id}/leave` | ✅ | مغادرة |
| DELETE | `/groups/{id}/members/{userId}` | ✅ | إزالة عضو (Owner) |
| **Location** |
| POST | `/groups/{id}/location` | ✅ | تحديث موقع |
| **SOS** |
| POST | `/groups/{id}/sos` | ✅ | إرسال SOS |
| POST | `/groups/{id}/sos/{alertId}/resolve` | ✅ | إغلاق SOS |

---

## 💡 نصائح مهمة

### ✅ Token Management
```
• بعد Register → احفظ token
• بعد Verify OTP → حدّث token (مهم!)
• استخدم user1_token للـ Owner
• استخدم user2_token للـ Member
```

### ✅ IDs Management
```
• group_id → من response إنشاء المجموعة
• invite_code → من response إنشاء المجموعة
• alert_id → من response إرسال SOS
```

### ✅ Testing Tips
```
• جرب كل endpoint بترتيب
• اقرأ الردود جيداً
• احفظ المعرفات المهمة
• جرب حالات الخطأ (مثلاً: token خاطئ)
```

---

## 🔧 Troubleshooting

### ❌ خطأ 401 Unauthorized
**السبب:** Token غير صحيح أو منتهي  
**الحل:**
- تأكد من وجود token في Environment
- تأكد من تحديثه بعد verify-otp
- تأكد من استخدام الـ token الصحيح

### ❌ خطأ 404 Not Found
**السبب:** المورد غير موجود  
**الحل:**
- تأكد من group_id في Environment
- تأكد من أن المجموعة لم تُحذف

### ❌ خطأ 403 Forbidden
**السبب:** لا تملك صلاحية  
**الحل:**
- تأكد من أنك عضو في المجموعة
- بعض العمليات للـ Owner فقط

### ❌ الإشعارات لا تصل
**السبب:** Firebase غير معد  
**الحل:**
- راجع `FIREBASE_SETUP.md`
- تأكد من وجود fcm_token في جدول users

---

## 📚 الملفات المرجعية

### للبدء السريع:
👉 **POSTMAN_QUICK_START.md** - ابدأ من هنا!

### للتفاصيل الكاملة:
📘 **POSTMAN_TEST_SCENARIOS.md** - سيناريوهات مفصلة  
📙 **GROUPS_API_DOCUMENTATION.md** - وثائق API كاملة  
📕 **GROUPS_QUICK_REFERENCE.md** - مرجع سريع

### للإعداد:
🛠️ **GROUPS_SETUP_GUIDE.md** - دليل الإعداد  
🔥 **FIREBASE_SETUP.md** - إعداد Firebase

---

## 🎬 فيديو توضيحي (Flow)

```
1. Authentication
   ├── Register User 1 (أحمد - Owner)
   ├── Verify OTP
   ├── Register User 2 (سارة - Member)
   └── Verify OTP

2. Create Group
   └── "رحلة دبي - العائلة" (safety_radius: 100m)

3. Join Group
   └── سارة تنضم باستخدام invite_code

4. Update Locations
   ├── أحمد: (25.2048, 55.2708) ✅ داخل النطاق
   ├── سارة: (25.2050, 55.2710) ✅ داخل النطاق
   └── سارة: (25.2150, 55.2800) ❌ خارج النطاق
       └── 🔔 إشعار لأحمد: "سارة خارج النطاق"

5. Send SOS
   └── سارة: "أحتاج المساعدة!"
       └── 🚨 إشعار طوارئ لأحمد

6. Resolve SOS
   └── أحمد يغلق الإشارة بعد إيجاد سارة
```

---

## ✅ Checklist قبل البدء

- [ ] Laravel Server يعمل (`php artisan serve`)
- [ ] Database موجودة ومعدة
- [ ] تم تنفيذ Migrations
- [ ] Postman مثبت
- [ ] تم استيراد Collection
- [ ] تم استيراد Environment
- [ ] تم تفعيل Environment

---

## 🎯 الخطوات التالية

### بعد الاختبار على Postman:

1. **✅ تكامل Mobile App**
   - استخدم نفس الـ endpoints
   - أضف Location Services
   - أضف QR Code Scanner

2. **✅ إضافة الإشعارات**
   - إعداد Firebase Cloud Messaging
   - حفظ FCM Tokens
   - اختبار الإشعارات الحقيقية

3. **✅ تحسينات إضافية**
   - إضافة صور للمجموعات
   - تاريخ المواقع
   - إحصائيات

---

## 🆘 الدعم

### إذا واجهت مشاكل:

1. **راجع الوثائق:**
   - `POSTMAN_QUICK_START.md`
   - `TROUBLESHOOTING_401_AR.md`

2. **تحقق من Logs:**
   - `storage/logs/laravel.log`

3. **اختبر بشكل منفصل:**
   - جرب endpoint واحد في كل مرة
   - تأكد من البيانات المرسلة

---

## 🎉 ملخص

**الملفات الأساسية:**
- `Groups_API_Collection.postman_collection.json` → استورده
- `Groups_API_Environment.postman_environment.json` → استورده
- `POSTMAN_QUICK_START.md` → اقرأه أولاً

**الترتيب:**
1. Authentication (تسجيل المستخدمين)
2. Create Group (إنشاء مجموعة)
3. Join Group (انضمام الأعضاء)
4. Update Locations (تحديث المواقع)
5. Send SOS (إشارات الطوارئ)

**النتيجة:**
✅ نظام كامل لتتبع المواقع وحالات الطوارئ

---

## 📞 مستعد؟

**ابدأ الآن:**
```
1. استورد الملفات في Postman
2. افتح POSTMAN_QUICK_START.md
3. اتبع التعليمات خطوة بخطوة
```

**Good luck! 🚀🎉**

