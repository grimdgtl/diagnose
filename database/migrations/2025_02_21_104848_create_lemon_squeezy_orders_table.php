<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lemon_squeezy_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('billable_type');
            $table->string('billable_id');
            $table->string('lemon_squeezy_id');
            $table->string('customer_id');
            $table->char('identifier', 36);
            $table->string('product_id');
            $table->string('variant_id');
            $table->integer('order_number');
            $table->string('currency');
            $table->integer('subtotal');
            $table->integer('discount_total');
            $table->integer('tax');
            $table->integer('total');
            $table->string('tax_name')->nullable();
            $table->string('status');
            $table->string('receipt_url')->nullable();
            $table->boolean('refunded');
            $table->timestamp('refunded_at')->nullable();

            // Evo ispravljene kolone
            $table->timestamp('ordered_at')->nullable();
            // ili
            // $table->timestamp('ordered_at')->useCurrent();

            // U slučaju da želiš klasične timestamps:
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lemon_squeezy_orders');
    }
};
