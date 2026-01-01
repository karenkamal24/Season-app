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
        Schema::create('bag_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bag_id')->constrained()->onDelete('cascade');
            $table->string('analysis_id')->unique();
            $table->json('missing_items')->nullable();
            $table->json('extra_items')->nullable();
            $table->json('weight_optimization')->nullable();
            $table->json('additional_suggestions')->nullable();
            $table->json('smart_alert')->nullable();
            $table->json('metadata')->nullable();
            $table->decimal('confidence_score', 3, 2)->default(0)->comment('AI confidence score 0-1');
            $table->integer('processing_time_ms')->nullable();
            $table->string('ai_model')->default('gemini-2.0-flash-exp');
            $table->timestamps();

            $table->index('bag_id');
            $table->index('analysis_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bag_analyses');
    }
};
