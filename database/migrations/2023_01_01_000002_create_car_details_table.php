<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('car_details', function (Blueprint $table) {
            $table->increments('id'); // int(11) AUTO_INCREMENT
            // user_id je varchar(255), moze biti NULL
            $table->string('user_id', 255)->nullable();
            $table->string('brand', 255)->nullable();
            $table->string('model', 255)->nullable();
            $table->integer('year')->nullable();
            $table->string('fuel_type', 255)->nullable();
            $table->string('engine_capacity', 255)->nullable();
            $table->string('engine_power', 255)->nullable();
            $table->string('transmission', 255)->nullable();

            $table->timestamp('created_at')->useCurrent();

            // Foreign key prema users(id) (varchar)
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('car_details');
    }
}
