<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempAdvisorUser extends Model
{
    use HasFactory;

    protected $table = 'temp_advisor_users';
    protected $primaryKey = 'temp_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['temp_id', 'created_at'];

    public function vehicles()
    {
        return $this->hasMany(TempAdvisorVehicle::class, 'temp_id', 'temp_id');
    }

    public function chat()
    {
        return $this->hasOne(TempAdvisorChat::class, 'temp_id', 'temp_id');
    }
}
