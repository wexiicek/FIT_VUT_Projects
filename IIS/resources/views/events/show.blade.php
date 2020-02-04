@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card" style="margin-bottom: 10px">
                <div class="card-header"><h4>{{ $event->name }} [{{ $event->type}}]</h4></div>
                        <div class="card-body">
                        <div class="row">
                            <div class="col-8">
                            @if(!empty($event->performers))
                            <h5>Performers:</h5>
                            <p style="text-align: justify-content">{{ $event->performers}}</p>
                            @endif
                            @if(!empty($event->description))
                            <h5>Desciption:</h5>
                            <p style="text-align: justify-content">{{ $event->description}}</p>
                            @endif
                            </div>
                            <div class="col-4">
                                @if(!empty($event->cover))
                                            <img class="eventImg img-fluid img-thumbnail float-right" src="../storage/images/{{$event->cover}}">
                                @endif
                            </div>
                            </div>



                                    @if(!empty(json_decode($event->pictures, true)))
                                        <h5>Pictures</h5>
                                        <div class="row" id="eventPictures">

                                        @foreach (json_decode($event->pictures, true) as $image)
                                            <div class="col-lg-3 col-md-4 col-xs-6 thumb">
                                                <a class="thumbnail" href="#" data-image-id="" data-toggle="modal" data-title=""
                                                data-image="../storage/images/{{ $image }}"
                                                data-target="#image-gallery">
                                                    <img class="img-thumbnail"
                                                        src="../storage/images/{{ $image }}"
                                                        >
                                                </a>
                                            </div>
                                        @endforeach
                                        </div>
                                    @endif




                                <div class="modal fade" id="image-gallery" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title" id="image-gallery-title"></h4>
                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <img id="image-gallery-image" class="img-responsive col-md-12" src="">
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary float-left" id="show-previous-image"><i class="fa fa-arrow-left"></i>
                                                </button>

                                                <button type="button" id="show-next-image" class="btn btn-secondary float-right"><i class="fa fa-arrow-right"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>





                                <h5>Instances</h5>



                                        @foreach($instances as $instance)
                                            <a class="btn btn-outline-primary mr-1" href="{{ route('buy_ticket_get', $instance->id) }}">{{$instance->date}}, {{$instance->time}}</a>
                                        @endforeach
                        </div>
            </div>
        </div>
    </div>
</div>
@endsection
