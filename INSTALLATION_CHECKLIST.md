# Firebase Cloud Messaging - Installation Checklist

## ✅ Completed Steps

All implementation has been completed. Here's what was done:

### 1. ✅ Package Installation
- Google API Client package reference added

### 2. ✅ File Structure Created
```
storage/app/firebase/          → Directory for service account JSON
storage/app/firebase/.gitkeep  → Keeps directory in git
```

### 3. ✅ Core Services
- `app/Services/FirebaseService.php` → Main FCM service with all methods
- `app/Providers/AppServiceProvider.php` → Service registered as singleton

### 4. ✅ API Endpoints
- `app/Http/Controllers/NotificationController.php` → Controller with 3 endpoints
- `routes/api.php` → Routes configured with authentication

### 5. ✅ Database Setup
- `database/migrations/..._add_fcm_token_to_users_table.php` → Migration created
- Migration executed: `fcm_token` column added to users table
- `app/Models/User.php` → Model updated with fcm_token field

### 6. ✅ Queue Support
- `app/Jobs/SendPushNotification.php` → Job for async notifications

### 7. ✅ Security
- `.gitignore` → Service account JSON files excluded from git

### 8. ✅ Documentation
- `FIREBASE_SETUP.md` → Complete setup and usage guide
- `INSTALLATION_CHECKLIST.md` → This checklist

---

## 🔧 Manual Steps Required

### Step 1: Install Google API Client Package

Run this command in your terminal:
```bash
composer require google/apiclient
```

### Step 2: Place Firebase Service Account File

1. Download your service account JSON from Firebase Console:
   - Go to Project Settings → Service Accounts
   - Click "Generate New Private Key"
   
2. Save the file to:
   ```
   storage/app/firebase/season-9ede3-firebase-adminsdk-fbsvc-c1b9e2f2e7.json
   ```

### Step 3: Update Environment Variables

Add to your `.env` file:
```env
FIREBASE_CREDENTIALS=firebase/season-9ede3-firebase-adminsdk-fbsvc-c1b9e2f2e7.json
FIREBASE_PROJECT_ID=season-9ede3
```

**Important:** Replace `season-9ede3` with your actual Firebase project ID!

### Step 4: Clear Configuration Cache

```bash
php artisan config:cache
```

---

## 🧪 Testing

### Test 1: Check API Endpoints

```bash
php artisan route:list --path=notifications
```

Should show:
```
POST  api/notifications/send-to-user
POST  api/notifications/send-to-all
POST  api/notifications/send-to-multiple
```

### Test 2: Send Test Notification (Postman)

1. Login to get auth token:
```
POST /api/auth/login
Body: { "email": "your@email.com", "password": "password" }
```

2. Send test notification:
```
POST /api/notifications/send-to-user
Headers:
  Authorization: Bearer YOUR_TOKEN
  Content-Type: application/json
Body:
{
  "user_id": 1,
  "title": "Test Notification",
  "body": "Hello from Firebase!",
  "data": { "type": "test" }
}
```

### Test 3: Check Logs

```bash
tail -f storage/logs/laravel.log
```

Look for messages like:
- `FCM Notification sent successfully`
- `Firebase Access Token Error` (if config is wrong)

---

## 📱 Mobile App Integration

Your mobile app needs to:

1. **Initialize Firebase SDK** in the app
2. **Get FCM Token** on app startup
3. **Send Token to Backend** during login/registration:
   ```json
   {
     "email": "user@example.com",
     "password": "password",
     "fcm_token": "DEVICE_TOKEN_FROM_FIREBASE"
   }
   ```

4. **Update AuthController** to save FCM token:
   ```php
   if ($request->has('fcm_token')) {
       $user->fcm_token = $request->fcm_token;
       $user->save();
   }
   ```

---

## 🚀 Quick Usage Examples

### Example 1: Send Welcome Notification After Login
```php
use App\Services\FirebaseService;

public function login(Request $request, FirebaseService $firebase)
{
    // ... login logic ...
    
    if ($user->fcm_token) {
        $firebase->sendToDevice(
            $user->fcm_token,
            'Welcome Back!',
            'Nice to see you again, ' . $user->name,
            ['type' => 'welcome']
        );
    }
}
```

### Example 2: Queue Notification (Better Performance)
```php
use App\Jobs\SendPushNotification;

SendPushNotification::dispatch(
    $user->fcm_token,
    'New Message',
    'You have a new message',
    ['message_id' => '123']
);
```

### Example 3: Broadcast to All Users
```php
use App\Services\FirebaseService;

public function broadcast(FirebaseService $firebase)
{
    $firebase->sendToAllUsers(
        'Maintenance Notice',
        'System will be down for maintenance at 2 AM',
        ['type' => 'announcement']
    );
}
```

---

## 📊 Available API Endpoints

All endpoints require `Authorization: Bearer TOKEN` header.

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/notifications/send-to-user` | Send to specific user by user_id |
| POST | `/api/notifications/send-to-all` | Send to all users (topic: all_users) |
| POST | `/api/notifications/send-to-multiple` | Send to multiple users by user_ids array |

---

## 🛠️ FirebaseService Methods

```php
// Send to specific device
$firebase->sendToDevice($fcmToken, $title, $body, $data);

// Send to topic (all subscribed users)
$firebase->sendToTopic($topic, $title, $body, $data);

// Send to all users
$firebase->sendToAllUsers($title, $body, $data);

// Send to multiple devices
$firebase->sendToMultipleDevices($tokens, $title, $body, $data);

// Validate token format
$firebase->isValidToken($token);
```

---

## 🔍 Troubleshooting

### "Failed to fetch access token"
- Check if service account JSON file exists
- Verify file path in `.env`
- Check file permissions

### "Invalid project ID"
- Verify `FIREBASE_PROJECT_ID` in `.env`
- Must match Firebase Console project ID
- Run `php artisan config:cache`

### "User has no FCM token"
- User hasn't logged in from mobile app
- Mobile app isn't sending FCM token
- Token not saved to database

---

## 📚 Documentation Files

- **FIREBASE_SETUP.md** → Complete setup guide with examples
- **INSTALLATION_CHECKLIST.md** → This checklist
- **storage/logs/laravel.log** → Runtime logs and errors

---

## ✨ You're All Set!

After completing the manual steps above, your Laravel backend will be ready to send push notifications via Firebase Cloud Messaging!

For detailed usage examples and troubleshooting, refer to `FIREBASE_SETUP.md`.

