<?php

namespace App\Http\Controllers;

use App\EventInstance;
use App\Room;
use App\Ticket;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function create(Request $req)
    {
        $room = Room::create([
            'name' => $req->input()['name'],
            'rows' => $req->input()['rows'],
            'columns' => $req->input()['columns'],
        ]);
        return redirect(route('manage_rooms'));
    }

    public function reserveSeat(Request $req)
    {
        $instance = EventInstance::findOrFail($req['instance_id']);
        if (json_decode($instance->seats, true) == null) {
            $seats = [];
        } else {
            $seats = json_decode($instance->seats, true);
        }

        if (isset($seats[$req['seat_id']])) {
            if ($seats[$req['seat_id']]['token'] == $req['token']) {
                unset($seats[$req['seat_id']]);
                $instance->seats = json_encode($seats);
                $instance->save();

                return response($instance->seats, 200);
            }
            return response("Seat is already taken.", 409);
        } else {
            $seats[$req['seat_id']] = ["token" => $req['token'], "timestamp" => $req['timestamp']];
        }

        $instance->seats = json_encode($seats);
        $instance->save();
        return response($instance->seats, 201);
    }

    public function cancelSeats($id, Request $req)
    {
        $instance = EventInstance::find($id);
        $instance_seats = json_decode($instance->seats);
        $ticket_seats = array_values(get_object_vars(json_decode($req['seats'], false))['seats']);
        foreach ($ticket_seats as $seat) {
            if (property_exists($instance_seats, $seat)) {
                unset($instance_seats->$seat);
            }
        }
        $instance->seats = json_encode($instance_seats);
        $instance->save();
        return response("helo", 200);
    }

    public function getRoom($id, Request $req)
    {
        return json_encode(Room::find($id));
    }

    public function updateRoom($id, Request $req)
    {
        $room = Room::find($id);
        $room->name = $req->input('name') ?? $room->name;
        $room->rows = $req->input('rows') ?? $room->rows;
        $room->columns = $req->input('columns') ?? $room->columns;

        $room->save();
        return redirect(route('manage_rooms'));
    }

    public function deleteRoom($id, Request $req)
    {
        $room = Room::find($id);
        $instances = EventInstance::where('room_id', $id)->get();
        foreach ($instances as $instance) {
            Ticket::where('eventInstance_id', $instance->id)->delete();
            $instance->delete();
        }
        $room->delete();
        return redirect(route('manage_rooms'));

    }
}
