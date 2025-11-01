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
        Schema::table('emergency_numbers', function (Blueprint $table) {
            $table->string('embassy')->nullable()->after('ambulance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('emergency_numbers', function (Blueprint $table) {
            $table->dropColumn('embassy');
        });
    }
};
