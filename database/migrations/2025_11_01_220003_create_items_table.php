<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('item_categories')->onDelete('cascade');
            $table->string('name_en'); // e.g., "laptop"
            $table->string('name_ar')->nullable(); // e.g., "حاسوب محمول"
            $table->decimal('default_weight', 8, 2)->default(0.0);
            $table->string('weight_unit', 10)->default('kg');
            $table->string('icon')->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_ar')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};

