<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name_en'); // e.g., "electronics"
            $table->string('name_ar')->nullable(); // e.g., "إلكترونيات"
            $table->string('icon')->nullable();
            $table->string('icon_color')->nullable(); // Hex color code
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_categories');
    }
};

