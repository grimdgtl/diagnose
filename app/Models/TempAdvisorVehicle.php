<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempAdvisorVehicle extends Model
{
    use HasFactory;

    protected $table = 'temp_advisor_vehicles';
    public $timestamps = false;

    protected $fillable = [
        'temp_id',
        'brand',
        'model',
        'year',
        'mileage',
        'engine_capacity',
        'engine_power',
        'fuel_type',
        'transmission',
        'created_at',
    ];

    public function tempAdvisorUser()
    {
        return $this->belongsTo(TempAdvisorUser::class, 'temp_id', 'temp_id');
    }
}
