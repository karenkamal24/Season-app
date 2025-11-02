# أمثلة Postman لـ Reminders API

## متطلبات أساسية:
- Base URL: `http://localhost:8000/api` (أو URL السيرفر الخاص بك)
- Header مطلوب: `Authorization: Bearer {token}`
- للحصول على Token: استخدم `/api/auth/login`

---

## 1. إنشاء تذكير جديد (مع ملف مرفق)

### Request Details:
- **Method:** `POST`
- **URL:** `{{base_url}}/reminders`
- **Headers:**
  ```
  Authorization: Bearer {{token}}
  Accept: application/json
  ```

### Body (form-data):
| Key | Type | Value |
|-----|------|-------|
| `title` | Text | `موعد السفر للعاصمة` |
| `date` | Text | `2025-11-15` |
| `time` | Text | `14:30` |
| `recurrence` | Text | `once` |
| `notes` | Text | `لا تنسى الجواز وتذاكر السفر` |
| `timezone` | Text | `Africa/Cairo` |
| `attachment` | File | اختر ملف (صورة أو PDF) |

### مثال على القيم:
```
title: "موعد السفر للعاصمة"
date: "2025-11-15"
time: "14:30"
recurrence: "once"
notes: "لا تنسى الجواز وتذاكر السفر"
timezone: "Africa/Cairo"
attachment: [اختر ملف من جهازك]
```

### Response Example (201 Created):
```json
{
    "success": true,
    "message": "Reminder created successfully",
    "data": {
        "reminder_id": 1,
        "title": "موعد السفر للعاصمة",
        "date": "2025-11-15",
        "time": "14:30:00",
        "timezone": "Africa/Cairo",
        "recurrence": "once",
        "recurrence_arabic": "مرة واحدة",
        "notes": "لا تنسى الجواز وتذاكر السفر",
        "attachment": "http://localhost:8000/storage/reminders/1/abc123.jpg",
        "status": "active",
        "created_at": "2025-11-02T18:00:00+00:00",
        "updated_at": "2025-11-02T18:00:00+00:00"
    }
}
```

---

## 2. إنشاء تذكير بدون ملف مرفق

### Request Details:
- **Method:** `POST`
- **URL:** `{{base_url}}/reminders`
- **Headers:** (نفس ما سبق)

### Body (form-data):
| Key | Type | Value |
|-----|------|-------|
| `title` | Text | `تذكير يومي` |
| `date` | Text | `2025-11-10` |
| `time` | Text | `09:00` |
| `recurrence` | Text | `daily` |
| `notes` | Text | `خذ الأدوية الصباحية` |
| `timezone` | Text | `UTC` |

---

## 3. تحديث تذكير (مع رفع ملف جديد)

### Request Details:
- **Method:** `PUT`
- **URL:** `{{base_url}}/reminders/1` (1 هو reminder_id)
- **Headers:**
  ```
  Authorization: Bearer {{token}}
  Accept: application/json
  ```

### Body (form-data):
| Key | Type | Value |
|-----|------|-------|
| `title` | Text | `موعد السفر (محدث)` |
| `notes` | Text | `تم تحديث الملاحظات` |
| `attachment` | File | اختر ملف جديد (سيتم حذف القديم تلقائياً) |

### Response Example (200 OK):
```json
{
    "success": true,
    "message": "Reminder updated successfully",
    "data": {
        "reminder_id": 1,
        "title": "موعد السفر (محدث)",
        "attachment": "http://localhost:8000/storage/reminders/1/newfile.pdf",
        ...
    }
}
```

---

## 4. تحديث تذكير بدون تغيير الملف

### Request Details:
- **Method:** `PUT`
- **URL:** `{{base_url}}/reminders/1`
- **Headers:** (نفس ما سبق)

### Body (raw JSON):
```json
{
    "title": "عنوان محدث",
    "notes": "ملاحظات محدثة",
    "status": "completed"
}
```

**ملاحظة:** عند استخدام JSON، لا يمكن رفع ملف. استخدم `form-data` لرفع الملفات.

---

## 5. الحصول على جميع التذكيرات

### Request Details:
- **Method:** `GET`
- **URL:** `{{base_url}}/reminders`
- **Headers:**
  ```
  Authorization: Bearer {{token}}
  Accept: application/json
  ```

### Query Parameters (اختيارية):
- `status`: `active`, `completed`, `cancelled`
- `from_date`: `2025-11-01`
- `to_date`: `2025-11-30`

### مثال:
```
GET {{base_url}}/reminders?status=active&from_date=2025-11-01
```

### Response Example:
```json
{
    "success": true,
    "data": {
        "reminders": [
            {
                "reminder_id": 1,
                "title": "موعد السفر",
                "date": "2025-11-15",
                "time": "14:30:00",
                "attachment": "http://localhost:8000/storage/reminders/1/file.jpg",
                "status": "active",
                ...
            }
        ],
        "active_count": 5,
        "total_count": 1
    }
}
```

---

## 6. الحصول على تذكير واحد

### Request Details:
- **Method:** `GET`
- **URL:** `{{base_url}}/reminders/1`
- **Headers:** (نفس ما سبق)

---

## 7. حذف تذكير

### Request Details:
- **Method:** `DELETE`
- **URL:** `{{base_url}}/reminders/1`
- **Headers:**
  ```
  Authorization: Bearer {{token}}
  Accept: application/json
  ```

### Response Example:
```json
{
    "success": true,
    "message": "Reminder deleted successfully"
}
```

**ملاحظة:** سيتم حذف الملف المرفق تلقائياً من السيرفر.

---

## أنواع recurrence المتاحة:
- `once` - مرة واحدة
- `daily` - يومي
- `weekly` - أسبوعي
- `monthly` - شهري

---

## أنواع الملفات المدعومة:
- صور: `jpg`, `jpeg`, `png`, `gif`
- مستندات: `pdf`, `doc`, `docx`
- الحجم الأقصى: **10MB**

---

## أمثلة على Timezones:
- `UTC`
- `Africa/Cairo`
- `Asia/Riyadh`
- `America/New_York`

---

## نصائح مهمة:

1. **لرفع ملف:** استخدم `form-data` وليس `raw` أو `x-www-form-urlencoded`
2. **لإرسال بيانات بدون ملف:** يمكنك استخدام `raw JSON` أو `form-data`
3. **الملفات القديمة:** يتم حذفها تلقائياً عند رفع ملف جديد في `update`
4. **الملفات المحذوفة:** يتم حذفها تلقائياً عند حذف التذكير

