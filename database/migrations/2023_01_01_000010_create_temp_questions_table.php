<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTempQuestionsTable extends Migration
{
    public function up()
    {
        Schema::create('temp_questions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('temp_id', 255)->nullable();
            $table->text('issueDescription')->nullable();
            $table->text('diagnose')->nullable();
            $table->text('indicatorLight')->nullable();
            $table->timestamp('created_at')->useCurrent();

            // Foreign key
            $table->foreign('temp_id')
                  ->references('temp_id')->on('temp_users')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('temp_questions');
    }
}
