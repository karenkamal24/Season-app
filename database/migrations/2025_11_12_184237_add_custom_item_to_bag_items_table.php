<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();
        
        // Drop the existing unique constraint first
        try {
            Schema::table('bag_items', function (Blueprint $table) {
                $table->dropUnique(['travel_bag_id', 'item_id']);
            });
        } catch (\Exception $e) {
            // Constraint might not exist, continue
        }
        
        // Make item_id nullable and add custom_item_name - different approaches for different databases
        if ($driver === 'sqlite') {
            // SQLite: We need to recreate the table to change nullable
            DB::statement('
                CREATE TABLE bag_items_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    travel_bag_id INTEGER NOT NULL,
                    item_id INTEGER NULL,
                    custom_item_name VARCHAR(255) NULL,
                    quantity INTEGER NOT NULL DEFAULT 1,
                    custom_weight DECIMAL(8,2) NULL,
                    created_at TIMESTAMP NULL,
                    updated_at TIMESTAMP NULL,
                    FOREIGN KEY (travel_bag_id) REFERENCES travel_bags(id) ON DELETE CASCADE,
                    FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE
                )
            ');
            
            // Copy data from old table
            DB::statement('
                INSERT INTO bag_items_new (id, travel_bag_id, item_id, quantity, custom_weight, created_at, updated_at)
                SELECT id, travel_bag_id, item_id, quantity, custom_weight, created_at, updated_at
                FROM bag_items
            ');
            
            // Drop old table and rename new one
            Schema::dropIfExists('bag_items');
            DB::statement('ALTER TABLE bag_items_new RENAME TO bag_items');
        } else {
            // MySQL/PostgreSQL: Use ALTER TABLE
            try {
                Schema::table('bag_items', function (Blueprint $table) {
                    $table->dropForeign(['item_id']);
                });
            } catch (\Exception $e) {
                // Foreign key might not exist, continue
            }
            
            // Add custom_item_name column
            Schema::table('bag_items', function (Blueprint $table) {
                $table->string('custom_item_name')->nullable()->after('item_id');
            });
            
            // Make item_id nullable
            DB::statement('ALTER TABLE bag_items MODIFY item_id BIGINT UNSIGNED NULL');
            
            // Re-add foreign key constraint
            Schema::table('bag_items', function (Blueprint $table) {
                $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();
        
        // Make item_id not nullable again and remove custom_item_name
        if ($driver === 'sqlite') {
            // SQLite: Recreate table without custom_item_name and with NOT NULL item_id
            DB::statement('
                CREATE TABLE bag_items_old (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    travel_bag_id INTEGER NOT NULL,
                    item_id INTEGER NOT NULL,
                    quantity INTEGER NOT NULL DEFAULT 1,
                    custom_weight DECIMAL(8,2) NULL,
                    created_at TIMESTAMP NULL,
                    updated_at TIMESTAMP NULL,
                    FOREIGN KEY (travel_bag_id) REFERENCES travel_bags(id) ON DELETE CASCADE,
                    FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE,
                    UNIQUE(travel_bag_id, item_id)
                )
            ');
            
            // Copy only items with item_id (exclude custom items)
            DB::statement('
                INSERT INTO bag_items_old (id, travel_bag_id, item_id, quantity, custom_weight, created_at, updated_at) 
                SELECT id, travel_bag_id, item_id, quantity, custom_weight, created_at, updated_at 
                FROM bag_items 
                WHERE item_id IS NOT NULL
            ');
            
            // Drop old table and rename
            Schema::dropIfExists('bag_items');
            DB::statement('ALTER TABLE bag_items_old RENAME TO bag_items');
        } else {
            // MySQL/PostgreSQL: Use ALTER TABLE
            try {
                Schema::table('bag_items', function (Blueprint $table) {
                    $table->dropForeign(['item_id']);
                });
            } catch (\Exception $e) {
                // Foreign key might not exist, continue
            }
            
            // Drop custom_item_name
            Schema::table('bag_items', function (Blueprint $table) {
                $table->dropColumn('custom_item_name');
            });
            
            // Delete custom items (items without item_id) before making item_id NOT NULL
            DB::table('bag_items')->whereNull('item_id')->delete();
            
            // Make item_id not nullable
            DB::statement('ALTER TABLE bag_items MODIFY item_id BIGINT UNSIGNED NOT NULL');
            
            // Re-add foreign key and unique constraint
            Schema::table('bag_items', function (Blueprint $table) {
                $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
                $table->unique(['travel_bag_id', 'item_id']);
            });
        }
    }
};
