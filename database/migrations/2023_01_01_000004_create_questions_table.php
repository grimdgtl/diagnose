<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTable extends Migration
{
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id', 255)->nullable();
            $table->text('issueDescription')->nullable();
            $table->text('diagnose')->nullable();
            $table->text('indicatorLight')->nullable();
            $table->timestamp('created_at')->useCurrent();
            // Veza ka chat-u
            $table->integer('chat_id')->unsigned()->nullable();

            // Foreign keys
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('set null');

            $table->foreign('chat_id')
                  ->references('id')->on('chats')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('questions');
    }
}
