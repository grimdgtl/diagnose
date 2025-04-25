<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('purchase_chats', function (Blueprint $t) {
            $t->id();
            $t->string('user_id');                         // FK → users.id
            $t->enum('status',['draft','active','archived'])->default('draft');
            $t->timestamps();

            $t->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
    public function down(): void { Schema::dropIfExists('purchase_chats'); }
};

