<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('locatable_id');
            $table->string('locatable_type');
            $table->string('type');
            $table->string('name')->nullable();
            $table->string('street_line_1')->nullable();
            $table->string('street_line_2')->nullable();
            $table->string('municipality')->nullable();
            $table->string('administrative_area')->nullable();
            $table->string('sub_administrative_area')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
            $table->string('country_code', 2)->nullable();
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
        Schema::dropIfExists('locations');
    }
}
