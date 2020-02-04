@extends('layouts.app')
<!--
* ITU Project 2019/2020
* Flight Search (Team xjurig00, xlinka01, xpukan01)
*
* Author of this file: Adam Linka (xlinka01)
*
* -->
@section('content')
    <div class="container pt-2">
        <div class="profile">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">{{$user->username}}</div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <i class="fas fa-fw fa-user"></i>
                                <span id="user_name">{{ $user->name }}</span>
                            </li>
                            <li class="list-group-item">
                                <i class="fas fa-fw fa-phone"></i>
                                <span id="user_phone">{{ $user->phone_number ?? "Not Set" }}</span>
                            </li>
                            <li class="list-group-item">
                                <i class="fas fa-fw fa-envelope"></i>
                                <span id="user_email">{{ $user->email }}</span>
                            </li>
                            <li class="list-group-item">
                                <i class="fas fa-fw fa-plane-departure"></i>
                                <span id="user_preferred">{{ $user->preferred_airport }}</span>
                            </li>
                        </ul>
                        <div class="card-body">
                            <button id="credentials_button" type="button" class="btn btn-primary w-100" data-toggle="modal"
                                    data-target="#credentials_modal">
                                Change Your Credentials
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">Your Address</div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item address_item">
                                <div class="address_desc">Street</div>
                                <div class="address_value" id="user_street">{{ $user->street ?? "Not Set" }} </div>
                            </li>

                            <li class="list-group-item address_item">
                                <div class="address_desc">City</div>
                                <div class="address_value" id="user_city">{{ $user->city ?? "Not Set" }} </div>
                            </li>

                            <li class="list-group-item address_item">
                                <div class="address_desc">ZIP</div>
                                <div class="address_value" id="user_zip">{{ $user->zip ?? "Not Set" }} </div>
                            </li>

                            <li class="list-group-item address_item">
                                <div class="address_desc">State</div>
                                <div class="address_value" id="user_state">{{ $user->state ?? "Not Set" }} </div>
                            </li>
                        </ul>
                        <div class="card-body">
                            <button type="button" class="btn btn-primary w-100" data-toggle="modal"
                                    data-target="#address_modal">
                                Change Your Address
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header">Your passengers</div>
                        <div class="card-body">
                            <div class="card" style="margin-top: 0">
                                <div class="card-body">
                                    @if(count($passengers) == 0)
                                        <p>You have no passengers tied to your account.</p>
                                    @else
                                        <p>You have {{count($passengers)}}
                                            passenger{{count($passengers) > 1 ? "s": ""}}
                                            tied to your account.</p>
                                    @endif
                                    <button type="button" class="btn btn-primary" data-toggle="modal"
                                            data-target="#add_passenger_modal">
                                        Add Passengers
                                    </button>
                                </div>
                            </div>
                            <div class="row">
                                @if(count($passengers) > 0)
                                    @foreach($passengers as $passenger)
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-header">{{ $passenger->name }}</div>
                                                <ul class="list-group list-group-flush">
                                                    <li class="list-group-item address_item">
                                                        <div class="address_desc">Street</div>
                                                        <div
                                                            class="address_value">{{ $passenger->street ?? "Not Set" }} </div>
                                                    </li>

                                                    <li class="list-group-item address_item">
                                                        <div class="address_desc">City</div>
                                                        <div
                                                            class="address_value">{{ $passenger->city ?? "Not Set" }} </div>
                                                    </li>

                                                    <li class="list-group-item address_item">
                                                        <div class="address_desc">ZIP</div>
                                                        <div
                                                            class="address_value">{{ $passenger->zip ?? "Not Set" }} </div>
                                                    </li>

                                                    <li class="list-group-item address_item">
                                                        <div class="address_desc">State</div>
                                                        <div
                                                            class="address_value">{{ $passenger->state ?? "Not Set" }} </div>
                                                    </li>
                                                </ul>
                                                <div class="card-body">
                                                    <button type="button" class="btn btn-primary w-100">
                                                        Change Credentials
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-5">
                <div class="col-sm">
                    <div class="card">
                        <div class="card-header">Your Tickets</div>
                        <div class="card-body">
                            <div class="tickets">

                                <div class="result_flight">
                                    <div class="table_from">From</div>
                                    <div class="table_to">To</div>
                                    <div class="table_date">Date</div>
                                    <div class="table_time">Time</div>
                                    <div class="table_price">Price</div>
                                </div>
                                <div class="dropdown-divider"></div>

                                @if (count($tickets) > 0)

                                @foreach($tickets as $ticket)
                                    <div class="result_flight">
                                        <div class="table_from">{{ $ticket->from }}</div>
                                        <div class="table_to">{{ $ticket->to }}</div>
                                        <div class="table_date">{{ $ticket->date }}</div>
                                        <div class="table_time">{{ $ticket->time }}</div>
                                        <div class="table_price">{{ $ticket->price }}</div>
                                        <div class="table_select">
                                            <button disabled class="select_flight btn btn-secondary">Details</button>
                                        </div>
                                    </div>
                                @endforeach
                                @else
                                    <div class="result_flight">You have no tickets at the moment</div>
                                @endif

                                {{ $tickets->links() }}

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('modals.credentials')
    @include('modals.address')
    @include('modals.add_passenger')
    @yield('add_passenger_modal')
    @yield('credentials_modal')
    @yield('address_modal')
@endsection
