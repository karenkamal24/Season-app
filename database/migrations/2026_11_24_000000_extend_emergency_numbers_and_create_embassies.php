<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('emergency_numbers', function (Blueprint $table) {
            $table->char('country_code', 3)->nullable()->unique();
            $table->string('unified_emergency', 20)->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->char('nationality', 3)->nullable();
        });

        Schema::create('embassies', function (Blueprint $table) {
            $table->id();
            $table->char('country_represented_code', 3);
            $table->char('host_country_code', 3);
            $table->string('name');
            $table->string('phone');
            $table->text('address');
            $table->timestamps();
            $table->unique(['country_represented_code', 'host_country_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('embassies');
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('nationality'));
        Schema::table('emergency_numbers', function (Blueprint $table) {
            $table->dropUnique(['country_code']);
            $table->dropColumn(['country_code', 'unified_emergency']);
        });
    }
};
