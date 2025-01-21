<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatsTable extends Migration
{
    public function up()
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->increments('id'); // int(11) AUTO_INCREMENT
            $table->string('user_id', 255);
            $table->string('session_id', 255)->nullable();
            // U SQL dump-u je status enum('open', 'closed'), ovde možemo iskoristiti enum ili string
            $table->enum('status', ['open','closed'])->default('open');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('closed_at')->nullable();

            // Foreign key prema users(id)
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('chats');
    }
}
