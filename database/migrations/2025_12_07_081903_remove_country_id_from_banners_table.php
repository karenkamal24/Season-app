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
        // First, find and drop the foreign key constraint using raw SQL
        // This is necessary because MySQL requires dropping foreign key before unique constraint
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'banners'
            AND COLUMN_NAME = 'country_id'
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");

        if (!empty($foreignKeys)) {
            foreach ($foreignKeys as $fk) {
                $constraintName = $fk->CONSTRAINT_NAME;
                DB::statement("ALTER TABLE `banners` DROP FOREIGN KEY `{$constraintName}`");
            }
        }

        // Then, drop the unique constraint
        Schema::table('banners', function (Blueprint $table) {
            $table->dropUnique(['country_id', 'language']);
        });

        // Finally, drop the column and add new unique constraint
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn('country_id');
            $table->unique('language');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            // Drop the unique constraint on language
            $table->dropUnique(['language']);

            // Add back country_id column
            $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');

            // Add back the unique constraint on country_id and language
            $table->unique(['country_id', 'language']);
        });
    }
};
