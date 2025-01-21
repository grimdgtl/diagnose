<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarDetail extends Model
{
    use HasFactory;

    protected $table = 'car_details';
    protected $primaryKey = 'id'; // int auto-increment
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'brand',
        'model',
        'year',
        'fuel_type',
        'engine_capacity',
        'engine_power',
        'transmission',
        'created_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
