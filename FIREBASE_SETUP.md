# Firebase Cloud Messaging (FCM) Setup Guide

## Prerequisites

✅ Firebase project created  
✅ Service account JSON file downloaded from Firebase Console  
✅ Google API Client installed (`google/apiclient`)

---

## Installation Steps

### 1. Place Service Account File

Place your Firebase service account JSON file in:
```
storage/app/firebase/season-9ede3-firebase-adminsdk-fbsvc-c1b9e2f2e7.json
```

### 2. Configure Environment Variables

Add these lines to your `.env` file:

```env
# Firebase Cloud Messaging Configuration
FIREBASE_CREDENTIALS=firebase/season-9ede3-firebase-adminsdk-fbsvc-c1b9e2f2e7.json
FIREBASE_PROJECT_ID=season-9ede3
```

**Note:** Update the credentials path and project ID with your actual Firebase project details.

### 3. Clear Configuration Cache

Run this command to clear the configuration cache:
```bash
php artisan config:cache
```

---

## API Endpoints

### Authentication Required
All notification endpoints require Bearer token authentication via `auth:sanctum` middleware.

### 1. Send Notification to Specific User

**Endpoint:** `POST /api/notifications/send-to-user`

**Headers:**
```
Authorization: Bearer YOUR_API_TOKEN
Content-Type: application/json
```

**Request Body:**
```json
{
  "user_id": 1,
  "title": "Test Notification",
  "body": "This is a test message",
  "data": {
    "type": "test",
    "action": "open_screen"
  }
}
```

**Response:**
```json
{
  "success": true,
  "message": "Notification sent successfully",
  "response": {
    "name": "projects/season-9ede3/messages/..."
  }
}
```

---

### 2. Send Notification to All Users

**Endpoint:** `POST /api/notifications/send-to-all`

**Request Body:**
```json
{
  "title": "Important Announcement",
  "body": "New features available now!",
  "data": {
    "type": "announcement"
  }
}
```

---

### 3. Send Notification to Multiple Users

**Endpoint:** `POST /api/notifications/send-to-multiple`

**Request Body:**
```json
{
  "user_ids": [1, 2, 3, 4],
  "title": "Group Notification",
  "body": "You have been added to a new group",
  "data": {
    "type": "group",
    "group_id": "123"
  }
}
```

---

## Usage Examples in Controllers

### Example 1: Send Welcome Notification After Login

```php
use App\Services\FirebaseService;
use App\Jobs\SendPushNotification;

class AuthController extends Controller
{
    public function login(Request $request, FirebaseService $firebase)
    {
        // Your login logic here...
        $user = auth()->user();

        // Update FCM token in database
        if ($request->has('fcm_token')) {
            $user->fcm_token = $request->fcm_token;
            $user->save();
        }

        // Send welcome notification
        if ($user->fcm_token) {
            $firebase->sendToDevice(
                $user->fcm_token,
                'Welcome Back!',
                'Nice to see you again, ' . $user->name,
                [
                    'type' => 'welcome',
                    'user_id' => (string) $user->id,
                    'timestamp' => now()->toIso8601String()
                ]
            );
        }

        return response()->json([
            'status' => 200,
            'message' => 'Login successful',
            'data' => ['token' => $token, 'user' => $user]
        ]);
    }
}
```

### Example 2: Queue Notification (Asynchronous)

```php
use App\Jobs\SendPushNotification;

// Send immediately
SendPushNotification::dispatch(
    $user->fcm_token,
    'New Order',
    'Your order has been confirmed',
    ['order_id' => '12345']
);

// Send after 1 hour
SendPushNotification::dispatch(
    $user->fcm_token,
    'Reminder',
    'Your appointment is in 1 hour'
)->delay(now()->addHour());
```

### Example 3: Send to Multiple Users

```php
use App\Services\FirebaseService;

class OrderController extends Controller
{
    public function notifyVendors(FirebaseService $firebase)
    {
        $vendors = User::where('is_vendor', true)
            ->whereNotNull('fcm_token')
            ->pluck('fcm_token')
            ->toArray();

        $firebase->sendToMultipleDevices(
            $vendors,
            'New Order Available',
            'A customer is looking for your services',
            [
                'type' => 'new_order',
                'order_id' => '12345'
            ]
        );
    }
}
```

---

## Security Best Practices

✅ **Service Account File:** Never commit to version control (already in `.gitignore`)  
✅ **API Authentication:** All endpoints protected with `auth:sanctum`  
✅ **Token Validation:** FCM tokens are validated before sending  
✅ **Error Handling:** All exceptions are logged to `storage/logs/laravel.log`  
✅ **Rate Limiting:** Consider adding rate limiting to notification endpoints  

---

## Troubleshooting

### Error: "Failed to fetch access token"
**Solution:** 
- Verify service account JSON file path in `.env`
- Check file permissions in `storage/app/firebase/`
- Ensure the service account has "Firebase Cloud Messaging API Admin" role

### Error: "Invalid project ID"
**Solution:** 
- Verify `FIREBASE_PROJECT_ID` in `.env` matches your Firebase project
- Run `php artisan config:cache` after changing `.env`

### Error: "Token not valid"
**Solution:** 
- FCM token expired or invalid
- User needs to generate a new token (ask them to re-login or refresh token in mobile app)
- Remove invalid tokens from database

### Notifications Not Receiving on Mobile
**Solution:** 
- Verify FCM token is saved correctly in `users.fcm_token` column
- Check notification channel ID matches mobile app configuration (`season_app_channel`)
- Review `storage/logs/laravel.log` for error messages
- Test with Firebase Console's "Cloud Messaging" test tool

---

## Testing with Postman

### 1. Get Authentication Token
```
POST /api/auth/login
Body: { "email": "user@example.com", "password": "password" }
Response: { "token": "YOUR_API_TOKEN" }
```

### 2. Test Send Notification
```
POST /api/notifications/send-to-user
Headers:
  Authorization: Bearer YOUR_API_TOKEN
  Content-Type: application/json
Body:
{
  "user_id": 1,
  "title": "Test",
  "body": "Hello from Postman",
  "data": { "type": "test" }
}
```

---

## Mobile App Integration

### When User Logs In:
1. Mobile app generates FCM token
2. Send token to backend in login request: `{ "fcm_token": "DEVICE_TOKEN" }`
3. Backend saves token to `users.fcm_token` column

### When User Logs Out:
1. Mobile app sends request to clear FCM token
2. Backend sets `users.fcm_token` to `null`

### Token Refresh:
Firebase tokens can expire or change. Update the token:
```
PUT /api/profile
Body: { "fcm_token": "NEW_DEVICE_TOKEN" }
```

---

## Queue Configuration (Optional)

For better performance, configure Laravel queues to process notifications asynchronously:

1. **Set Queue Driver in `.env`:**
```env
QUEUE_CONNECTION=database
```

2. **Run Queue Worker:**
```bash
php artisan queue:work
```

3. **Use Queued Jobs:**
```php
SendPushNotification::dispatch($fcmToken, $title, $body, $data);
```

---

## Monitoring & Logs

All FCM operations are logged to `storage/logs/laravel.log`:

- ✅ Successful sends: `FCM Notification sent successfully`
- ❌ Failed sends: `FCM Notification failed`
- 🔑 Token errors: `Firebase Access Token Error`

Monitor these logs to track delivery issues and debug problems.

---

## Support

For issues or questions:
1. Check `storage/logs/laravel.log` for detailed error messages
2. Verify Firebase Console → Cloud Messaging settings
3. Test with Firebase Console's test notification feature
4. Review this documentation: [Firebase Cloud Messaging Documentation](https://firebase.google.com/docs/cloud-messaging)

---

**✨ Your Laravel backend is now ready to send Firebase Cloud Messaging notifications!**

