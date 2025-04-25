<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('comparison_set_items', function (Blueprint $t) {
            $t->engine = 'InnoDB';      // ← eksplicitno, zbog FK‑ova

            $t->id();

            // Laravel sada SAM kreira unsignedBigInteger + index + FK
            $t->foreignId('comparison_set_id')
              ->constrained('comparison_sets')
              ->cascadeOnDelete();

            $t->foreignId('purchase_chat_id')
              ->constrained('purchase_chats')
              ->cascadeOnDelete();

            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comparison_set_items');
    }
};
