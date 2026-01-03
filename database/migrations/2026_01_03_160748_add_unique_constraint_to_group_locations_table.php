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
        // First, cleanup duplicates - keep only the latest location for each user in each group
        $this->cleanupDuplicates();

        Schema::table('group_locations', function (Blueprint $table) {
            // Drop foreign keys first (they depend on the index)
            $table->dropForeign(['group_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('group_locations', function (Blueprint $table) {
            // Drop the old index
            $table->dropIndex(['group_id', 'user_id']);

            // Add unique constraint (only one location per user per group)
            $table->unique(['group_id', 'user_id'], 'group_locations_group_user_unique');
        });

        Schema::table('group_locations', function (Blueprint $table) {
            // Re-add foreign keys
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Cleanup duplicate locations before adding unique constraint
     */
    protected function cleanupDuplicates(): void
    {
        // Get all unique combinations that have duplicates
        $combinations = \DB::table('group_locations')
            ->select('group_id', 'user_id')
            ->groupBy('group_id', 'user_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($combinations as $combo) {
            // Get IDs to keep (the most recent one)
            $keepId = \DB::table('group_locations')
                ->where('group_id', $combo->group_id)
                ->where('user_id', $combo->user_id)
                ->orderBy('updated_at', 'desc')
                ->value('id');

            // Delete all others
            \DB::table('group_locations')
                ->where('group_id', $combo->group_id)
                ->where('user_id', $combo->user_id)
                ->where('id', '!=', $keepId)
                ->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('group_locations', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['group_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('group_locations', function (Blueprint $table) {
            // Drop unique constraint
            $table->dropUnique('group_locations_group_user_unique');

            // Re-add the index
            $table->index(['group_id', 'user_id']);
        });

        Schema::table('group_locations', function (Blueprint $table) {
            // Re-add foreign keys
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
