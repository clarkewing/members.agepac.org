<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePollsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('polls', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('thread_id');
            $table->string('title', 255);
            $table->boolean('votes_editable');
            $table->unsignedBigInteger('max_votes');
            $table->unsignedTinyInteger('votes_privacy');
            $table->boolean('results_before_voting');
            $table->dateTime('locked_at')->nullable(true);

            $table->foreign('thread_id')
            ->references('id')
            ->on('threads')
            ->onDelete('cascade');

            // Only one poll is allowed per thread
            $table->unique('thread_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('polls');
    }
}
