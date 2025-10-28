<?php
/**
 * Quick Test Script for Online Status Middleware
 *
 * Run this to verify the middleware is working:
 * php TEST_MIDDLEWARE.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🧪 Testing UpdateUserLastActive Middleware\n\n";

// Test 1: Check if middleware exists
echo "1️⃣ Checking Middleware Registration...\n";
$middlewarePath = __DIR__ . '/app/Http/Middleware/UpdateUserLastActive.php';
if (file_exists($middlewarePath)) {
    echo "   ✅ Middleware file exists\n";
} else {
    echo "   ❌ Middleware file NOT found!\n";
    exit(1);
}

// Test 2: Check bootstrap registration
echo "\n2️⃣ Checking Bootstrap Registration...\n";
$bootstrapContent = file_get_contents(__DIR__ . '/bootstrap/app.php');
if (strpos($bootstrapContent, 'UpdateUserLastActive') !== false) {
    echo "   ✅ Middleware registered in bootstrap/app.php\n";
} else {
    echo "   ❌ Middleware NOT registered in bootstrap/app.php!\n";
    exit(1);
}

// Test 3: Check if uses Cache and DB
echo "\n3️⃣ Checking Middleware Code...\n";
$middlewareContent = file_get_contents($middlewarePath);
if (strpos($middlewareContent, 'Cache::') !== false && strpos($middlewareContent, 'DB::table') !== false) {
    echo "   ✅ Middleware uses Cache and DB (NEW VERSION)\n";
} else {
    echo "   ⚠️  Middleware might be using old code!\n";
    echo "   Please check if file was updated correctly\n";
}

// Test 4: Check a real user
echo "\n4️⃣ Checking Users Table...\n";
try {
    $user = DB::table('users')->where('id', 1)->first();
    if ($user) {
        echo "   ✅ Users table accessible\n";
        echo "   User ID: {$user->id}\n";
        echo "   Name: {$user->name}\n";
        echo "   Last Active: " . ($user->last_active_at ?? 'NULL') . "\n";

        if ($user->last_active_at) {
            $lastActive = new DateTime($user->last_active_at);
            $now = new DateTime();
            $diff = $now->getTimestamp() - $lastActive->getTimestamp();
            $minutes = floor($diff / 60);

            echo "   Last active: $minutes minutes ago\n";

            if ($minutes < 5) {
                echo "   🟢 User would show as ONLINE\n";
            } else {
                echo "   🔴 User would show as OFFLINE\n";
            }
        } else {
            echo "   ⚠️  last_active_at is NULL (user never logged in after update)\n";
        }
    } else {
        echo "   ❌ User ID 1 not found\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// Test 5: Test Cache
echo "\n5️⃣ Testing Cache System...\n";
try {
    Cache::put('test_key', 'test_value', 60);
    $value = Cache::get('test_key');
    if ($value === 'test_value') {
        echo "   ✅ Cache system working\n";
        Cache::forget('test_key');
    } else {
        echo "   ❌ Cache system NOT working properly\n";
    }
} catch (Exception $e) {
    echo "   ❌ Cache Error: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "✅ All checks passed!\n\n";
echo "📝 Next Steps:\n";
echo "1. Make sure to run: php artisan cache:clear\n";
echo "2. Make sure to run: php artisan config:clear\n";
echo "3. If on production server, restart PHP-FPM\n";
echo "4. Try making an API request (any authenticated endpoint)\n";
echo "5. Check the user's last_active_at in database\n";
echo "\n";

