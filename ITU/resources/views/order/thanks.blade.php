@extends('layouts.app')
<!--
* ITU Project 2019/2020
* Flight Search (Team xjurig00, xlinka01, xpukan01)
*
* Author of this file: Marian Pukancik (xpukan01)
*
* -->
@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="card mt-4">
                    <div class="card-header">Order complete!</div>
                    <div class="card-body">
                        <h3>Thank you for your order!</h3>
                        <p>Details will be sent to your e-mail address.</p>
                    </div>
                    <div class="card-footer">
                        <div class=" mx-auto" style="text-align: center">
                            <a class="btn btn-outline-primary" href="{{route('home')}}">Homepage</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
