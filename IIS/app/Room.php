<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $table = "room";

    protected $fillable = ['name', 'rows', 'columns'];

    public function events() {
        return $this->hasMany('App\EventInstance');
    }
}
