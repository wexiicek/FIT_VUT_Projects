<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = 'tickets';
    protected $fillable = ['from', 'to', 'date', 'time', 'price'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
