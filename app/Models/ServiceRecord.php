<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRecord extends Model
{
    protected $fillable = ['car_detail_id', 'service_date', 'description', 'mileage', 'cost', 'notes'];

    public function carDetail()
    {
        return $this->belongsTo(CarDetail::class, 'car_detail_id');
    }
}