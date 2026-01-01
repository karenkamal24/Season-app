<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bag_items', function (Blueprint $table) {
            // Drop the old enum category column if it exists
            if (Schema::hasColumn('bag_items', 'category')) {
                $table->dropColumn('category');
            }

            // Add item_category_id foreign key
            if (!Schema::hasColumn('bag_items', 'item_category_id')) {
                $table->foreignId('item_category_id')
                    ->nullable()
                    ->after('weight')
                    ->constrained('item_categories')
                    ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bag_items', function (Blueprint $table) {
            // Drop foreign key and column
            if (Schema::hasColumn('bag_items', 'item_category_id')) {
                $table->dropForeign(['item_category_id']);
                $table->dropColumn('item_category_id');
            }

            // Re-add enum category column
            if (!Schema::hasColumn('bag_items', 'category')) {
                $table->enum('category', [
                    'ملابس',
                    'أحذية',
                    'إلكترونيات',
                    'أدوية وعناية',
                    'مستندات',
                    'أخرى'
                ])->default('أخرى')->after('weight');
            }
        });
    }
};
