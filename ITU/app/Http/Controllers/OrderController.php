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

class OrderController extends Controller
{
    public function book(Request $r)
    {
        $data = json_decode($r->input('flight_data'));
        $pas_count = $r->input('pas_count');

        $passengers = auth()->user()->passengers ?? array();

        return view('order.book', ['flight' => $data, 'pas_count' => $pas_count, 'passengers' => $passengers]);
    }

    public function thanks() {
        return view('order.thanks');
    }
}
