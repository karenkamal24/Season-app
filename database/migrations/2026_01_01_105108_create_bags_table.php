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
        Schema::create('bags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('trip_type', ['عمل', 'سياحة', 'عائلية', 'علاج'])->default('سياحة');
            $table->integer('duration')->comment('Duration in days');
            $table->string('destination');
            $table->date('departure_date');
            $table->decimal('max_weight', 8, 2)->default(20.00)->comment('Maximum weight in kg');
            $table->decimal('total_weight', 8, 2)->default(0)->comment('Current total weight in kg');
            $table->enum('status', ['draft', 'in_progress', 'completed', 'cancelled'])->default('draft');
            $table->json('preferences')->nullable()->comment('User preferences like style, priorities');
            $table->boolean('is_analyzed')->default(false);
            $table->timestamp('last_analyzed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('departure_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bags');
    }
};
