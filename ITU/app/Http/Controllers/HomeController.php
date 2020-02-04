<?php
/*
 * ITU Project 2019/2020
 * Flight Search (Team xjurig00, xlinka01, xpukan01)
 *
 * Author of this file: Marian Pukancik (xpukan01)
 *
 * */
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $date = date( "d/m/Y", strtotime(now()));
        $s_value = auth()->user()->preferred_airport ?? "";
        return view('home', ['flights' => array(), 'date' => $date, 'search_value' => $s_value]);
    }
}
