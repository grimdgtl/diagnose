<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTempAdvisorMessagesTable extends Migration
{
    public function up()
    {
        Schema::create('temp_advisor_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('chat_id')->nullable();
            $table->enum('role', ['user', 'assistant']);
            $table->longText('content');
            $table->timestamp('created_at')->useCurrent();

            // Ispravljen strani ključ – referencira tabelu temp_advisor_chats_guest
            $table->foreign('chat_id')
                  ->references('id')->on('temp_advisor_chats_guest')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('temp_advisor_messages');
    }
}
