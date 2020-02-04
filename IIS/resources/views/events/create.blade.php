@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Create an event') }}</div>

                    <div class="card-body">
                        <form method="post" action="{{route('create_event_post')}}" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group row justify-content-center">
                                <div class="col-md-6">
                                    <input id="name" type="text"
                                           class="form-control @error('name') is-invalid @enderror" name="name"
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
                                    <input id="description" type="text"
                                           class="form-control @error('description') is-invalid @enderror"
                                           name="description" value="{{ old('description') }}"
                                           placeholder="Event Description" required autocomplete="description"
                                           autofocus>

                                    @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>


                            <div class="form-group row justify-content-center">
                                <div class="col-md-6">
                                    <select name="type">
                                        <option value="lecture">
                                            Lecture
                                        </option>

                                        <option value="movie">
                                            Movie
                                        </option>

                                        <option value="drama">
                                            Drama
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row justify-content-center">
                                <div class="col-md-6">
                                    <input type="file" name="event_picture" id="event_picture">
                                </div>
                            </div>


                            <div class="form-group row justify-content-center mb-0">
                                <div class="col-md-6 text-center">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-user-plus"></i> Create an event
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
