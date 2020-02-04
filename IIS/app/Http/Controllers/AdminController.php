<?php

namespace App\Http\Controllers;

use App\Event;
use App\EventInstance;
use App\Models\User;
use App\Room;
use App\Ticket;
use App\Mail\SendMail;
use Carbon\Traits\Date;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    public function create()
    {
        return view('events.create');
    }

    public function index()
    {
        $users = User::paginate(10,['*'], 'users');
        $events = Event::all();
        $events = $events->sortBy('name');
        $rooms = Room::all();
        $rooms = $rooms->sortBy('name');
        $tickets = Ticket::paginate(10, ['*'], "tickets");
        $roles = ['admin', 'editor', 'cashier', 'user'];
        return view('admin.index', ['users' => $users, 'events' => $events, 'rooms' => $rooms, 'tickets' => $tickets]);
    }

    public function manageTickets()
    {
        if (@auth()->user()->role == 'cashier' && @auth()->user()->manages != null) {
            $tickets = Ticket::all();
            $manages = array_map('trim', explode(",", @auth()->user()->manages));
            $ticket_ids = [];
            foreach ($tickets as $ticket) {
                $instance = EventInstance::find($ticket->eventInstance_id);
                $room = Room::find($instance->room_id);
                if (in_array($room->name, $manages)) {
                    array_push($ticket_ids, $ticket->id);
                }
            }
            $tickets = Ticket::whereIn('id', $ticket_ids)->paginate(15);
        }
        else {
            $tickets = Ticket::paginate(15);
        }
        return view('cashier.index', ['tickets' => $tickets]);
    }

    public function manageUsers() {
        $users = User::orderBy('id', 'asc')->paginate(10);
        return view('admin.manage_users', ['users' => $users]);
    }

    public function manageRooms() {
        $rooms = Room::paginate(10);
        return view('admin.manage_rooms', ['rooms' => $rooms]);
    }

    public function manageEvents() {
        $events = Event::paginate(10);
        return view('admin.manage_events', ['events' => $events]);
    }

    public function manageInstances() {
        $instances = EventInstance::paginate(10);
        $events = Event::all();
        $rooms = Room::all();
        return view('admin.manage_instances', ['instances' => $instances, 'events' => $events, 'rooms' => $rooms]);
    }

    public function confirmTicket(Request $req)
    {
        $ticket = Ticket::findOrFail($req['ticket_id']);
        $ticket->paid = true;
        $ticket->confirmed_at = now();
        $ticket->save();

        $data = array(
            'price'      =>  $ticket->price,
            'ticket_amount' => $ticket->ticket_amount,
            'seats' => $ticket->seats,
            'eventInstance_id' => $ticket->eventInstance_id,
            'flag' => "confirmation"
        );

        Mail::to($ticket->email)->send(new SendMail($data));

        return response('confirmed', 200);
    }

    protected function user_create(Request $req)
    {
        User::create([
            'firstName' => $req->input('firstName') ?? "",
            'lastName' => $req->input('lastName') ?? "",
            'username' => $req->input('username'),
            'email' => $req->input('email'),
            'phoneNumber' => $req->input('phoneNumber') ?? "",
            'role' => $req->input('role') ?? "user",
            'password' => bcrypt($req->input('password')),
        ]);
        return redirect(route('manage_users'));
    }


}
