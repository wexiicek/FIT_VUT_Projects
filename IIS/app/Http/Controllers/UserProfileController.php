<?php

namespace App\Http\Controllers;

use App\EventInstance;
use App\Models\User;
use App\Ticket;
use Illuminate\Http\Request;
use stdClass;

use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;
use PharIo\Manifest\Email;

class UserProfileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index($username)
    {
        $user = User::where('username', $username);
        return view('user.profile', compact('user'));
    }

    public function show($username)
    {
        $user = User::where('username', $username)->first();
        if (@auth()->user()->id != $user->id) {
            abort(401);
        }
        $tickets = Ticket::where('user_id', $user->id)->get();
        return view('user.profile', ['user' => $user, 'tickets' => $tickets]);
    }

    public function update($username, Request $req)
    {
        $user = User::where('username', $username)->first();
        $user->firstName = $req->input('firstName');
        $user->lastName = $req->input('lastName');
        if ($req->input('user_role')) {
            $user->role = $req->input('user_role');
            if ($user->role == "cashier") {
                $user->manages = $req->input('manages') ?? "";
            } else {
                $user->manages = null;
            }
        }
        $user->phoneNumber = $req->input('phoneNumber');
        $user->save();

        $data = array(
            'firstName' => $user->firstName,
            'lastName' => $user->lastName,
            'userRole' => $user->role,
            'phoneNumber' => $user->phoneNumber,
            'username' => $user->username,
            'flag' => "updateUser"
        );

        Mail::to($user->email)->send(new SendMail($data));

        if (@auth()->user()->role == 'admin') {
            return redirect(route('manage_users'));
        }
        return (redirect('user/' . $user->username));
    }

    public function returnUser($username)
    {
        $user = User::where('username', $username)->first();
        return $user != null ? response(json_encode($user), 200) : response("No user", 404);
    }

    public function deleteUser($username)
    {
        $user = User::where('username', $username)->first();
        $tickets = Ticket::where('user_id', $user->id)->get();
        foreach ($tickets as $ticket) {
            $instance = EventInstance::find($ticket->eventInstance_id);
            if ($ticket->seats != null) {
                $ticket_seats = array_values(get_object_vars(json_decode($ticket->seats, false))['seats']);
                $instance_seats = json_decode($instance->seats);
                foreach ($ticket_seats as $seat) {
                    if (property_exists($instance_seats, $seat)) {
                        unset($instance_seats->$seat);

                    }
                }
                $instance->seats = json_encode($instance_seats);
            }
            $instance->save();
        }

        $data = array(
            'username' => $user->username,
            'flag' => "deleteUser"
        );

        $user->delete();

        Mail::to($user->email)->send(new SendMail($data));

        if (@auth()->user()->role == 'admin') {
            return redirect(route('manage_users'));
        }
        return redirect(route('home'));
    }

    public function cancelTicket($username, Request $req)
    {
        $ticket = Ticket::findOrFail($req['ticket_id']);
        if (auth()->id() == $ticket->user_id) {

            $data = array(
                'id' => $ticket->id,
                'flag' => "cancelTicket"
            );

            $mail = $ticket->email;

            $ticket_seats = array_values(get_object_vars(json_decode($ticket->seats, false))['seats']);
            $instance = EventInstance::find($ticket->eventInstance_id);
            $instance_seats = json_decode($instance->seats);
            foreach ($ticket_seats as $seat) {
                if (property_exists($instance_seats, $seat)) {
                    unset($instance_seats->$seat);
                }
            }
            $instance->seats = json_encode($instance_seats);
            $instance->save();
            $ticket->canceled = true;
            $ticket->save();

            Mail::to($mail)->send(new SendMail($data));

            return response("OK", 200);
        }
        return abort(401);
    }
}
