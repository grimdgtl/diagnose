<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTempResponsesTable extends Migration
{
    public function up()
    {
        Schema::create('temp_responses', function (Blueprint $table) {
            // bigIncrements ili increments; dump kaze BIGINT(20) UNSIGNED
            $table->bigIncrements('id');

            $table->integer('question_id')->unsigned()->nullable();
            $table->text('content');
            $table->timestamp('created_at')->useCurrent();

            // FK na temp_questions
            $table->foreign('question_id')
                  ->references('id')->on('temp_questions')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('temp_responses');
    }
}
