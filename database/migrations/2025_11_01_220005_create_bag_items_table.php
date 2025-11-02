<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bag_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('travel_bag_id')->constrained('travel_bags')->onDelete('cascade');
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->decimal('custom_weight', 8, 2)->nullable();
            $table->timestamps();
            $table->unique(['travel_bag_id', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bag_items');
    }
};

