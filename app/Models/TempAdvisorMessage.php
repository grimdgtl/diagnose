<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempAdvisorMessage extends Model
{
    use HasFactory;

    protected $table = 'temp_advisor_messages';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = ['chat_id', 'role', 'content', 'created_at'];

    public function tempAdvisorChat()
    {
        return $this->belongsTo(TempAdvisorChat::class, 'chat_id', 'id');
    }
}
