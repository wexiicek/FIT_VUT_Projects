<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = "tickets";

    protected $fillable = ['price', 'ticket_amount', 'seats', 'id', 'eventInstance_id', 'user_id', 'email'];
    public function eventInstances() {
        return $this->hasOne('App\EventInstance');
    }
}
