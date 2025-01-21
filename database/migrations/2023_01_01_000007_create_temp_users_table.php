<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTempUsersTable extends Migration
{
    public function up()
    {
        Schema::create('temp_users', function (Blueprint $table) {
            // U dump-u je PRIMARY KEY (temp_id)
            $table->string('temp_id', 255)->primary();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('temp_users');
    }
}
