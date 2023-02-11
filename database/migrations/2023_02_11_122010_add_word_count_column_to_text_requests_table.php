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
            $table->integer('word_count')->default(0);
            $table->text('original_text')->nullable()->change();
            $table->foreignUuid('account_id')->nullable()->constrained('accounts')->cascadeOnDelete();
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
            $table->dropColumn('word_count');
            $table->dropColumn('account_id');
            $table->text('original_text')->change();
        });
    }
};
