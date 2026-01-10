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
            // Add essential flag column for AI-suggested items
            // This allows users to mark items as essential when adding from AI suggestions
            if (!Schema::hasColumn('bag_items', 'essential')) {
                $table->boolean('essential')->default(false)->after('custom_weight');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bag_items', function (Blueprint $table) {
            if (Schema::hasColumn('bag_items', 'essential')) {
                $table->dropColumn('essential');
            }
        });
    }
};
