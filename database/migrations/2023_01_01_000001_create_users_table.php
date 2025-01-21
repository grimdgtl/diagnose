<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            // U dump-u je id varchar(255), primary key
            $table->string('id', 255)->primary();
            $table->string('first_name', 255)->nullable();
            $table->string('last_name', 255)->nullable();
            $table->string('email', 255)->unique()->nullable();
            $table->string('password', 255)->nullable();
            $table->string('city', 255)->nullable();
            $table->string('country', 255)->nullable();
            $table->integer('num_of_questions_left')->default(0);
            $table->string('verification_token', 255)->nullable();
            $table->boolean('verified')->default(false);
            // Polja za reset lozinke / pamćenje
            $table->string('reset_token', 255)->nullable();
            $table->dateTime('reset_requested_at')->nullable();
            $table->string('remember_token', 255)->nullable();

            // created_at kao TIMESTAMP sa default CURRENT_TIMESTAMP
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
