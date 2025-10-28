<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "📊 Checking Users Status\n\n";

$users = DB::table('users')->whereIn('id', [1, 12])->get();

foreach ($users as $user) {
    echo "User ID: {$user->id}\n";
    echo "Name: {$user->name}\n";
    echo "Email: {$user->email}\n";
    echo "Last Active: " . ($user->last_active_at ?? 'NULL') . "\n";

    if ($user->last_active_at) {
        $lastActive = new DateTime($user->last_active_at);
        $now = new DateTime();
        $diff = $now->getTimestamp() - $lastActive->getTimestamp();
        $minutes = floor($diff / 60);

        echo "Minutes ago: $minutes\n";

        if ($minutes < 5) {
            echo "Status: 🟢 ONLINE\n";
        } else {
            echo "Status: 🔴 OFFLINE (last seen $minutes minutes ago)\n";
        }
    } else {
        echo "Status: 🔴 OFFLINE (never active after update)\n";
    }

    echo "\n" . str_repeat("-", 50) . "\n\n";
}

echo "💡 Solution:\n";
echo "Make ANY API request (with Bearer token) and last_active_at will update!\n";
echo "\nExample:\n";
echo "GET /api/groups\n";
echo "Authorization: Bearer YOUR_TOKEN\n";

