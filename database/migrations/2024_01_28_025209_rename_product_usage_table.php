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
        Schema::rename('product_usage', 'app_usage');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('app_usage', 'product_usage');
    }
};
