<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempChat extends Model
{
    use HasFactory;

    protected $table = 'temp_chat'; // napomena: ime tabele je "temp_chat"
    protected $primaryKey = 'id';   // int auto-increment
    public $timestamps = false;

    protected $fillable = [
        'temp_id',
        'status',
        // 'created_at' (ako želimo da dodamo ručno, ali ga nema u dump-u)
    ];

    public function tempUser()
    {
        return $this->belongsTo(TempUser::class, 'temp_id', 'temp_id');
    }
}
