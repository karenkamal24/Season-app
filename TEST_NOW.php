<?php

/**
 * =====================================================
 * 🧪 ملف اختبار Firebase Cloud Messaging السريع
 * =====================================================
 *
 * شغّل هذا الملف في Terminal:
 * php TEST_NOW.php
 */

require __DIR__.'/vendor/autoload.php';

// Load Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n";
echo "═══════════════════════════════════════════════════\n";
echo "   🔥 Firebase Cloud Messaging - اختبار سريع\n";
echo "═══════════════════════════════════════════════════\n\n";

// Test 1: Check if Firebase credentials file exists
echo "✓ اختبار 1: التحقق من ملف Firebase...\n";
$credentialsPath = storage_path('app/firebase/season-9ede3-firebase-adminsdk-fbsvc-c1b9e2f2e7.json');
if (file_exists($credentialsPath)) {
    echo "   ✅ الملف موجود: {$credentialsPath}\n";
} else {
    echo "   ❌ الملف غير موجود: {$credentialsPath}\n";
    echo "   الرجاء التأكد من وجود ملف Firebase في المسار الصحيح\n";
}
echo "\n";

// Test 2: Check environment variables
echo "✓ اختبار 2: التحقق من إعدادات .env...\n";
$firebaseCredentials = env('FIREBASE_CREDENTIALS');
$firebaseProjectId = env('FIREBASE_PROJECT_ID');

if ($firebaseCredentials) {
    echo "   ✅ FIREBASE_CREDENTIALS = {$firebaseCredentials}\n";
} else {
    echo "   ❌ FIREBASE_CREDENTIALS غير موجود في .env\n";
    echo "   أضف: FIREBASE_CREDENTIALS=firebase/season-9ede3-firebase-adminsdk-fbsvc-c1b9e2f2e7.json\n";
}

if ($firebaseProjectId) {
    echo "   ✅ FIREBASE_PROJECT_ID = {$firebaseProjectId}\n";
} else {
    echo "   ❌ FIREBASE_PROJECT_ID غير موجود في .env\n";
    echo "   أضف: FIREBASE_PROJECT_ID=season-9ede3\n";
}
echo "\n";

// Test 3: Check database column
echo "✓ اختبار 3: التحقق من عمود fcm_token في قاعدة البيانات...\n";
try {
    $hasColumn = Schema::hasColumn('users', 'fcm_token');
    if ($hasColumn) {
        echo "   ✅ عمود fcm_token موجود في جدول users\n";
    } else {
        echo "   ❌ عمود fcm_token غير موجود\n";
        echo "   شغل: php artisan migrate\n";
    }
} catch (Exception $e) {
    echo "   ❌ خطأ في الاتصال بقاعدة البيانات: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 4: Check FirebaseService class
echo "✓ اختبار 4: التحقق من FirebaseService...\n";
try {
    $firebase = app(App\Services\FirebaseService::class);
    echo "   ✅ FirebaseService موجود ويعمل\n";

    // Test token validation
    $isValid = $firebase->isValidToken(str_repeat('a', 150));
    if ($isValid) {
        echo "   ✅ دالة isValidToken() تعمل بشكل صحيح\n";
    }
} catch (Exception $e) {
    echo "   ❌ خطأ في FirebaseService: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 5: Check routes
echo "✓ اختبار 5: التحقق من API Routes...\n";
$routes = [
    'api/notifications/send-to-user',
    'api/notifications/send-to-all',
    'api/notifications/send-to-multiple'
];

foreach ($routes as $route) {
    $routeExists = Route::has($route);
    if ($routeExists) {
        echo "   ✅ {$route}\n";
    } else {
        echo "   ⚠️  {$route} (تحقق من routes/api.php)\n";
    }
}
echo "\n";

// Test 6: Check users with FCM tokens
echo "✓ اختبار 6: فحص المستخدمين الذين لديهم FCM tokens...\n";
try {
    $usersWithTokens = DB::table('users')
        ->whereNotNull('fcm_token')
        ->count();

    if ($usersWithTokens > 0) {
        echo "   ✅ يوجد {$usersWithTokens} مستخدم لديهم FCM tokens\n";

        // Show first user with token
        $user = DB::table('users')->whereNotNull('fcm_token')->first();
        if ($user) {
            echo "   📱 مثال: المستخدم #{$user->id} - {$user->email}\n";
        }
    } else {
        echo "   ⚠️  لا يوجد مستخدمين لديهم FCM tokens\n";
        echo "   💡 لإضافة token تجريبي، شغل في Tinker:\n";
        echo "      \$user = User::first();\n";
        echo "      \$user->fcm_token = 'test_token_here';\n";
        echo "      \$user->save();\n";
    }
} catch (Exception $e) {
    echo "   ❌ خطأ: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 7: Check Job class
echo "✓ اختبار 7: التحقق من SendPushNotification Job...\n";
if (class_exists('App\Jobs\SendPushNotification')) {
    echo "   ✅ SendPushNotification Job موجود\n";
} else {
    echo "   ❌ SendPushNotification Job غير موجود\n";
}
echo "\n";

// Summary
echo "═══════════════════════════════════════════════════\n";
echo "   📊 ملخص الاختبار\n";
echo "═══════════════════════════════════════════════════\n\n";

if (file_exists($credentialsPath) && $firebaseCredentials && $firebaseProjectId) {
    echo "✅ الإعدادات الأساسية جاهزة!\n\n";
    echo "🚀 جرب الآن:\n";
    echo "   1. افتح Postman\n";
    echo "   2. سجل دخول للحصول على Token\n";
    echo "   3. أرسل طلب POST إلى:\n";
    echo "      http://localhost:8000/api/notifications/send-to-user\n\n";
    echo "📖 للمزيد من التفاصيل، افتح:\n";
    echo "   - TESTING_GUIDE_AR.md\n";
    echo "   - FIREBASE_SETUP.md\n\n";
} else {
    echo "⚠️  يوجد بعض الإعدادات الناقصة\n";
    echo "📝 راجع الخطوات أعلاه وأكمل الإعدادات المطلوبة\n\n";
}

echo "═══════════════════════════════════════════════════\n\n";



