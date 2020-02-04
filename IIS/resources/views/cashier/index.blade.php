@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Tickets
                        @if(Auth::user()->manages)
                            in rooms {{Auth::user()->manages}}
                        @endif
                    </div>
                    <div class="card-body">
                        @if(count($tickets) > 0)
                            <table>
                                <tr style="border-bottom: 1px solid #999">
                                    <th>ID</th>
                                    <th>Room</th>
                                    <th>Username</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Date</th>
                                    <th>Price</th>
                                    <th>Paid</th>
                                    <th>Confirm</th>
                                </tr>
                                @foreach($tickets as $ticket)
                                    <tr>
                                        <th>{{$ticket->id}}</th>
                                        <th>{{App\Room::find(App\EventInstance::find($ticket->eventInstance_id)->room_id)->name}}</th>
                                        <th>{{App\Models\User::find($ticket->user_id)->username ?? '-'}}</th>
                                        <th>{{App\Models\User::find($ticket->user_id)->firstName ?? '-'}}</th>
                                        <th>{{App\Models\User::find($ticket->user_id)->lastName ?? '-'}}</th>
                                        <th>{{ \App\EventInstance::find($ticket->eventInstance_id)->date }} {{\App\EventInstance::find($ticket->eventInstance_id)->time}}</th>
                                        <th>{{$ticket->price}}</th>
                                        <th class="ticket_paid">{{$ticket->paid ? "Paid" : "Unpaid"}}</th>
                                        <th>
                                            <button
                                                class="btn {{$ticket->canceled || $ticket->paid ? "ticket_canceled" : "btn btn-primary ticket_c"}}"
                                                value="{{$ticket->id}}">{{ $ticket->canceled || $ticket->paid ? "Unavailable" : "Confirm"}}</button>
                                        </th>
                                    </tr>
                                @endforeach

                            </table>
                            <div class="justify-content-center row">
                            {{$tickets->links()}}
                        </div>
                        @else
                            Sorry, there are no tickets at the moment!
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
