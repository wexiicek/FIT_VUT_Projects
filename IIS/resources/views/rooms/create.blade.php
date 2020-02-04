@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="card">
                    <div class="card-header">{{ __('Reserve A Seat') }}</div>

                    <div class="card-body">
                        <form method="post" action="{{route('buy_ticket_post', $instance->id)}}">
                            @csrf
                            <div class="form-group row justify-content-center">
                                <div class="col-md-6">
                                    <input id="name" type="text"
                                           class="form-control @error('name') is-invalid @enderror" name="name"
                                           value="{{ old('name') }}" placeholder="Price" required
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
                                    <input id="rows" type="text"
                                           class="form-control @error('rows') is-invalid @enderror"
                                           name="rows" value="{{ old('rows') }}"
                                           placeholder="Seats" required autocomplete="rows"
                                           autofocus>

                                    @error('rows')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row justify-content-center">
                                <div class="col-md-6">
                                    <input id="columns" type="text"
                                           class="form-control @error('columns') is-invalid @enderror"
                                           name="columns" value="{{ old('columns') }}"
                                           placeholder="Seats" required autocomplete="columns"
                                           autofocus>

                                    @error('columns')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>columns



                            <div class="form-group row justify-content-center mb-0">
                                <div class="col-md-6 text-center">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-user-plus"></i> Buy Tickets
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
