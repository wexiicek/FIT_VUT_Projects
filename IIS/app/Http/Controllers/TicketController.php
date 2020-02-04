<?php

namespace App\Http\Controllers;

use App\Event;
use App\EventInstance;
use App\Room;
use App\Ticket;
use Illuminate\Http\Request;
use PharIo\Manifest\Email;
use stdClass;
use App\Models\User;

use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;

class TicketController extends Controller
{

    public function index($id) {
        usleep(100000);
        $in = eventInstance::findOrFail($id);
        $ev = Event::findOrFail($in->event_id);
        $room = Room::findOrFail($in->room_id);
        $seats = array_keys(get_object_vars(json_decode($in->seats) ?? new stdClass()));
        return view('ticket.create', ['instance' => $in, 'event' => $ev, 'room' => $room, 'seats' => $seats]);
    }



    public function purchaseTicket($id, Request $req) {
        if (@auth()->user()) {
            $email = @auth()->user()->email;
        }
        else {
            $email = $req->input('email');
        }
        $ticket = Ticket::create([
            'price' => $req->input('price'),
            'ticket_amount' => $req->input('ticket_amount'),
            'eventInstance_id' => $id ?? "",
            'email' => $email,
            'seats' => $req->input('ticket_seats'),
            'user_id' =>  $req->input('user_id'),
        ]);

        $data = array(
            'price'      =>  $ticket->price,
            'ticket_amount' => $ticket->ticket_amount,
            'seats' => $ticket->seats,
            'eventInstance_id' => $id,
            'flag' => "payment"
        );

        Mail::to($ticket->email)->send(new SendMail($data));

        if (@auth()->user()) {
            return redirect('user/'.@auth()->user()->username);
        }
        else {
            return redirect('/');
        }

    }
}
