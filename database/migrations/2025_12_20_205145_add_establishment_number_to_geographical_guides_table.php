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
        Schema::table('geographical_guides', function (Blueprint $table) {
            $table->string('establishment_number')->nullable()->after('commercial_register');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('geographical_guides', function (Blueprint $table) {
            $table->dropColumn('establishment_number');
        });
    }
};
