<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTempAdvisorChatsGuestTable extends Migration
{
    public function up()
    {
        Schema::create('temp_advisor_chats_guest', function (Blueprint $table) {
            $table->increments('id');
            $table->string('temp_id', 255);
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('temp_id')->references('temp_id')->on('temp_advisor_users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('temp_advisor_chats_guest');
    }
}
