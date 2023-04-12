<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_history', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('document_id')->constrained('documents');
            $table->string('description')->nullable();
            $table->text('content')->nullable();
            $table->integer('word_count')->default(0);
            $table->string('model')->nullable();
            $table->integer('prompt_token_usage')->default(0);
            $table->integer('completion_token_usage')->default(0);
            $table->integer('total_token_usage')->default(0);
            $table->decimal('cost', 10, 4)->default(0.0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('document_history');
    }
};
