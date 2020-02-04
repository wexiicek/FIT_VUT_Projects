@extends('layouts.app')

@section('content')
    <!--<div class="spotlight"></div>-->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">

                    <h1>Buy tickets</h1>
                    <a class="btn btn-outline-secondary col-md-12" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample" >
                        Toggle Filter
                    </a>
                    <div class="collapse show" id="collapseExample" show>
                    <div class="card card-body">
                    <div class="form-group col-md">
                        <form method="get" action="{{route('filter')}}">
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label for="eventType">Type</label>
                                    <select name="eventType" id="eventType" class="form-control">
                                        <option value="">All Types</option>
                                        @foreach($eventTypes as $event)
                                                <option value="{{$event->type}}" <?php
                                                if (isset($_GET['eventType']) && ($_GET['eventType']) == $event->type) {
                                                    echo('selected');
                                                }
                                                ?>>{{ $event->type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="eventName">Event</label>
                                    <select name="eventName" id="eventName" class="form-control">
                                        <option value="">All Events</option>
                                        @foreach($instantiatedEvents as $event)
                                                <option value="{{$event->id}}" <?php
                                                if (isset($_GET['eventName']) && ($_GET['eventName']) == $event->id) {
                                                    echo('selected');
                                                }
                                                ?>>{{ $event->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="datepicker">Date</label>
                                    <div class="input-group mb-3">
                                        <input value="<?php echo isset($_GET['date']) ? $_GET['date'] : '' ?>" type="text" id="datepicker" name="date" autocomplete="off" placeholder="Select Date" class="form-control" readonly required></p>
                                        <div class="input-group-append">
                                            <button id="reset-date" class="btn input-group-text" type="button">Reset</button>
                                        </div>
                                    </div>
                                </div>

                            </div>


                            <div class="form-row">

                                    <div class="form-group col-md-3">
                                        <label class="form-group-label" for="priceRangeFrom">Price From</label>
                                        <input type="number" placeholder="From" name="f" class="form-control" id="priceRangeFrom" value="<?php echo isset($_GET['f']) ? $_GET['f'] : '' ?>">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label class="form-group-label" for="priceRangeTo">Price To</label>
                                        <input type="number" placeholder="To" name="t" class="form-control" id="priceRangeTo" value="<?php echo isset($_GET['t']) ? $_GET['t'] : '' ?>">
                                    </div>

                                <div class="form-group col-md-6">
                                    <label class="form-group-label" for="sort">Sort</label>
                                    <select name="sort" id="sort" class="form-control">
                                        <option value="da"<?php
                                            if (isset($_GET['sort']) && ($_GET['sort']) == 'da') {
                                                echo('selected');
                                            }
                                            ?>>Date - Ascending</option>
                                        <option value="dd"<?php
                                            if (isset($_GET['sort']) && ($_GET['sort']) == 'dd') {
                                                echo('selected');
                                            }
                                            ?>>Date - Descending</option>
                                        <option value="pa"<?php
                                            if (isset($_GET['sort']) && ($_GET['sort']) == 'pa') {
                                                echo('selected');
                                            }
                                            ?>>Price - Ascending</option>
                                        <option value="pd"<?php
                                            if (isset($_GET['sort']) && ($_GET['sort']) == 'pd') {
                                                echo('selected');
                                            }
                                            ?>>Price - Descending</option>
                                        <option value="ta"<?php
                                            if (isset($_GET['sort']) && ($_GET['sort']) == 'ta') {
                                                echo('selected');
                                            }
                                            ?>>Time - Ascending</option>
                                        <option value="td"<?php
                                            if (isset($_GET['sort']) && ($_GET['sort']) == 'td') {
                                                echo('selected');
                                            }
                                            ?>>Time - Descending</option>
                                    </select>
                                </div>

                            </div>
                            <label class="form-group-label" for="rooms">Rooms</label>
                            <div class="form-group" id="rooms">
                            @foreach($rooms as $room)
                                <div class="form-check form-check-inline">
                                    <input type="checkbox" name="r[]" value="{{$room->id}}" id="{{$room->id}}" class="form-check-input" <?php
                                    if (isset($_GET['r']) && in_array($room->id, $_GET['r'])) {
                                        echo('checked');
                                    }
                                    ?>>
                                    <label class="form-check-label" for="{{$room->id}}">
                                        {{$room->name}}
                                    </label>
                                </div>
                            @endforeach
                            </div>

                            <div class="modal-footer">
                                    <a type="button" class="btn btn-secondary col-md-2" href="{{ route('home') }}">Reset</a>
                                    <button type="submit" class="btn btn-primary col-md-2">Filter</button>
                            </div>
                        </form>
                    </div>
                    </div>
                    </div>
                @foreach($events as $event)
                    <div class="card" style="margin-bottom: 10px">
                        <div class="card-header"><a
                                href="{{ route('show_event', $event->event_id) }}"><h4> {{ App\Event::find($event->event_id)->name }} [{{ App\Event::find($event->event_id)->type }}]</h4></a>
                        </div>
                        <div class="card-body">
                            <div class="container">
                                <div class="row">
                                    <div class="col-sm">

                                        <p><span style="font-weight: bold">Date: </span>{{ $event->date }}</p>
                                        <p><span style="font-weight: bold">Time: </span>{{ $event->time }}</p>
                                        <p><span style="font-weight: bold">Price: </span>{{ $event->price }} €</p>
                                        <p><span style="font-weight: bold">Location: </span>{{ App\Room::find($event->room_id)->name }}</p>

                                    </div>
                                    <div class="col-sm">
                                    @if(!empty(App\Event::find($event->event_id)->cover))
                                            <img class="eventImg img-fluid img-thumbnail float-right" src="../storage/images/{{App\Event::find($event->event_id)->cover}}">
                                    @endif
                                    </div>
                                </div>

                                <a class="btn btn-primary" href="{{route('buy_ticket_get', $event->id)}}">Reserve
                                Seats</a>

                            </div>



                        </div>
                    </div>
                @endforeach
                @if( method_exists($events,'links') )
                    <div class="row justify-content-center">
                    {{  $events ->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
