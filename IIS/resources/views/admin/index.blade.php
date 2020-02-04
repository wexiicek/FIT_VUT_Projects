@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Admin Panel</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                    @endif

                    <!-- Button trigger modal -->

                        <button type="button" class="btn btn-success col-md" data-toggle="modal"
                                data-target="#newRoomModal">
                            Add New Room
                        </button>

                        <button type="button" class="btn btn-success col-md" data-toggle="modal"
                                data-target="#newEventModal">
                            Add New Event
                        </button>

                        <button type="button" class="btn btn-success col-md" data-toggle="modal"
                                data-target="#newEventInstanceModal">
                            Add New Event Instance
                        </button>

                        <a href="{{route('manage_tickets')}}" class="btn btn-primary col-md">Manage Tickets</a>

                        <a href="{{route('manage_users')}}" class="btn btn-primary col-md">Manage Users</a>

                        <a href="{{route('manage_rooms')}}" class="btn btn-primary col-md">Manage Rooms</a>

                        <a href="{{route('manage_events')}}" class="btn btn-primary col-md">Manage Events</a>

                        <a href="{{route('manage_instances')}}" class="btn btn-primary col-md">Manage Instances</a>


                        <!-- New Room Modal -->
                        <div class="modal fade" id="newRoomModal" tabindex="-1" role="dialog"
                             aria-labelledby="newRoomModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="newRoomModalLabel">New Room</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="post" action="{{ route('create_room_post') }}">
                                            @csrf
                                            <div class="form-group row justify-content-center">
                                                <div class="col-md-6">
                                                    <label for="name" class="control-label">Name</label>
                                                    <input id="name" type="text"
                                                           class="form-control @error('name') is-invalid @enderror"
                                                           name="name"
                                                           value="{{ old('name') }}" placeholder="Room Name" required
                                                           autocomplete="name" autofocus>

                                                    @error('name')
                                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group row justify-content-center">
                                                <div class="col-md-6">
                                                    <label for="rows" class="control-label">Rows</label>
                                                    <input id="rows" type="number"
                                                           class="form-control @error('rows') is-invalid @enderror"
                                                           name="rows" value="{{ old('rows') }}"
                                                           placeholder="Number Of Rows" required autocomplete="rows"
                                                           autofocus min="1" max="20">

                                                    @error('rows')
                                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group row justify-content-center">
                                                <div class="col-md-6">
                                                    <label for="columns" class="control-label">Columns</label>
                                                    <input id="columns" type="number"
                                                           class="form-control @error('columns') is-invalid @enderror"
                                                           name="columns" value="{{ old('columns') }}"
                                                           placeholder="Number Of Columns" required
                                                           autocomplete="columns"
                                                           autofocus min="1" max="20">

                                                    @error('columns')
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
                                                <button type="submit" class="btn btn-success">Add Room</button>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- New Event Modal -->
                        <div class="modal fade" id="newEventModal" tabindex="-1" role="dialog"
                             aria-labelledby="newEventModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="newEventModalLabel">New Event</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="post" action="{{route('create_event_post')}}"
                                              enctype="multipart/form-data">
                                            @csrf

                                            <div class="form-group row justify-content-center">
                                                <div class="col-md-6">
                                                    <label for="eventNname" class="control-label">Name</label>
                                                    <input id="eventName" type="text"
                                                           class="form-control @error('eventName') is-invalid @enderror"
                                                           name="eventName"
                                                           value="{{ old('eventName') }}" placeholder="Event Name"
                                                           required
                                                           autocomplete="name" autofocus>

                                                    @error('eventName')
                                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group row justify-content-center">
                                                <div class="col-md-6">
                                                    <label for="description">Description</label>
                                                    <textarea id="description" type="text"
                                                              class="form-control @error('description') is-invalid @enderror"
                                                              name="description" value="{{ old('description') }}"
                                                              placeholder="Event Description"
                                                              autocomplete="description"
                                                              autofocus></textarea>

                                                    @error('description')
                                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group row justify-content-center">
                                                <div class="col-md-6">
                                                    <label for="performers">Performers</label>
                                                    <input id="performers" type="text"
                                                           class="form-control @error('performers') is-invalid @enderror"
                                                           name="performers" value="{{ old('performers') }}"
                                                           placeholder="Event Performers"
                                                           autocomplete="Performers"
                                                           autofocus>

                                                    @error('performers')
                                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group row justify-content-center">
                                                <div class="col-md-6">
                                                    <label for="type">Type</label>
                                                    <select class="form-control" id="type" name="type">
                                                        <option value="Lecture">
                                                            Lecture
                                                        </option>

                                                        <option value="Movie">
                                                            Movie
                                                        </option>

                                                        <option value="Drama">
                                                            Drama
                                                        </option>
                                                        <option value="Other" selected>
                                                            Other
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group row justify-content-center">
                                                <div class="col-md-6">
                                                    <label for="event_cover">Cover Picture</label>
                                                    <div class="input-group">
                                                        <div class="custom-file">
                                                            <input type="file" class="custom-file-input"
                                                                   id="inputGroupFile01"
                                                                   aria-describedby="inputGroupFileAddon01"
                                                                   name="event_cover" id="event_cover" accept="image/*">
                                                            <label class="custom-file-label" for="inputGroupFile01">Choose
                                                                file</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group row justify-content-center">
                                                <div class="col-md-6">
                                                    <label for="event_picture">Pictures</label>
                                                    <div class="input-group">
                                                        <div class="custom-file">
                                                            <input type="file" class="custom-file-input"
                                                                   id="inputGroupFile02"
                                                                   aria-describedby="inputGroupFileAddon01"
                                                                   name="event_picture[]" id="event_picture"
                                                                   accept="image/*" multiple>
                                                            <label class="custom-file-label" for="inputGroupFile01">Choose
                                                                file</label>
                                                        </div>


                                                    </div>
                                                </div>
                                            </div>


                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                    Close
                                                </button>
                                                <button type="submit" class="btn btn-success">Add Event</button>
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
                                                    <label for="event_id" class="control-label">Event</label>
                                                    <select class="form-control" name="event_id" id="event_id" required>
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
                                                    <label for="room_id" class="control-label">Room</label>
                                                    <select class="form-control" name="room_id" id="room_id" required>
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
                                                    <label for="datepicker" class="control-label">Date</label>
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
                                                    <label for="time" class="control-label">Time</label>
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
                                                    <label for="price" class="control-label">Price</label>
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
                </div>
            </div>
        </div>
    </div>
@endsection
