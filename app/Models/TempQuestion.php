<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempQuestion extends Model
{
    use HasFactory;

    protected $table = 'temp_questions';
    protected $primaryKey = 'id'; // int auto-increment
    public $timestamps = false;

    protected $fillable = [
        'temp_id',
        'issueDescription',
        'diagnose',
        'indicatorLight',
        'created_at',
    ];

    public function tempUser()
    {
        return $this->belongsTo(TempUser::class, 'temp_id', 'temp_id');
    }

    public function tempResponses()
    {
        return $this->hasMany(TempResponse::class, 'question_id', 'id');
    }
}
