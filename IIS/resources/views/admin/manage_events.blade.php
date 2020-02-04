@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">




                <div class="card">
                    <div class="card-header">Events</div>
                    <div class="card-body">
                        <button type="button" class="btn btn-success col-md" data-toggle="modal"
                                data-target="#newEventModal">
                            Add New Event
                        </button>
                        @if(count($events) > 0)
                            <table>
                                <tr style="border-bottom: 1px solid #999">
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Performers</th>
                                    <th>Type</th>
                                    <th>Edit</th>
                                    <th>Delete</th>
                                </tr>
                                @foreach($events as $event)
                                    <tr>
                                        <th>{{$event->id}}</th>
                                        <th>{{$event->name}}</th>
                                        <th>{{substr($event->description, 0, 16)}}..</th>
                                        <th>{{substr($event->performers, 0, 16)}}..</th>
                                        <th>{{ucfirst($event->type)}}</th>
                                        <th>
                                            <button type="button" class="btn btn-primary update_event_admin"
                                                    data-toggle="modal"
                                                    data-target="#updateEventModal" value="{{$event->id}}">
                                                Edit
                                            </button>
                                        </th>
                                        <th>
                                            <button type="button" class="btn btn-danger delete_event_admin"
                                                    data-toggle="modal"
                                                    data-target="#deleteEventModal" value="{{$event->id}}">Delete
                                            </button>
                                        </th>
                                    </tr>
                                @endforeach

                            </table>

                            <div class="col-md" style="margin-top: 10px">
                                <div class="justify-content-center row">
                                    {{$events->links()}}
                                </div>
                            </div>
                        @else
                            Sorry, there are no events at the moment
                        @endif
                    </div>
                </div>
            </div>
        </div>


        <!-- Update Event Modal -->
        <div class="modal fade" id="updateEventModal" tabindex="-1" role="dialog"
             aria-labelledby="updateEventModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateEventModalLabel">Update Event <span
                                id="update_event_placeholder"></span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="{{route('update_event', 'placeholder')}}"
                              enctype="multipart/form-data" id="update_event_modal_form">
                            @csrf

                            <div class="form-group row justify-content-center">
                                                <div class="col-md-6">
                                                    <label for="name">Name</label>
                                                    <input id="name" type="text"
                                                           class="form-control @error('name') is-invalid @enderror"
                                                           name="name"
                                                           value="{{ old('name') }}" placeholder="Event Name" required
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
                                                        <input type="file" class="custom-file-input" id="inputGroupFile01"
                                                        aria-describedby="inputGroupFileAddon01" name="event_cover" id="event_cover" accept="image/*">
                                                        <label class="custom-file-label" for="inputGroupFile01">Choose file</label>
                                                    </div>
                                                </div>
                                                </div>
                                            </div>
                                            <div class="form-group row justify-content-center">
                                                <div class="col-md-6">
                                                    <input type="checkbox" class="form-controll" id="event_cover_del" name="event_cover_del">
                                                    <label class="form-check-label" for="event_cover_del">
                                                    Delete cover picture
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="form-group row justify-content-center">
                                                <div class="col-md-6">
                                                <label for="event_picture">Pictures</label>
                                                <div class="input-group">
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" id="inputGroupFile02"
                                                        aria-describedby="inputGroupFileAddon01" name="event_picture[]" id="event_picture" accept="image/*" multiple>
                                                        <label class="custom-file-label" for="inputGroupFile01">Choose file</label>
                                                    </div>


                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row justify-content-center">
                                                <div class="col-md-6">
                                                    <input type="checkbox" class="form-controll" id="event_picture_del" name="event_picture_del">
                                                    <label class="form-check-label" for="event_picture_del">
                                                    Delete other pictures
                                                    </label>
                                            </div>
                                        </div>

                            <div class="form-group row justify-content-center mb-0">
                                <div class="col-md-6 text-center">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-user-plus"></i> Update an event
                                    </button>
                                </div>
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
                                    <label for="eventNname">Name</label>
                                    <input id="eventName" type="text"
                                           class="form-control @error('name') is-invalid @enderror"
                                           name="eventName"
                                           value="{{ old('eventName') }}" placeholder="Event Name"
                                           required
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

        <!-- Delete Account Modal -->
        <div class="modal fade" id="deleteEventModal" tabindex="-1" role="dialog"
             aria-labelledby="deleteEventModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteEventModalLabel">Delete Event</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="{{ route('delete_event', '__placeholder__') }}"
                              id="delete_event_modal_form">
                            @csrf

                            <input type="checkbox" required> Do you really want to delete this event? This will also delete all instances of the event and their associated tickets.


                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    Close
                                </button>
                                <button type="submit" class="btn btn-primary">Delete Event
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
