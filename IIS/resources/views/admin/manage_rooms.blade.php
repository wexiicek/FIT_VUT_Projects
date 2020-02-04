@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">


                <div class="card">
                    <div class="card-header">Rooms</div>
                    <div class="card-body">
                        <button type="button" class="btn btn-success col-md" data-toggle="modal"
                                data-target="#newRoomModal">
                            Add New Room
                        </button>
                        @if(count($rooms) > 0)
                            <table>
                                <tr style="border-bottom: 1px solid #999">
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Rows</th>
                                    <th>Columns</th>
                                    <th>Edit</th>
                                    <th>Delete</th>
                                </tr>
                                @foreach($rooms as $room)
                                    <tr>
                                        <th>{{$room->id}}</th>
                                        <th>{{$room->name}}</th>
                                        <th>{{$room->rows}}</th>
                                        <th>{{$room->columns}}</th>
                                        <th>
                                            <button type="button" class="btn btn-primary update_room_admin"
                                                    data-toggle="modal"
                                                    data-target="#updateRoomModal" value="{{$room->id}}">
                                                Edit
                                            </button>
                                        </th>
                                        <th>
                                            <button type="button" class="btn btn-danger delete_room_admin"
                                                    data-toggle="modal"
                                                    data-target="#deleteRoomModal" value="{{$room->id}}">Delete
                                            </button>
                                        </th>
                                    </tr>
                                @endforeach

                            </table>

                            <div class="col-md" style="margin-top: 10px">
                                <div class="justify-content-center row">
                                    {{$rooms->links()}}
                                </div>
                            </div>
                        @else
                            Sorry, there are no rooms at the moment
                        @endif
                    </div>
                </div>
            </div>
        </div>


        <!-- Update Room Modal -->
        <div class="modal fade" id="updateRoomModal" tabindex="-1" role="dialog"
             aria-labelledby="newRoomModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="update_room_placeholder" id="updateRoomModalLabel">New Room</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="{{route('update_room', 'placeholder')}}"
                              id="update_room_modal_form">
                            @csrf
                            <div class="form-group row justify-content-center">
                                <div class="col-md-6">
                                    <label for="name">Name</label>
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
                                    <label for="rows">Rows</label>
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
                                    <label for="columns">Columns</label>
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
                                <button type="submit" class="btn btn-success">Update Room</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Room Modal -->
        <div class="modal fade" id="deleteRoomModal" tabindex="-1" role="dialog"
             aria-labelledby="deleteRoomModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteRoomModalLabel">Delete Room</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="{{ route('delete_room', '__placeholder__') }}"
                              id="delete_event_modal_form">
                            @csrf

                            <input type="checkbox" required> Do you really want to delete this room? This will also
                            delete all events happening in this room and their associated tickets.


                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    Close
                                </button>
                                <button type="submit" class="btn btn-primary">Delete Room
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

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
                                    <label for="name">Name</label>
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
                                    <label for="rows">Rows</label>
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
                                    <label for="columns">Columns</label>
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

    </div>
    </div>
    </div>
@endsection
