@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="card">
                    <div class="card-header">{{ __('Reserve A Seat') }}</div>

                    <div class="card-body">
                    <p style="display: none" id="instance_id">{{$instance->id}}</p>
                        <div class="row">
                            <div class="col-sm">


                            <h3> {{$event->name}} [{{$event->type}}]</h3>
                            <p><span>Date: </span>{{$instance->date}}</p>
                            <p><span>Time: </span>{{$instance->time}}</p>
                            <p>Price per ticket: <span id="price_per_ticket">{{$instance->price}}</span> €</p>
                            <p>Total price: <span id="price_total">0</span> €</p>

                            </div>
                            <div class="col-sm">
                            @if(!empty($event->cover))

                                    <img class="eventImg img-fluid img-thumbnail float-right" src="../../storage/images/{{ $event->cover }}">

                            @endif

                            </div>
                        </div>
                        <div class="row" style="margin-bottom: 10px">
                        <div class="col-sm">
                            <button type="button" class="seat_btn_example" style="background: white"></button><span class="legend">Available</span>
                            <button type="button" class="seat_btn_example" style="background: red"></button><span class="legend">Reserved</span>
                            <button type="button" class="seat_btn_example" style="background: blue"></button><span class="legend">Selected</span><br>
                            <button type="button" class="front" style="background: white; width: 40%; height: 35px; margin-left: 50px; margin-top: 15px">Screen</button>
                        </div>
                        </div>

                        <form method="post" action="{{route('buy_ticket_post', $instance->id)}}" id="ticket_form">
                            @csrf

                            <input type="hidden" name="price" value="{{$instance->price ?? -1}}" id="ticket_price">
                            <input type="hidden" name="user_id" value="{{Auth::user() ? Auth::user()->id : null}}"
                                   id="user_id">
                            <input type="hidden" name="ticket_seats" value="" id="ticket_seats">
                            <input type="hidden" name="ticket_amount" value="0" id="ticket_amount">



                            <div class="row">
                            <div class="col-sm">
                                @for($row = 1; $row < $room->rows + 1; $row++)

                                    <p style="width: 30px; float: left; margin: 0">{{$row}}</p>
                                    @for($column = 1; $column < $room->columns + 1; $column++)
                                        <button type="button" value="{{$row}}-{{$column}}"
                                                class="seat_btn {{in_array($row."-".$column, $seats) ? "reserved" : ""}}"></button>
                                    @endfor
                                    <br>
                                @endfor
                            </div>
</div>


                            @if(!Auth::user())
                                <div class="form-group row justify-content-center">
                                    <div class="col-md-6">
                                        Email
                                        <input id="email" type="email"
                                               class="form-control @error('email') is-invalid @enderror"
                                               name="email" value="{{ old('email') }}"
                                               placeholder="john@doe.com" required autocomplete="email"
                                               autofocus>

                                        @error('email')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                </div>
                            @endif


                            <div class="form-group row justify-content-center mb-0">

                                    <button type="submit" class="btn btn-success reserve_seats">
                                        <i class="fas fa-user-plus"></i> Reserve Seats
                                    </button>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
