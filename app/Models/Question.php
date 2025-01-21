<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $table = 'questions';
    protected $primaryKey = 'id'; // int auto-increment
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'issueDescription',
        'diagnose',
        'indicatorLight',
        'chat_id',
        'created_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function chat()
    {
        return $this->belongsTo(Chat::class, 'chat_id', 'id');
    }

    public function responses()
    {
        return $this->hasMany(Response::class, 'question_id', 'id');
    }
}
