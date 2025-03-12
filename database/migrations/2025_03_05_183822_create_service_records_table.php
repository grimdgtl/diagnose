<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceRecordsTable extends Migration
{
    public function up()
    {
        Schema::create('service_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('car_detail_id'); // Promenjeno sa foreignId na unsignedInteger
            $table->foreign('car_detail_id')
                  ->references('id')
                  ->on('car_details')
                  ->onDelete('cascade');
            $table->date('service_date');
            $table->text('description');
            $table->integer('mileage');
            $table->decimal('cost', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_records');
    }
}