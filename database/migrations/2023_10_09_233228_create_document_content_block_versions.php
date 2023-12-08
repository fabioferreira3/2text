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
        Schema::create('document_content_block_versions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('document_content_block_id')->constrained('document_content_blocks')->cascadeOnDelete();
            $table->text('content')->nullable();
            $table->integer('version')->default(1);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_content_block_versions');
    }
};
