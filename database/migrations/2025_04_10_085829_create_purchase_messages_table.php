<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('purchase_messages', function (Blueprint $t) {
            $t->id();
            $t->foreignId('purchase_chat_id')->constrained()->cascadeOnDelete();
            $t->enum('role',['user','assistant']);
            $t->longText('content');
            $t->integer('token_usage')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('purchase_messages'); }
};

