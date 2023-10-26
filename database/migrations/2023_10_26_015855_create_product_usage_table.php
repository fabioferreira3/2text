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
        Schema::create('product_usage', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('account_id')->constrained('accounts');
            $table->uuid('user_id')->nullable()->constrained('users');
            $table->json('meta')->default('{}');
            $table->string('model')->nullable();
            $table->integer('prompt_token_usage')->default(0);
            $table->integer('completion_token_usage')->default(0);
            $table->integer('total_token_usage')->default(0);
            $table->decimal('cost', 10, 4)->default(0.0);
            $table->timestamps();
        });

        Schema::table('document_history', function (Blueprint $table) {
            $table->dropColumn('model');
            $table->dropColumn('prompt_token_usage');
            $table->dropColumn('completion_token_usage');
            $table->dropColumn('total_token_usage');
            $table->dropColumn('cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_usage');

        Schema::table('document_history', function (Blueprint $table) {
            $table->string('model')->nullable();
            $table->integer('prompt_token_usage')->default(0);
            $table->integer('completion_token_usage')->default(0);
            $table->integer('total_token_usage')->default(0);
            $table->decimal('cost', 10, 4)->default(0.0);
        });
    }
};
