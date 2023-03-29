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
        Schema::table('text_requests', function (Blueprint $table) {
            $table->dropColumn('subheaders');
            $table->dropColumn('paragraphs');
            $table->integer('target_word_count')->default(500);
            $table->text('outline')->default('');
            $table->json('raw_structure')->default('[]');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('text_requests', function (Blueprint $table) {
            $table->json('subheaders')->default('{}');
            $table->json('paragraphs')->default('{}');
            $table->dropColumn('target_word_count');
            $table->dropColumn('raw_structure');
            $table->dropColumn('outline');
        });
    }
};
