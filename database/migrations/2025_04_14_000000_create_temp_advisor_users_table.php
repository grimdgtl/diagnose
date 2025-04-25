<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTempAdvisorUsersTable extends Migration
{
    public function up()
    {
        Schema::create('temp_advisor_users', function (Blueprint $table) {
            $table->string('temp_id', 255)->primary();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('temp_advisor_users');
    }
}
