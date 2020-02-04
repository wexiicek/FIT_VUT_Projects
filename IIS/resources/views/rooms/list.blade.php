@extends('layouts.app')

@section('content')

    @if(auth()->user())
    <div class="container col-md-6">
        <div class="row justify-content-center">
        <a class="btn btn-primary" href="{{ route('create_event_get') }}">Add New Event</a>
        </div>
</div>

    @endif
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @foreach($events as $event)
            <div class="card" style="margin-bottom: 10px">
                <div class="card-header"><a href="{{ route('show_event', $event->event_id) }}"> {{ $event->name }} </a></div>
                        <div class="card-body">
                            <p>{{ $event->description }}</p>
                            <p>{{ $event->type }}</p>
                        </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
