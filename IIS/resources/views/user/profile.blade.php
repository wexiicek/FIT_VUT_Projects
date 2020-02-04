@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <button type="button" class="btn btn-primary col-md" data-toggle="modal"
                        data-target="#updateUserInstanceModal">
                    Update User Data
                </button>
                <button type="button" class="btn btn-primary col-md" data-toggle="modal"
                        data-target="#deleteAccountModal">
                    Delete Account
                </button>

                <!-- Update Account Modal -->
                <div class="modal fade" id="updateUserInstanceModal" tabindex="-1" role="dialog"
                     aria-labelledby="updateUserInstanceModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="updateUserInstanceModalLabel">Update User Data</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="post" action="{{$user->username}}/update">
                                    @csrf

                                    <div class="form-group row justify-content-center">
                                        <div class="col-md-6">
                                            First Name
                                            <input id="firstName" type="text"
                                                   class="form-control @error('firstName') is-invalid @enderror"
                                                   name="firstName"
                                                   value="{{ $user->firstName }}" required
                                                   autocomplete="firstName" autofocus>

                                            @error('firstName')
                                            <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row justify-content-center">
                                        <div class="col-md-6">
                                            Last Name
                                            <input id="lastName" type="text"
                                                   class="form-control @error('lastName') is-invalid @enderror"
                                                   name="lastName"
                                                   value="{{ $user->lastName }}" required
                                                   autocomplete="lastName" autofocus>

                                            @error('lastName')
                                            <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row justify-content-center">
                                        <div class="col-md-6">
                                            Phone Number
                                            <input id="phoneNumber" type="text"
                                                   class="form-control @error('phoneNumber') is-invalid @enderror"
                                                   name="phoneNumber"
                                                   value="{{ $user->phoneNumber }}" required
                                                   autocomplete="phoneNumber" autofocus>

                                            @error('phoneNumber')
                                            <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                                            @enderror
                                        </div>
                                    </div>


                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                            Close
                                        </button>
                                        <button type="submit" class="btn btn-primary">Update User Data</button>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Delete Account Modal -->
                <div class="modal fade" id="deleteAccountModal" tabindex="-1" role="dialog"
                     aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteAccountModalLabel">Delete Account</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="post" action="{{$user->username}}/delete">
                                    @csrf

                                    <input type="checkbox" required> Do you really want to delete your account?


                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                            Close
                                        </button>
                                        <button type="submit" class="btn btn-primary">Delete Account</button>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="card">
                    <div class="card-header">
                        {{$user->firstName}} {{$user->lastName}}
                    </div>
                    <div class="card-body">

                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        Your Tickets
                    </div>
                    <div class="card-body">
                        @if(count($tickets) > 0)
                            <table>
                                <tr style="border-bottom: 1px solid #999">
                                    <th class="ticket_id" style="color: #999;">ID</th>
                                    <th class="ticket_first_name" style="color: #999;">Event</th>
                                    <th class="ticket_price" style="color: #999;">Price</th>
                                    <th class="ticket_paid" style="color: #999;">Paid</th>
                                    <th class="ticket_created" style="color: #999;">Date</th>
                                    <th style="color: #999;">Room</th>
                                    <th class="ticket_confirmed" style="color: #999;">Confirmed at</th>
                                    <th class="ticket_seats" style="color: #999;">Seats</th>
                                    <th class="ticket_cancel" style="color: #999;">Cancel</th>
                                </tr>
                                @foreach($tickets as $ticket)
                                    <tr>
                                        <th class="ticket_id">{{$ticket->id ?? ""}}</th>
                                        <th class="ticket_event_name">{{App\Event::find(App\EventInstance::find($ticket->eventInstance_id)->id)->name ?? ""}}</th>
                                        <th class="ticket_price">{{$ticket->price ?? ""}}</th>
                                        <th class="ticket_paid">{{$ticket->paid ? "Yes" : "No"}}</th>
                                        <th class="ticket_created">{{ App\EventInstance::find($ticket->eventInstance_id)->date}} {{App\EventInstance::find($ticket->eventInstance_id)->time ?? ""}}</th>
                                        <th>{{ App\Room::find(\App\EventInstance::find($ticket->eventInstance_id)->room_id)->name  ?? ""}}</th>
                                        <th class="ticket_confirmed">{{ $ticket->confirmed_at ? date('d.m.Y, H:i', strtotime($ticket->confirmed_at)) : ""}}</th>
                                        <th class="ticket_seats">
                                            @foreach ((json_decode($ticket->seats, true)['seats']) ?? [] as $seat)
                                                {{$seat }}
                                            @endforeach
                                        </th>
                                        <th>
                                            @if (!$ticket->paid)
                                                <button
                                                    class="{{$ticket->canceled? "ticket_canceled" : "btn btn-primary ticket_can"}}"
                                                    value="{{$ticket->id}}">{{$ticket->canceled? "Canceled" : "Cancel"}}</button>
                                            @endif
                                        </th>
                                    </tr>
                                @endforeach

                            </table>
                        @else
                            Sorry, there are no tickets at the moment!
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
