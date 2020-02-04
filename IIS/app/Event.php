<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'event';

    protected $fillable = ['name', 'description', 'type', 'performers', 'cover', 'pictures'];

    public function event_instances() {
        return $this->hasMany('App\EventInstances');
    }
}
