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
        Schema::create('text_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('original_text');
            $table->text('paraphrased_text')->nullable();
            $table->text('summary')->nullable();
            $table->string('title')->nullable();
            $table->text('meta_description')->nullable();
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
        Schema::dropIfExists('text_requests');
    }
};
