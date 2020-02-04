@extends('layouts.app')
<!--
* ITU Project 2019/2020
* Flight Search (Team xjurig00, xlinka01, xpukan01)
*
* Author of this file: Dominik Juriga (xjurig00)
*
* -->
@section('content')
    <div class="container-fluid homepage">
        <div class="row justify-content-center">
            <div class="col-xl-3 col-lg-4 col-sm-12 col-12 homepage_search">
                <form id="flight_search" action="{{ route('search_flights') }}">

                    <!-- Search Line - From -->
                    <div class="search_line">
                        <div class="row h-100">
                            <div class="col-md-3 col-sm-3 col-3">
                                <p class="search_line_desc">From</p>
                            </div>
                            <div class="col-md-9 col-sm-9 col-9">
                                <input type="text" placeholder="Brno"
                                       value="{{$search_value}}"
                                       class="search_form_input" name="search_form_from"
                                       required>
                            </div>
                        </div>
                    </div>

                    <!-- Search Line - To -->
                    <div class="search_line" style="border-bottom: none">
                        <div class="row h-100">
                            <div class="col-md-3 col-sm-3 col-3">
                                <p class="search_line_desc">To</p>
                            </div>
                            <div class="col-md-9 col-sm-9 col-9">
                                <div class="row h-100">
                                    <div class="col">
                                        <input type="text" placeholder="Bratislava" id="search_form_to"
                                               value="{{$_GET['search_form_to'] ?? ""}}"
                                               class="search_form_input"
                                               name="search_form_to" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Search Line - Lucky Button -->
                    <div class="search_line" style="height: 30px">
                        <div class="row h-100">
                            <div class="col-md-3 col-sm-3 col-3">
                            </div>
                            <div class="col-md-9 col-sm-9 col-9">
                                <div id="feeling_lucky" title="Choose a random location"
                                     style="text-align: right; cursor: pointer">Feeling lucky?
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Search Line - Departure Date -->
                    <div class="search_line">
                        <div class="row h-100">
                            <div class="col-md-3 col-sm-3 col-3">
                                <p class="search_line_desc">Departure</p>
                            </div>
                            <div class="col-md-9 col-sm-9 col-9">
                                <input type="text" id="datepicker_departure" placeholder="{{$date}}"
                                       value="{{$_GET['search_form_dateFrom'] ?? ""}}" class="search_form_input readonly"
                                       name="search_form_dateFrom" required autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <!-- Search Line - Arrival Date -->
                    <div class="search_line">
                        <div class="row h-100">
                            <div class="col-md-3 col-sm-3 col-3">
                                <p class="search_line_desc">Arrival</p>
                            </div>
                            <div class="col-md-9 col-sm-9 col-9">
                                <input type="text" id="datepicker_arrival" placeholder="{{$date}}"
                                       value="{{$_GET['search_form_dateTo'] ?? ""}}" class="search_form_input readonly"
                                       name="search_form_dateTo" required autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <!-- Search Line - Time From -->
                    <div class="search_line">
                        <div class="row h-100">
                            <div class="col-md-3 col-sm-3 col-3">
                                <p class="search_line_desc">Time </p>
                            </div>
                            <div class="col-md-9 col-sm-9 col-9">
                                <input type="time" id="timepicker" placeholder="11:00"
                                       value="{{$_GET['search_form_timeFrom'] ?? ""}}" class="search_form_input"
                                       name="search_form_timeFrom" step="600">
                            </div>
                        </div>
                    </div>

                    <!-- Search Line - Passengers -->
                    <div class="row pass_description">
                        <div class="col-md-4 col-sm-4 col-4">
                            <p title="16 years and older">Adults <span>?</span></p>
                        </div>

                        <div class="col-md-4 col-sm-4 col-4">
                            <p title="Between 2 and 15 years old">Children <span>?</span></p>
                        </div>

                        <div class="col-md-4 col-sm-4 col-4">
                            <p title="Younger than 2">Infants <span>?</span></p>
                        </div>
                    </div>
                    <div class="search_line">
                        <div class="row">
                            <input type="number" id="adults" min="1" max="8" placeholder="1"
                                   class="col-md-4 col-sm-4 col-xs-4 col-4 search_form_input input_person"
                                   value="{{$_GET['search_form_adults'] ?? 1}}"
                                   name="search_form_adults">

                            <input type="number" id="children" min="0" max="8" placeholder="0"
                                   class="col-md-4 col-sm-4 col-xs-4 col-4 search_form_input input_person"
                                   value="{{$_GET['search_form_students'] ?? 0}}"
                                   name="search_form_students">

                            <input type="number" id="infants" min="0" max="8" placeholder="0"
                                   class="col-md-4 col-sm-4 col-xs-4 col-4 search_form_input input_person"
                                   value="{{$_GET['search_form_children'] ?? 0}}"
                                   name="search_form_children">
                        </div>
                    </div>
                    <button type="submit" name="search_form_sort" id="searchButton" value="price">SEARCH</button>
                </form>
            </div>
            <div class="col-xl-9 col-lg-8 col-sm-12 col-12 homepage_results">
                @if(count($flights) > 0)
                    <h4 id="sortResults">Sort flights by
                        <button type="submit" name="search_form_sort" value="price" form="flight_search"
                                class="result_sort {{$sort == 'price' ? 'active_sort':""}}">
                            Price
                        </button>
                        <button type="submit" name="search_form_sort" value="duration" form="flight_search"
                                class="result_sort {{$sort == 'duration' ? 'active_sort':""}}">
                            Duration
                        </button>
                        <button type="submit" name="search_form_sort" value="date" form="flight_search"
                                class="result_sort {{$sort == 'date' ? 'active_sort':""}}">
                            Date
                        </button>
                    </h4>
                @endif

                @if(count($flights) > 0)
                    <div class="results">
                        <div class="result_flight">
                            <div class="table_airport">Airport</div>
                            <div class="table_date">Date</div>
                            <div class="table_operator">Operator</div>
                            <div class="table_time">Time</div>
                            <div class="table_duration">Duration</div>
                            <div class="table_transfers">Transfers</div>
                            <div class="table_price">Price</div>
                            <div class="table_select"></div>
                        </div>
                        @foreach($flights as $flight)
                            <form class="result_flight" action="{{ route('book_flight') }}" method="POST">
                                @csrf
                                <input type="hidden" name="flight_data" value="{{ json_encode($flight) }}">
                                <input type="hidden" name="pas_count" value="{{ $pas_count }}">
                                <div class="table_airport">{{$flight->flyFrom}}</div>
                                <div class="table_date">{{date('d/m/Y', $flight->dTimeUTC + 60*60*2)}}</div>
                                <div class="table_operator">{{$companies->{$flight->airlines[0]} ?? ""}}</div>
                                <div class="table_time">{{date('H:i', $flight->dTimeUTC)}}</div>
                                <div class="table_duration">{{$flight->fly_duration}}</div>
                                <div class="table_transfers">
                                    @foreach($flight->route as $transfer)
                                        {{$transfer->flyTo == $flight->flyTo ? "" : $transfer->flyTo}}
                                    @endforeach
                                </div>
                                <div class="table_price">{{$flight->price}}€ <span title="Price for one bag">({{ $flight->bags_price->{1} ?? '-' }}€)</span>
                                </div>
                                <div class="table_select">
                                    <button type="submit" class="select_flight">Select</button>
                                </div>

                                <hr>

                                <div class="table_airport">{{$flight->flyTo}}</div>
                                <div class="table_date">{{date('d/m/Y', $flight->aTime)}}</div>
                                <div class="table_operator">{{$companies->{$flight->airlines[0]} ?? ""}}</div>
                                <div class="table_time">{{date('h:m', $flight->aTime)}}</div>
                                <div class="table_duration">{{$flight->fly_duration}}</div>
                                <div class="table_transfers">{{$flight->transfers[0] ?? "-"}}</div>
                                <div class="table_price">{{$flight->price}}€ <span title="Price for one bag">({{ $flight->bags_price->{1} ?? '-' }}€)</span>
                                </div>
                            </form>
                        @endforeach
                    </div>
                    @else
                        <p class="sorryText">There are no flights for you to enjoy yet! ¯\_(ツ)_/¯</p>
                    @endif

            </div>
        </div>
    </div>
@endsection
