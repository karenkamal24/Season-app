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
        // Change provider_token from string(255) to text to accommodate long Google/Apple tokens
        Schema::table('users', function (Blueprint $table) {
            $table->text('provider_token')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to string(255) - note: this may fail if there are tokens longer than 255 chars
        Schema::table('users', function (Blueprint $table) {
            $table->string('provider_token')->nullable()->change();
        });
    }
};

