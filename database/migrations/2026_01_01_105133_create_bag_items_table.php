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
        Schema::create('bag_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bag_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->decimal('weight', 8, 2)->default(0)->comment('Weight in kg');
            $table->enum('category', [
                'ملابس',
                'أحذية',
                'إلكترونيات',
                'أدوية وعناية',
                'مستندات',
                'أخرى'
            ])->default('أخرى');
            $table->boolean('essential')->default(false)->comment('Is this item essential?');
            $table->boolean('packed')->default(false)->comment('Is this item packed?');
            $table->text('notes')->nullable();
            $table->integer('quantity')->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->index('bag_id');
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bag_items');
    }
};
