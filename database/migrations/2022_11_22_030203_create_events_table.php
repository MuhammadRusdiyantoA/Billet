<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('description');
            $table->longText('tnc');
            $table->double('latitude');
            $table->double('longitude');
            $table->dateTime('date_start');
            $table->dateTime('date_end');
            $table->bigInteger('quota');
            $table->bigInteger('sold');
            $table->bigInteger('price');
            $table->foreignId('host_id')->constrained('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events', function (Blueprint $table) {
            $table->dropForeign('host_id');
        });
    }
}
