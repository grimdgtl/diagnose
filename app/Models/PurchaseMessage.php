<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseMessage extends Model
{
    protected $fillable = ['purchase_chat_id','role','content','token_usage'];

    public function chat() { return $this->belongsTo(PurchaseChat::class); }
}