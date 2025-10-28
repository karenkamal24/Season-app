<?php
/**
 * One-time script to set last_active_at for all users
 * This makes all users appear online immediately
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔄 Updating last_active_at for all users...\n\n";

// Update all users to have last_active_at = now
$count = DB::table('users')->update(['last_active_at' => now()]);

echo "✅ Updated {$count} users!\n\n";

// Show the updated users
$users = DB::table('users')->whereIn('id', [1, 12])->get(['id', 'name', 'last_active_at']);

foreach ($users as $user) {
    echo "✓ User: {$user->name} (ID: {$user->id})\n";
    echo "  Last Active: {$user->last_active_at}\n";
    echo "  Status: 🟢 ONLINE\n\n";
}

echo "🎉 Done! All users will now show as ONLINE!\n";
echo "\n📝 Note: This is a one-time update.\n";
echo "From now on, last_active_at will update automatically with each API request.\n";

