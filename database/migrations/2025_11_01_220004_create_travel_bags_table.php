<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('travel_bags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('bag_type_id')->constrained('bag_types')->onDelete('cascade');
            $table->string('name_en')->nullable(); // Custom bag name
            $table->string('name_ar')->nullable(); // Custom bag name Arabic
            $table->decimal('max_weight', 8, 2)->default(23.0);
            $table->string('weight_unit', 10)->default('kg');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('travel_bags');
    }
};

