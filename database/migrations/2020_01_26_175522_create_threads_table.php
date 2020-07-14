<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateThreadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('threads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('slug')->unique()->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('channel_id');
            $table->unsignedInteger('visits')->default(0);
            $table->string('title');
            $table->unsignedBigInteger('best_post_id')->nullable();
            $table->boolean('locked')->default(false);
            $table->boolean('pinned')->default(false);
            $table->timestamps();

            $table->foreign('best_post_id')
                ->references('id')
                ->on('posts')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('threads');
    }
}
