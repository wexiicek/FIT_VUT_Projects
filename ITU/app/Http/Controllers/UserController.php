<?php
/*
 * ITU Project 2019/2020
 * Flight Search (Team xjurig00, xlinka01, xpukan01)
 *
 * Author of this file: Adam Linka (xlinka01)
 *
 * */
namespace App\Http\Controllers;

use App\Passenger;
use App\Ticket;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index($username)
    {
        if (auth()->user() == null) {
            abort(404);
        }

        if (Auth::user()->username != $username) {
            abort(401);
        }

        $passengers = Passenger::where('user_id', Auth::user()->id)->get();

        $tickets = Ticket::paginate(5);

        return view('profile.index', ['user' => auth()->user(), 'passengers' => $passengers, 'tickets' => $tickets]);
    }

    public function edit($username, Request $r)
    {
        $user = auth()->user();
        if ($user->username != $username) {
            abort(401);
        }

        $user->name = $r->input('name') ?? $user->name;
        $user->email = $r->input('email') ?? $user->email;
        $user->phone_number = $r->input('phone_number') ?? $user->phone_number;
        $user->preferred_airport = $r->input('preferred_airport') ?? $user->preferred_airport;
        $user->save();
        return json_encode($user);
    }

    public function edit_address($username, Request $r)
    {
        $user = auth()->user();
        if ($user->username != $username) {
            abort(401);
        }

        $user->street = $r->input('street') ?? $user->street;
        $user->city = $r->input('city') ?? $user->city;
        $user->zip = $r->input('zip') ?? $user->zip;
        $user->state = $r->input('state') ?? $user->state;
        $user->save();
        return json_encode($user);
    }

    public function add_passenger($username, Request $r)
    {
        if (auth()->user()->username != $username) {
            abort(401);
        }

        Passenger::create([
            'name' => $r->input('passenger_name') ?? "",
            'street' => $r->input('passenger_street') ?? "",
            'city' => $r->input('passenger_city') ?? "",
            'zip' => $r->input('passenger_zip') ?? "",
            'state' => $r->input('passenger_state') ?? "",
            'user_id' => auth()->user()->id,
        ]);
        return redirect(route('profile', $username));
    }

    public function get_passenger(Request $r) {
        return json_encode(Passenger::find(json_decode($r->input('passenger'))));
    }
}
