<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComparisonSetItem extends Model
{
    protected $fillable = ['comparison_set_id','purchase_chat_id'];

    public function purchaseChat() { return $this->belongsTo(PurchaseChat::class); }
}
