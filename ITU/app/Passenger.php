<?php
/*
 * ITU Project 2019/2020
 * Flight Search (Team xjurig00, xlinka01, xpukan01)
 *
 * Author of this file: Dominik Juriga (xjurig00)
 *
 * */
namespace App;

use Illuminate\Database\Eloquent\Model;

class Passenger extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'street', 'city', 'zip', 'state', 'user_id',
    ];

    protected $table = 'passengers';

    public function user() {
        return $this->belongsTo(User::class);
    }
}
