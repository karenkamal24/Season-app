<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Apple can supply names up to 255 characters.
        Schema::table('users', function (Blueprint $table) {
            $table->string('name', 255)->change();
        });
        // Existing installations already have these columns in the users migration.
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'provider')) {
                $table->string('provider')->nullable();
            }
            if (! Schema::hasColumn('users', 'provider_id')) {
                $table->string('provider_id')->nullable();
            }
        });
        Schema::table('users', function (Blueprint $table) {
            $table->index(['provider', 'provider_id'], 'users_provider_provider_id_index');
        });
    }

    public function down(): void
    {
        // Keep the larger name capacity to avoid truncating names on rollback.
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_provider_provider_id_index');
        });
    }
};
