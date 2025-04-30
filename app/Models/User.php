<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use LemonSqueezy\Laravel\Billable;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Filament\Models\Contracts\FilamentUser;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, Billable;

    /* -----------------------------------------------------------------
     |  Osnovne postavke
     |----------------------------------------------------------------- */
    protected $table = 'users';

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

    /* -----------------------------------------------------------------
     |  Mass-assignable polja
     |----------------------------------------------------------------- */
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

    /* -----------------------------------------------------------------
     |  Accessors / mutators
     |----------------------------------------------------------------- */
    /**
     * Virtual "name" atribut za Filament (i ostale delove sistema koji očekuju name).
     */
    public function getNameAttribute(): string
    {
        $full = trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
        return $full !== '' ? $full : $this->email;
    }

    /* -----------------------------------------------------------------
     |  Relacije
     |----------------------------------------------------------------- */
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

    /* -----------------------------------------------------------------
     |  Casts
     |----------------------------------------------------------------- */
    protected $casts = [
        'subscription_expires_at' => 'datetime',
    ];

    /* -----------------------------------------------------------------
     |  Scopes / pomoćne metode
     |----------------------------------------------------------------- */
    public function hasActiveSubscription()
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('ends_at')
                      ->orWhere('ends_at', '>', now());
            })
            ->exists();
    }

    /* -----------------------------------------------------------------
     |  Notifikacije
     |----------------------------------------------------------------- */
    public function sendPasswordResetNotification($token)
    {
        Mail::to($this->email)->send(new ResetPasswordMail($token, $this->email));
    }

    /* -----------------------------------------------------------------
     |  Auto-generisanje UUID primarnog ključa
     |----------------------------------------------------------------- */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (Model $model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /* -----------------------------------------------------------------
     |  FilamentUser interfejs
     |----------------------------------------------------------------- */
    public function getFilamentName(): string
    {
        return $this->name; // koristi accessor
    }

    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        return true; // zameni logikom role ako zatreba
    }
}
