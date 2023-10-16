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
        Schema::create('chat_thread_iterations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('chat_thread_id')->constrained('chat_threads')->cascadeOnDelete();
            $table->string('origin');
            $table->text('response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_thread_iterations');
    }
};
