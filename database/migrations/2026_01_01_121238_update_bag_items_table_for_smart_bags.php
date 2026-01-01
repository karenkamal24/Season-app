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
            // Check if old column exists and rename it
            if (Schema::hasColumn('bag_items', 'travel_bag_id') && !Schema::hasColumn('bag_items', 'bag_id')) {
                $table->renameColumn('travel_bag_id', 'bag_id');
            }

            // Add new columns for Smart Bags system if they don't exist
            if (!Schema::hasColumn('bag_items', 'name')) {
                $table->string('name')->after('bag_id');
            }

            if (!Schema::hasColumn('bag_items', 'weight')) {
                $table->decimal('weight', 8, 2)->default(0)->after('name');
            }

            // Note: category column will be replaced by item_category_id in next migration
            // So we skip adding it here

            if (!Schema::hasColumn('bag_items', 'essential')) {
                $table->boolean('essential')->default(false)->after('category');
            }

            if (!Schema::hasColumn('bag_items', 'packed')) {
                $table->boolean('packed')->default(false)->after('essential');
            }

            if (!Schema::hasColumn('bag_items', 'notes')) {
                $table->text('notes')->nullable()->after('packed');
            }

            // Make sure quantity exists (might exist from old system)
            if (!Schema::hasColumn('bag_items', 'quantity')) {
                $table->integer('quantity')->default(1)->after('notes');
            }

            // Add soft deletes if not exists
            if (!Schema::hasColumn('bag_items', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bag_items', function (Blueprint $table) {
            // Rename back to travel_bag_id
            if (Schema::hasColumn('bag_items', 'bag_id')) {
                $table->renameColumn('bag_id', 'travel_bag_id');
            }

            // Note: We don't drop columns in down() to preserve data
            // If you want to drop them, uncomment below:
            /*
            $table->dropColumn([
                'name',
                'weight',
                'category',
                'essential',
                'packed',
                'notes'
            ]);
            */
        });
    }
};
