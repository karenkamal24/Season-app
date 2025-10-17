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
        Schema::create('users', function (Blueprint $table) {
            $table->id();


            $table->string('name', 100);
            $table->string('nickname', 100)->nullable()->unique();
            $table->string('email', 320)->unique();
            $table->string('phone', 20)->nullable();


            $table->string('photo_url')->nullable();
            $table->string('address')->nullable();
            $table->string('avatar')->nullable();


            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
            $table->string('city', 100)->nullable();


            $table->string('currency')->nullable();
            $table->string('language')->nullable();


            $table->string('provider')->nullable();
            $table->string('provider_id')->nullable();


            $table->string('role')->default('customer');
            $table->boolean('is_blocked')->default(false);


            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();

            $table->string('last_otp')->nullable();
            $table->timestamp('last_otp_expire')->nullable();
            $table->string('notification_token', 500)->nullable();


            $table->integer('request')->default(0);
            $table->integer('coins')->default(0);
            $table->integer('trips')->default(0);
            $table->boolean('has_interests')->default(false);


            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email', 320)->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
