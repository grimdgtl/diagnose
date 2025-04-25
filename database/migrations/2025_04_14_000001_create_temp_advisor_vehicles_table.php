<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTempAdvisorVehiclesTable extends Migration
{
    public function up()
    {
        Schema::create('temp_advisor_vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('temp_id', 255);
            $table->string('brand');
            $table->string('model');
            $table->integer('year');
            $table->integer('mileage');
            $table->string('engine_capacity');
            $table->string('engine_power');
            $table->string('fuel_type');
            $table->string('transmission');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('temp_id')->references('temp_id')->on('temp_advisor_users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('temp_advisor_vehicles');
    }
}
