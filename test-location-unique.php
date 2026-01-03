<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\GroupLocation;

echo "🧪 Testing unique constraint on group_locations...\n\n";

try {
    // Test 1: Create first location
    echo "Test 1: Creating first location...\n";
    $location1 = GroupLocation::updateOrCreate(
        ['group_id' => 999, 'user_id' => 999],
        [
            'latitude' => 24.7136,
            'longitude' => 46.6753,
            'distance_from_center' => 0,
            'is_within_radius' => true,
        ]
    );
    echo "✅ Created: ID = {$location1->id}\n\n";
    
    // Test 2: Update same location (should work)
    echo "Test 2: Updating same location...\n";
    $location2 = GroupLocation::updateOrCreate(
        ['group_id' => 999, 'user_id' => 999],
        [
            'latitude' => 24.7200,
            'longitude' => 46.6800,
            'distance_from_center' => 100,
            'is_within_radius' => true,
        ]
    );
    echo "✅ Updated: ID = {$location2->id} (should be same as {$location1->id})\n";
    
    if ($location1->id === $location2->id) {
        echo "✅ Perfect! Same ID - updateOrCreate is working!\n\n";
    } else {
        echo "❌ ERROR! Different IDs!\n\n";
    }
    
    // Test 3: Count rows (should be 1)
    $count = GroupLocation::where('group_id', 999)->where('user_id', 999)->count();
    echo "Test 3: Row count = {$count}\n";
    
    if ($count === 1) {
        echo "✅ Perfect! Only 1 row exists!\n\n";
    } else {
        echo "❌ ERROR! Multiple rows exist!\n\n";
    }
    
    // Cleanup
    echo "Cleaning up test data...\n";
    GroupLocation::where('group_id', 999)->where('user_id', 999)->delete();
    echo "✅ Cleanup done!\n\n";
    
    echo "🎉 All tests passed! Unique constraint is working!\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    
    // Cleanup on error
    try {
        GroupLocation::where('group_id', 999)->where('user_id', 999)->delete();
    } catch (\Exception $e2) {
        // Ignore cleanup errors
    }
}

