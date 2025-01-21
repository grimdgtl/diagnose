<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempCarDetail extends Model
{
    use HasFactory;

    protected $table = 'temp_car_details';
    protected $primaryKey = 'temp_id'; // varchar(255), PK
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'temp_id',
        'brand',
        'model',
        'year',
        'fuel_type',
        'engine_capacity',
        'engine_power',
        'transmission',
        'created_at',
    ];

    public function tempUser()
    {
        return $this->belongsTo(TempUser::class, 'temp_id', 'temp_id');
    }
}
