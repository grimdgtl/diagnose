<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempResponse extends Model
{
    use HasFactory;

    protected $table = 'temp_responses';
    protected $primaryKey = 'id'; // bigint auto-increment
    public $timestamps = false;

    protected $fillable = [
        'question_id',
        'content',
        'created_at',
    ];

    public function tempQuestion()
    {
        return $this->belongsTo(TempQuestion::class, 'question_id', 'id');
    }
}
