<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResponsesTable extends Migration
{
    public function up()
    {
        Schema::create('responses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('question_id')->unsigned()->nullable();
            $table->text('content')->nullable();
            $table->timestamp('created_at')->useCurrent();

            // Foreign key
            $table->foreign('question_id')
                  ->references('id')->on('questions')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('responses');
    }
}
