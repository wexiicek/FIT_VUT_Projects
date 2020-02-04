@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="card">
                    <div class="card-header">Tickets</div>
                    <div class="card-body">
                        <button type="button" class="btn btn-success col-md" data-toggle="modal"
                                data-target="#newEventInstanceModal">
                            Add New Event Instance
                        </button>
                        @if(count($instances) > 0)
                            <table>
                                <tr style="border-bottom: 1px solid #999">
                                    <th>ID</th>
                                    <th>Event</th>
                                    <th>Room</th>
                                    <th>Price</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Edit</th>
                                    <th>Delete</th>
                                </tr>
                                @foreach($instances as $instance)
                                    <tr>

                                        <th>{{$instance->id ?? ""}}</th>
                                        <th>{{App\Event::find($instance->event_id)->name ?? ""}}</th>
                                        <th>{{App\Room::find($instance->room_id)->name ?? ""}}</th>
                                        <th>{{$instance->price ?? ""}}</th>
                                        <th>{{$instance->date}}</th>
                                        <th>{{$instance->time}}</th>
                                        <th>
                                            <button type="button" class="btn btn-primary update_instance_admin"
                                                    data-toggle="modal"
                                                    data-target="#updateEventInstanceModal" value="{{$instance->id}}">
                                                Edit
                                            </button>
                                        </th>
                                        <th>
                                            <button type="button" class="btn btn-danger delete_instance_admin"
                                                    data-toggle="modal"
                                                    data-target="#deleteInstanceModal" value="{{$instance->id}}">Delete
                                            </button>
                                        </th>
                                    </tr>
                                @endforeach

                            </table>

                            <div class="col-md" style="margin-top: 10px">
                                <div class="justify-content-center row">
                                    {{$instances->links()}}
                                </div>
                            </div>
                        @else
                            Sorry, there are no instances at the moment
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Update Instance Modal -->
        <div class="modal fade" id="updateEventInstanceModal" tabindex="-1" role="dialog"
             aria-labelledby="updateEventInstanceModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateEventInstanceModalLabel">Update Instance <span id="update_event_placeholder"></span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="{{route('update_instance', 'placeholder')}}" id="update_instance_form">
                            @csrf

                            <div class="form-group row justify-content-center">
                                <div class="col-md-6">
                                    <label for="event_id">Event</label>
                                    <select class="form-control" name="event_id" id="event_id">
                                        @foreach($events as $event)
                                            <option value="{{$event->id}}">
                                                {{$event->name}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row justify-content-center">
                                <div class="col-md-6">
                                    <label for="room_id">Room</label>
                                    <select class="form-control" name="room_id" id="room_id">
                                        @foreach($rooms as $room)
                                            <option value="{{$room->id}}">
                                                {{$room->name}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row justify-content-center">
                                <div class="col-md-6">
                                    <label for="datepicker">Date</label>
                                    <input id="datepicker" type="text"
                                           class="form-control @error('date') is-invalid @enderror"
                                           name="date" value="{{ old('date') }}"
                                           placeholder="Select Date" required readonly>
                                    @error('date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row justify-content-center">
                                <div class="col-md-6">
                                    <label for="time">Time</label>
                                    <input id="time" type="time"
                                           class="form-control @error('time') is-invalid @enderror"
                                           name="time" value="{{ old('time') }}" placeholder="Time"
                                           required autocomplete="time" autofocus>

                                    @error('time')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row justify-content-center">
                                <div class="col-md-6">
                                    <label for="price">Price</label>
                                    <input id="price" type="number"
                                           class="form-control @error('price') is-invalid @enderror"
                                           name="price" value="{{ old('price') }}" placeholder="Price"
                                           required autocomplete="price" autofocus>

                                    @error('price')
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
                                <button type="submit" class="btn btn-success">Update Event Instance
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- New Event Instance Modal -->
        <div class="modal fade" id="newEventInstanceModal" tabindex="-1" role="dialog"
             aria-labelledby="newEventInstanceModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="newEventInstanceModalLabel">New Event Instance</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="{{route('create_event_instance_post')}}">
                            @csrf

                            <div class="form-group row justify-content-center">
                                <div class="col-md-6">
                                    <label for="event_id">Event</label>
                                    <select class="form-control" name="event_id" id="event_id">
                                        @foreach($events as $event)
                                            <option value="{{$event->id}}">
                                                {{$event->name}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row justify-content-center">
                                <div class="col-md-6">
                                    <label for="room_id">Room</label>
                                    <select class="form-control" name="room_id" id="room_id">
                                        @foreach($rooms as $room)
                                            <option value="{{$room->id}}">
                                                {{$room->name}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row justify-content-center">
                                <div class="col-md-6">
                                    <label for="datepicker">Date</label>
                                    <input id="datepicker_add" type="text"
                                           class="form-control @error('date') is-invalid @enderror"
                                           name="date" value="{{ old('date') }}"
                                           placeholder="Select Date" required readonly>
                                    @error('date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row justify-content-center">
                                <div class="col-md-6">
                                    <label for="time">Time</label>
                                    <input id="time" type="time"
                                           class="form-control @error('time') is-invalid @enderror"
                                           name="time" value="{{ old('time') }}" placeholder="Time"
                                           required autocomplete="time" autofocus>

                                    @error('time')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row justify-content-center">
                                <div class="col-md-6">
                                    <label for="price">Price</label>
                                    <input id="price" type="number"
                                           class="form-control @error('price') is-invalid @enderror"
                                           name="price" value="{{ old('price') }}" placeholder="Price"
                                           required autocomplete="price" autofocus>

                                    @error('price')
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
                                <button type="submit" class="btn btn-success">Add Event Instance
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <!-- Delete Account Modal -->
        <div class="modal fade" id="deleteInstanceModal" tabindex="-1" role="dialog"
             aria-labelledby="deleteEventInstanceLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteInstanceModalLabel">Delete Instance</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="{{ route('delete_instance', '__placeholder__') }}"
                              id="delete_instance_modal_form">
                            @csrf

                            <input type="checkbox" required> Do you really want to delete this instance? This will also delete any ticket for this instance.


                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    Close
                                </button>
                                <button type="submit" class="btn btn-primary">Delete Instance
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
    </div>
    </div>
@endsection
