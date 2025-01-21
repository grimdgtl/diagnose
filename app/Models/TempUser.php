<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempUser extends Model
{
    use HasFactory;

    protected $table = 'temp_users';
    protected $primaryKey = 'temp_id'; // varchar(255)
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'temp_id',
        'created_at',
    ];

    // Relacije
    // S obzirom na to da je temp_car_details takođe PRIMARY KEY = temp_id,
    // to je uglavnom 1:1 veza (jedan TempUser -> jedan TempCarDetail).
    public function tempCarDetail()
    {
        return $this->hasOne(TempCarDetail::class, 'temp_id', 'temp_id');
    }

    public function tempQuestions()
    {
        return $this->hasMany(TempQuestion::class, 'temp_id', 'temp_id');
    }

    public function tempChats()
    {
        return $this->hasMany(TempChat::class, 'temp_id', 'temp_id');
    }
}
