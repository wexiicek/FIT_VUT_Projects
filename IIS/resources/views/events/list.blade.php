@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                @foreach($events as $event)
                    <div class="card" style="margin-bottom: 10px">
                        <div class="card-header"><a
                                href="{{ route('show_event', $event['event']->id) }}"><h4> {{ $event['event']->name }} [{{
                                    $event['event']->type }}]</h4></a></div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-8">
                                    <p>{{$event['event']->description}}</p>
                                    @if(!empty($event['event']->performers))
                                        <h5>Performers:</h5>
                                        <p style="text-align: justify-content">{{ $event['event']->performers}}</p>
                                    @endif
                                </div>
                                <div class="col-4">
                                    @if(!empty($event['event']->cover))
                                        <img class="eventImg img-fluid img-thumbnail float-right"
                                             src="../storage/images/{{$event['event']->cover }}">
                                    @endif
                                </div>
                            </div>
                            <div class="event_instances">
                                @foreach($event['instances'] as $instance)
                                    <a class="btn btn-outline-primary mr-1"
                                       href="{{ route('buy_ticket_get', $instance->id) }}">{{$instance->date}}, {{$instance->time}}</a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
