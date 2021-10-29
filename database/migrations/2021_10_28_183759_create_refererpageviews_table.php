<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRefererPageviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referer_pageviews', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uri');
            $table->integer('timestamp');
            $table->char('refererHash', 90);
            $table->unique(['uri', 'timestamp', 'refererHash']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pageviews');
    }
}
