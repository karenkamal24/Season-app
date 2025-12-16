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
        Schema::create('geographical_guides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('geographical_category_id')->constrained('geographical_categories')->onDelete('cascade');
            $table->foreignId('geographical_sub_category_id')
            ->nullable()
            ->constrained('geographical_sub_categories')
            ->cascadeOnDelete();

            $table->string('service_name');
            $table->text('description')->nullable();
            $table->string('phone_1')->nullable();
            $table->string('phone_2')->nullable();
            $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');
            $table->foreignId('city_id')->constrained('cities')->onDelete('cascade');
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('website')->nullable();
            $table->string('commercial_register')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geographical_guides');
    }
};
