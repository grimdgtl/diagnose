<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $table = 'lemon_squeezy_orders';   // eksplicitno ime tabele
    public $timestamps = true;                   // migracija ima timestamps

    protected $fillable = [
        'billable_type',
        'billable_id',
        'lemon_squeezy_id',
        'customer_id',
        'identifier',
        'product_id',
        'variant_id',
        'order_number',
        'currency',
        'subtotal',
        'discount_total',
        'tax',
        'total',
        'tax_name',
        'status',
        'receipt_url',
        'refunded',
        'refunded_at',
        'ordered_at',
    ];

    /* ===== Relacija ka User-u (pretpostavljam billable_type = App\\Models\\User) ===== */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'billable_id', 'id');
    }

    /* ===== Pomoćni accessor za cenu u EUR ===== */
    public function getTotalEurAttribute(): float
    {
        return $this->total / 100;   // Lemon Squeezy vraća u centima
    }
}
