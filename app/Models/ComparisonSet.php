<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComparisonSet extends Model
{
    protected $fillable = ['user_id','title'];

    public function items() { return $this->hasMany(ComparisonSetItem::class); }
}
