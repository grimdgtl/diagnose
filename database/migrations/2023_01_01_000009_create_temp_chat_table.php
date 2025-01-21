<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTempChatTable extends Migration
{
    public function up()
    {
        Schema::create('temp_chat', function (Blueprint $table) {
            $table->increments('id');
            $table->string('temp_id', 255);
            $table->string('status', 255)->default('open');

            // Nema klasičan created_at u originalnom dump-u, ali dodajemo ako hoćemo
            // $table->timestamp('created_at')->useCurrent();

            // Foreign key
            $table->foreign('temp_id')
                  ->references('temp_id')->on('temp_users')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('temp_chat');
    }
}
