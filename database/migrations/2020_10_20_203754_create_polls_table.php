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
            $table->unsignedBigInteger('thread_id')->unique();
            $table->string('title');
            $table->boolean('votes_editable');
            $table->unsignedBigInteger('max_votes')->nullable();
            $table->unsignedTinyInteger('votes_privacy');
            $table->boolean('results_before_voting');
            $table->timestamp('locked_at')->nullable();
            $table->timestamps();

            $table->foreign('thread_id')
                ->references('id')
                ->on('threads')
                ->onDelete('cascade');
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
