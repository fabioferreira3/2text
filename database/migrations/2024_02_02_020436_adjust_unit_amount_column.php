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
        Schema::table('unit_transactions', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->change();
        });

        Schema::table('accounts', function (Blueprint $table) {
            $table->decimal('units', 10, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unit_transactions', function (Blueprint $table) {
            $table->integer('amount')->change();
        });

        Schema::table('accounts', function (Blueprint $table) {
            $table->integer('units')->change();
        });
    }
};
