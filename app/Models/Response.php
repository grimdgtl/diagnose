<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    use HasFactory;

    protected $table = 'responses';
    protected $primaryKey = 'id'; // int auto-increment
    public $timestamps = false;

    protected $fillable = [
        'question_id',
        'content',
        'created_at',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id', 'id');
    }
}
