<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    public $timestamps = false;

    public function host() {
        return $this->belongsTo(User::class, 'host_id');
    }

    public function tickets() {
        return $this->hasMany(Ticket::class, 'event_id');
    }
}
