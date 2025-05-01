<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('service_records')) {
            Schema::create('service_records', function (Blueprint $table) {
                $table->id();
                $table->foreignId('car_detail_id')
                      ->constrained('car_details')
                      ->cascadeOnDelete();
                $table->date('service_date');
                $table->text('description');
                $table->integer('mileage');
                $table->decimal('cost', 10, 2)->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('service_records');
    }
};
