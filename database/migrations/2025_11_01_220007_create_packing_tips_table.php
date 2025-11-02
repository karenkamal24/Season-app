<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packing_tips', function (Blueprint $table) {
            $table->id();
            $table->text('text_en'); // English text
            $table->text('text_ar')->nullable(); // Arabic text
            $table->string('category')->nullable(); // e.g., "organization", "space_saving", "security", "weight"
            $table->integer('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packing_tips');
    }
};

