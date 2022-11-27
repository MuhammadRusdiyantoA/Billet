<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    public $timestamps = false;

    public function event() {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function buyer() {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function transaction() {
        return $this->belongsTo(Transaction::class, 'trans_id');
    }
}
