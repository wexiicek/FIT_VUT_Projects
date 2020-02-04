<?php

namespace App\Http\Controllers;

use App\Event;
use App\EventInstance;
use App\Room;
use App\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\RequestAttributeValueResolver;

class EventController extends Controller
{
    public function indexEvents()
    {
        return view('events.create');
    }

    public function indexInstances()
    {
        $events = Event::all();
        $rooms = Room::all();
        return view('eventInstances.create', ['events' => $events, 'rooms' => $rooms]);
    }

    public function createEvent(Request $req)
    {
        $cover = "";
        $destinationPath = 'storage/images/';
        if ($req->hasFile('event_cover')) {
            $name = "0_" .
                $req->input()['eventName'] .
                "-" .
                date('YmdHis') .
                "." .
                $req->file('event_cover')->getClientOriginalExtension();
            $req->file('event_cover')->move($destinationPath, $name);
            $cover = $name;
        }

        if ($req->hasFile('event_picture')) {
            $counter = 1;
            foreach ($req->file('event_picture') as $file) {

                $name = $counter .
                    "_" .
                    $req->input()['eventName'] .
                    "-" .
                    date('YmdHis') .
                    "." .
                    $file->getClientOriginalExtension();
                $file->move($destinationPath, $name);
                $images_data[] = $name;
                $counter++;
            }
        }


        if (isset($images_data)) {
            $event = Event::create([
                'name' => $req->input()['eventName'],
                'description' => $req->input()['description'],
                'type' => $req->input()['type'],
                'performers' => $req->input()['performers'],
                'cover' => $cover,
                'pictures' => json_encode($images_data),
            ]);
        } else {
            $event = Event::create([
                'name' => $req->input()['eventName'],
                'description' => $req->input()['description'],
                'type' => $req->input()['type'],
                'performers' => $req->input()['performers'],
                'cover' => $cover,
            ]);
        }
        return redirect('/event/' . $event->id);
    }

    public function createInstance(Request $req)
    {
        $instance = EventInstance::create([
            'date' => $req->input()['date'],
            'time' => substr($req->input()['time'], 0, 5),
            'price' => $req->input()['price'],
            'room_id' => $req->input()['room_id'],
            'event_id' => $req->input()['event_id'],
        ]);
        return redirect('/event/' . $instance->event_id);
    }

    public function showInstances()
    {
        $eventTypes = DB::table('event')
                        ->select('event.type')
                        ->join('event_instances', 'event.id', '=', 'event_instances.event_id')
                        ->distinct()
                        ->orderBy('event.type')
                        ->get();

        $instantiatedEvents = DB::table('event')
                                ->select('event.id', 'event.name')
                                ->join('event_instances', 'event.id', '=', 'event_instances.event_id')
                                ->distinct()
                                ->orderBy('event.name')
                                ->get();

        $events = EventInstance::paginate(5);
        $rooms = Room::all();

        return view("eventInstances.show", [
            'events' => $events,
            'rooms' => $rooms,
            'instantiatedEvents' => $instantiatedEvents,
            'eventTypes' => $eventTypes,
        ]);
    }

    public function showEvents()
    {
        $evs = Event::all();
        $events = [];
        foreach ($evs as $event) {
            $events[$event->name]['event'] = $event;
            $events[$event->name]['instances'] = EventInstance::where('event_id', $event->id)->limit(5)->get();
        }

        return view("events.list", ['events' => $events]);
    }

    public function showEvent($ev)
    {
        $event = Event::findOrFail($ev);
        $instances = EventInstance::where('event_id', $event->id)->get();
        return view("events.show", ['event' => $event, 'instances' => $instances]);
    }

    public function filter_results(Request $req)
    {

        $f = $req->input();
        if (array_key_exists('eventType', $f)) {
            if (!empty($f['eventType'])) {
                $a = DB::table('event_instances')
                       ->select('event_instances.id', 'event_instances.date', 'event_instances.time',
                           'event_instances.price', 'event_instances.room_id', 'event_instances.event_id', 'event.type')
                       ->join('event', 'event_instances.event_id', '=', 'event.id')
                       ->where('event.type', "=", $f['eventType'])
                       ->get();
            } else {
                $a = EventInstance::all();
            }
        } else {
            $a = EventInstance::all();
        }


        if (array_key_exists('r', $f)) {
            $a = $a->whereIn('room_id', $f['r']);
        }
        if (array_key_exists('f', $f)) {
            $a = $a->where('price', ">=", empty($f['f']) ? 0 : $f['f']);
        }
        if (array_key_exists("t", $f)) {
            $a = $a->where('price', "<=", empty($f['t']) ? 999999 : $f['t']);
        }

        if (array_key_exists('sort', $f)) {
            switch ($f['sort']) {
                case 'pa':
                    $a = $a->sortBy('price');
                    break;
                case 'pd':
                    $a = $a->sortByDesc('price');
                    break;
                case 'da':
                    $a = $a->sortBy('date');
                    break;
                case 'dd':
                    $a = $a->sortByDesc('date');
                    break;
                case 'ta':
                    $a = $a->sortBy('time');
                    break;
                case 'td':
                    $a = $a->sortByDesc('time');
                    break;
                default:
                    $a = $a->sortBy('date');
                    break;
            }
        }

        if (array_key_exists('eventName', $f)) {
            if (!empty($f['eventName'])) {
                $a = $a->whereIn('event_id', $f['eventName']);
            }
        }

        if (array_key_exists('date', $f)) {
            if (!empty($f['date'])) {
                $a = $a->whereIn('date', $f['date']);
            }
        }
        $events = $a;

        $rooms = Room::all();
        $instantiatedEvents = DB::table('event')
                                ->select('event.id', 'event.name')
                                ->join('event_instances', 'event.id', '=', 'event_instances.event_id')
                                ->distinct()
                                ->orderBy('event.name')
                                ->get();
        $eventTypes = DB::table('event')
                        ->select('event.type')
                        ->join('event_instances', 'event.id', '=', 'event_instances.event_id')
                        ->distinct()
                        ->orderBy('event.type')
                        ->get();


        return view('eventInstances.show', [
            'events' => $events,
            'rooms' => $rooms,
            'instantiatedEvents' => $instantiatedEvents,
            'eventTypes' => $eventTypes,
        ]);
    }

    public function getEvent($id, Request $req)
    {
        return json_encode(Event::find($id));
    }

    public function deleteEvent($id, Request $req)
    {
        $event = Event::find($id);
        $instances = EventInstance::where('event_id', $id)->get();
        foreach ($instances as $instance) {
            Ticket::where('eventInstance_id', $instance->id)->delete();
            $instance->delete();
        }
        $this->deleteCover($event->cover);
        $this->deletePictures($event->pictures);
        $event->delete();
        return redirect(route('manage_events'));
        //return json_encode($event->delete() ? ['deleted' => true] : ['deleted' => false]);
    }

    public function updateEvent($id, Request $req)
    {
        $event = Event::find($id);
        $event->name = $req->input('name') ?? $event->name;
        $event->description = $req->input('description') ?? $event->description;
        $event->type = $req->input('type') ?? $event->type;
        $event->performers = $req->input('performers') ?? $event->performers;

        if (array_key_exists('event_cover_del', $req->input())) {
            $this->deleteCover($event->cover);
            $event->cover = null;
        }

        if (array_key_exists('event_picture_del', $req->input())) {
            $this->deletePictures($event->pictures);
            $event->pictures = null;
        }

        $destinationPath = 'storage/images/';
        if ($req->hasFile('event_cover')) {
            $this->deleteCover($event->cover);
            $name = "0_" .
                $event->name .
                "-" .
                date('YmdHis') .
                "." .
                $req->file('event_cover')->getClientOriginalExtension();
            $req->file('event_cover')->move($destinationPath, $name);
            $event->cover = $name;
        }

        if ($req->hasFile('event_picture')) {
            $this->deletePictures($event->pictures);

            $counter = 1;
            foreach ($req->file('event_picture') as $file) {
                $name = $counter .
                    "_" .
                    $event->name .
                    "-" .
                    date('YmdHis') .
                    "." .
                    $file->getClientOriginalExtension();
                $file->move($destinationPath, $name);
                $images_data[] = $name;
                $counter++;
            }
            $event->pictures = $images_data;
        }

        $event->save();
        return redirect(route('manage_events'));
    }

    public function getInstance($id, Request $req)
    {
        return json_encode(EventInstance::find($id));
    }

    public function deleteInstance($id, Request $req)
    {
        Ticket::where('eventInstance_id', $id)->delete();
        EventInstance::find($id)->delete();
        return redirect(route('manage_instances'));
        //return EventInstance::find($id)->delete() ? ['deleted' => true] : ['deleted' => false];
    }

    public function deleteCover($picture)
    {
        if (!empty($picture)) {
            if (is_file('storage/images/' . $picture)) {
                unlink('storage/images/' . $picture);
            }
        }
    }

    public function deletePictures($pictures)
    {
        if (!empty($pictures)) {
            foreach (json_decode($pictures, true) as $file) {
                if (is_file('storage/images/' . $file)) {

                    unlink('storage/images/' . $file);
                }
            }
        }
    }

    public function updateInstance($id, Request $req) {
        $instance = EventInstance::find($id);
        $instance->date = $req->input('date') ?? $instance->date;
        $instance->time = substr($req->input('time'), 0, 5) ?? $instance->time;
        $instance->price = $req->input('price') ?? $instance->price;
        $instance->event_id = $req->input('event_id') ?? $instance->event_id;
        $instance->room_id = $req->input('room_id') ?? $instance->room_id;
        $instance->save();
        return redirect(route('manage_instances'));

    }
}
