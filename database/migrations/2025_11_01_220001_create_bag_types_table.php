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
        Schema::create('bag_types', function (Blueprint $table) {
            $table->id();
            $table->string('name_en'); // e.g., "Travel Bag"
            $table->string('name_ar')->nullable(); // e.g., "شنطة سفر"
            $table->string('code')->unique(); // e.g., "travel_bag", "backpack"
            $table->text('description_en')->nullable();
            $table->text('description_ar')->nullable();
            $table->decimal('default_max_weight', 8, 2)->default(23.0); // Default weight in kg
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bag_types');
    }
};

