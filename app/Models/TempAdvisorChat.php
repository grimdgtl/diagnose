<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempAdvisorChat extends Model
{
    use HasFactory;

    protected $table = 'temp_advisor_chats_guest';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = ['temp_id', 'status', 'created_at'];

    public function tempAdvisorUser()
    {
        return $this->belongsTo(TempAdvisorUser::class, 'temp_id', 'temp_id');
    }

    public function messages()
    {
        return $this->hasMany(TempAdvisorMessage::class, 'chat_id', 'id');
    }
}
