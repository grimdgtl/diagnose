<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseChat extends Model
{
    protected $fillable = ['user_id','status'];

    public function messages()        { return $this->hasMany(PurchaseMessage::class); }
    public function comparisonItems() { return $this->hasMany(ComparisonSetItem::class); }
}
