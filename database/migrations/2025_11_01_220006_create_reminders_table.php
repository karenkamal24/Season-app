<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->date('date');
            $table->time('time');
            $table->string('timezone')->default('UTC');
            $table->enum('recurrence', ['once', 'daily', 'weekly', 'monthly'])->default('once');
            $table->text('notes')->nullable();
            $table->string('attachment')->nullable(); // Image/file attachment path
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};

