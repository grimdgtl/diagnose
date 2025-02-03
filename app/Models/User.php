<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    // U tabeli "users" primarni ključ je string(255)
    protected $table = 'users';
    protected $primaryKey = 'id';
    public $incrementing = false;       // zato što je varchar, ne auto-increment
    protected $keyType = 'string';
    public $timestamps = false;         // imamo samo created_at kolonu, bez updated_at

    protected $fillable = [
        'id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'city',
        'country',
        'num_of_questions_left',
        'verification_token',
        'verified',
        'password_reset_tokens',
        'reset_requested_at',
        'remember_token',
        'created_at',
    ];

    // Relacije
    public function carDetails()
    {
        return $this->hasMany(CarDetail::class, 'user_id', 'id');
    }

    public function chats()
    {
        return $this->hasMany(Chat::class, 'user_id', 'id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'user_id', 'id');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'user_id', 'id');
    }
}
