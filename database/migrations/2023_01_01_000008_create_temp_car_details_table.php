<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTempCarDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('temp_car_details', function (Blueprint $table) {
            // Prema dump-u: PRIMARY KEY (temp_id). 
            // Ovo znači da je 1:1 veza (jedan temp_user - jedan temp_car_details).
            $table->string('temp_id', 255)->primary();

            $table->string('brand', 255)->nullable();
            $table->string('model', 255)->nullable();
            $table->integer('year')->nullable();
            $table->string('fuel_type', 255)->nullable();
            $table->string('engine_capacity', 255)->nullable();
            $table->string('engine_power', 255)->nullable();
            $table->string('transmission', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();

            // Ako želimo foreign key (temp_id) -> (temp_users.temp_id):
            $table->foreign('temp_id')
                  ->references('temp_id')->on('temp_users')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('temp_car_details');
    }
}
