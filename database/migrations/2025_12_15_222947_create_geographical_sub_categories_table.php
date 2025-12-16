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
        Schema::create('geographical_sub_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('geographical_category_id')->constrained('geographical_categories')->onDelete('cascade');
            $table->string('name_ar');
            $table->string('name_en');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to drop foreign key manually - it will be dropped automatically
        // when geographical_guides table is dropped (which happens first in rollback order)
        Schema::dropIfExists('geographical_sub_categories');
    }

};
