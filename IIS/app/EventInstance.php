<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventInstance extends Model
{
    protected $table = 'event_instances';

    protected $fillable = ['date', 'time', 'price', 'event_id', 'room_id'];

    public function rooms() {
        return $this->hasOne('App\Room');
    }

    public function events() {
        return $this->hasOne('App\Event');
    }

    public function tickets() {
        return $this->hasMany('App\Ticket');
    }
}
