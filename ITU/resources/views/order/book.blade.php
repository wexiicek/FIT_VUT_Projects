@extends('layouts.app')
<!--
* ITU Project 2019/2020
* Flight Search (Team xjurig00, xlinka01, xpukan01)
*
* Author of this file: Marian Pukancik (xpukan01)
*
* -->
@section('content')
    <div class="order">
        <p id="pas_count" style="display: none">{{$pas_count}}</p>
        <div class="container pt-2">
            <div class="row">
                <div class="col">
                    <div class="card order_top">
                        <div class="card-header">Order a flight from <b>{{ $flight->cityFrom }} <span
                                    title="{{ $flight->countryFrom->name }}">[{{ $flight->countryFrom->code }}]</span></b>
                            to <b>{{ $flight->cityTo }} <span title="{{ $flight->countryTo->name }}">[{{ $flight->countryTo->code }}]</span></b>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header">Bags</div>
                                        <div class="card-body">

                                            <div class="order_line">
                                                <div class="o_line_desc">
                                                    Hand Bag
                                                </div>
                                                <div class="o_line_val">
                                                    {{ $flight->baglimit->hand_width }}
                                                    x{{ $flight->baglimit->hand_height }}
                                                    x{{ $flight->baglimit->hand_length }} [cm]
                                                </div>
                                            </div>

                                            <div class="order_line">
                                                <div class="o_line_desc">
                                                    Hand Bag
                                                </div>
                                                <div class="o_line_val">
                                                    {{ $flight->baglimit->hand_weight }} [kg]
                                                </div>
                                            </div>

                                            <div class="order_line">
                                                <div class="o_line_desc">
                                                    Hold Bag
                                                </div>
                                                <div class="o_line_val">
                                                    {{ $flight->baglimit->hold_width }}
                                                    x{{ $flight->baglimit->hold_height }}
                                                    x{{ $flight->baglimit->hold_length }} [cm]
                                                </div>
                                            </div>

                                            <div class="order_line">
                                                <div class="o_line_desc">
                                                    Hold Bag
                                                </div>
                                                <div class="o_line_val">
                                                    {{ $flight->baglimit->hold_weight }} [kg]
                                                </div>
                                            </div>

                                            <div class="dropdown-divider"></div>

                                            <div class="order_line">
                                                <div class="o_line_desc">
                                                    Additional Bag
                                                </div>
                                                <div class="o_line_val">
                                                    {{ $flight->bags_price->{1} }} [€]
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col">
                                    <div class="card">
                                        <div class="card-header">Passenger Data</div>
                                        <div class="card-body">
                                            <p>You have {{ $pas_count }} passenger{{$pas_count > 1 ? "s" : ""}} on your
                                                flight. Please, fill in the required data.</p>


                                            <div class="row">
                                                @if(Auth::user())
                                                    <div class="col-md-6">
                                                        <div class="card mt-3">
                                                            <div class="card-header">{{Auth::user()->username}}</div>
                                                            <div class="card-body">


                                                                <div class="form-group row">
                                                                    <label for="passenger_name0"
                                                                           class="col-md-4 col-form-label text-md-right control-label">Name</label>

                                                                    <div class="col-md-6">
                                                                        <input id="passenger_name0" type="text"
                                                                               class="form-control @error('passenger_name') is-invalid @enderror"
                                                                               name="passenger_name"
                                                                               value="{{Auth::user()->name}}"
                                                                               autocomplete="passenger_name" required>

                                                                        @error('passenger_name')
                                                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>

                                                                <div class="form-group row">
                                                                    <label for="passenger_street0"
                                                                           class="col-md-4 col-form-label text-md-right control-label">Street</label>

                                                                    <div class="col-md-6">
                                                                        <input id="passenger_street0" type="text"
                                                                               class="form-control @error('passenger_street') is-invalid @enderror"
                                                                               name="passenger_street"
                                                                               value="{{Auth::user()->street}}"
                                                                               autocomplete="passenger_street">

                                                                        @error('passenger_street')
                                                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>

                                                                <div class="form-group row">
                                                                    <label for="passenger_city0"
                                                                           class="col-md-4 col-form-label text-md-right control-label">City</label>

                                                                    <div class="col-md-6">
                                                                        <input id="passenger_city0" type="text"
                                                                               class="form-control @error('passenger_city') is-invalid @enderror"
                                                                               name="passenger_city"
                                                                               value="{{Auth::user()->city}}"
                                                                               autocomplete="passenger_city">

                                                                        @error('passenger_city')
                                                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>

                                                                <div class="form-group row">
                                                                    <label for="passenger_zip0"
                                                                           class="col-md-4 col-form-label text-md-right control-label">ZIP
                                                                        Code</label>

                                                                    <div class="col-md-6">
                                                                        <input id="passenger_zip0" type="number"
                                                                               min="10000" max="99999"
                                                                               class="form-control @error('passenger_zip') is-invalid @enderror"
                                                                               name="passenger_zip"
                                                                               value="{{Auth::user()->zip}}"
                                                                               autocomplete="passenger_zip">

                                                                        @error('passenger_zip')
                                                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>

                                                                <div class="form-group row">
                                                                    <label for="passenger_state0"
                                                                           class="col-md-4 col-form-label text-md-right control-label">State</label>

                                                                    <div class="col-md-6">
                                                                        <input id="passenger_state0" type="text"
                                                                               class="form-control @error('passenger_state') is-invalid @enderror"
                                                                               name="passenger_state"
                                                                               value="{{Auth::user()->state}}"
                                                                               autocomplete="passenger_state">

                                                                        @error('passenger_state')
                                                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>


                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                @for ($i = Auth::user() ? 1 : 0; $i < $pas_count; $i++)
                                                    <div class="col-md-6">
                                                        <div class="card mt-3">
                                                            <div
                                                                class="card-header">{{$i == 0 ? "Yourself" : "Passenger "}}{{$i >= 1 ? $i + 1 : ""}}</div>
                                                            <div class="card-body">

                                                                @if(!Auth::user() && $i == 0)
                                                                    <div class="form-group row">
                                                                        <label for="passenger_email"
                                                                               class="col-md-4 col-form-label text-md-right control-label">E-Mail</label>

                                                                        <div class="col-md-6">
                                                                            <input id="passenger_email" type="e-mail"
                                                                                   class="form-control @error('passenger_email') is-invalid @enderror"
                                                                                   name="passenger_email"
                                                                                   autocomplete="passenger_email"
                                                                                   required>

                                                                            @error('passenger_email')
                                                                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                @endif

                                                                <div class="form-group row">
                                                                    <label for="passenger_name{{ $i }}"
                                                                           class="col-md-4 col-form-label text-md-right control-label">Name</label>

                                                                    <div class="col-md-6">
                                                                        <input id="passenger_name{{ $i }}" type="text"
                                                                               class="form-control @error('passenger_name') is-invalid @enderror"
                                                                               name="passenger_name"
                                                                               autocomplete="passenger_name"
                                                                               required>

                                                                        @error('passenger_name')
                                                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>

                                                                <div class="form-group row">
                                                                    <label for="passenger_street{{ $i }}"
                                                                           class="col-md-4 col-form-label text-md-right control-label">Street</label>

                                                                    <div class="col-md-6">
                                                                        <input id="passenger_street{{ $i }}" type="text"
                                                                               class="form-control @error('passenger_street') is-invalid @enderror"
                                                                               name="passenger_street"
                                                                               autocomplete="passenger_street"
                                                                        >

                                                                        @error('passenger_street')
                                                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>

                                                                <div class="form-group row">
                                                                    <label for="passenger_city{{ $i }}"
                                                                           class="col-md-4 col-form-label text-md-right control-label">City</label>

                                                                    <div class="col-md-6">
                                                                        <input id="passenger_city{{ $i }}" type="text"
                                                                               class="form-control @error('passenger_city') is-invalid @enderror"
                                                                               name="passenger_city"
                                                                               autocomplete="passenger_city">

                                                                        @error('passenger_city')
                                                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>

                                                                <div class="form-group row">
                                                                    <label for="passenger_zip{{ $i }}"
                                                                           class="col-md-4 col-form-label text-md-right control-label">ZIP
                                                                        Code</label>

                                                                    <div class="col-md-6">
                                                                        <input id="passenger_zip{{ $i }}" type="number"
                                                                               min="10000" max="99999"
                                                                               class="form-control @error('passenger_zip') is-invalid @enderror"
                                                                               name="passenger_zip"
                                                                               autocomplete="passenger_zip">

                                                                        @error('passenger_zip')
                                                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>

                                                                <div class="form-group row">
                                                                    <label for="passenger_state{{ $i }}"
                                                                           class="col-md-4 col-form-label text-md-right control-label">State</label>

                                                                    <div class="col-md-6">
                                                                        <input id="passenger_state{{ $i }}" type="text"
                                                                               class="form-control @error('passenger_state') is-invalid @enderror"
                                                                               name="passenger_state"
                                                                               autocomplete="passenger_state">

                                                                        @error('passenger_state')
                                                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>

                                                                @if(Auth::check())
                                                                    <div class="form-group row">
                                                                        <div class="col-md-8 sel">
                                                                            <select class="custom-select">
                                                                                <option value="-1" selected>Select from
                                                                                    your passengers
                                                                                </option>
                                                                                @foreach($passengers as $passenger)
                                                                                    <option
                                                                                        value="{{ $passenger->id }}">{{ $passenger->name }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <button
                                                                                class="btn btn-secondary w-100 passenger_fill"
                                                                                value="{{$i}}">Fill
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                @endif


                                                            </div>
                                                        </div>
                                                    </div>
                                                @endfor
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card order_top">
                                <div class="card-header">
                                    Seats
                                </div>
                                <div class="card-body">
                                    <p>Please, choose {{$pas_count}} seat{{$pas_count > 1 ? "s" : ""}}.</p>
                                    <div class="col">
                                        <div class="card  d-flex">
                                            <div class="card-body align-content-center justify-content-center">
                                                <table style="margin: auto">
                                                    @for($row = 0; $row < 31; $row++)
                                                        @if ($row == 0)
                                                            <tr>
                                                                @for($col = 0; $col < 12; $col++)
                                                                    <th>
                                                                        <button
                                                                            class="flight_seat counter counter_top">{{$col > 0 && $col % 4 != 0? $col : ""}}</button>
                                                                    </th>
                                                                @endfor
                                                            </tr>
                                                        @else
                                                            <tr>
                                                                @for($col = 0; $col < 12; $col++)
                                                                    @if ($col % 4 != 0 || $col == 0)
                                                                        @if ($col == 0)
                                                                            <td>
                                                                                <button
                                                                                    class="flight_seat counter counter_top">{{$row > 0 ? $row : ""}}</button>
                                                                            </td>
                                                                        @else
                                                                            <td>
                                                                                <button
                                                                                    class="flight_seat {{ !rand(0,5) ? "reserved" : "" }}"></button>
                                                                            </td>
                                                                        @endif
                                                                    @else
                                                                        <td>
                                                                            <button
                                                                                class="flight_seat"
                                                                                style="border: none"></button>
                                                                        </td>
                                                                    @endif
                                                                @endfor
                                                                @endif
                                                            </tr>
                                                            @endfor
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <a href="{{route('thanks')}}" class="btn btn-primary w-100">Confirm Order</a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
