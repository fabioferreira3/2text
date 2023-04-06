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
        Schema::table('text_request_logs', function (Blueprint $table) {
            $table->string('model')->default('gpt-4');
            $table->integer('prompt_token_usage')->default(0);
            $table->integer('completion_token_usage')->default(0);
            $table->renameColumn('token_usage', 'total_token_usage');
            $table->decimal('costs', 10, 4)->default(0.0000);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('text_request_logs', function (Blueprint $table) {
            $table->dropColumn('model');
            $table->dropColumn('prompt_token_usage');
            $table->dropColumn('completion_token_usage');
            $table->renameColumn('total_token_usage', 'token_usage');
            $table->dropColumn('costs');
        });
    }
};
