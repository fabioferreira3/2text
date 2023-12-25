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
        Schema::create('document_tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->index();
            $table->foreignUuid('document_id')->constrained('documents');
            $table->uuid('process_id')->index()->nullable();
            $table->smallInteger('order')->default(1);
            $table->smallInteger('progress')->default(0);
            $table->string('status')->default('pending');
            $table->string('job');
            $table->json('meta')->default('{}');
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
        Schema::dropIfExists('document_tasks');
    }
};
