<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $guarded = [
        'id'
    ];

    public $timestamps = false;

    public function role() {
        return $this->belongsTo(Role::class, 'role');
    }

    public function events() {
        return $this->hasMany(Event::class, 'host_id');
    }

    public function tickets() {
        return $this->hasMany(Ticket::class, 'buyer_id');
    }

    public function send() {
        return $this->hasMany(Transaction::class, 'sender_id');
    }

    public function receive() {
        return $this->hasMany(Transaction::class, 'receiver_id');
    }
}
