@extends('layouts.index')

@section('title')
    {{$user->name}}
@endsection

@section('content')
<section class="banner-bottom">
    <div class="container py-md-5">
        <div class="row text-center">
            <div class="col-12">
                <p><h3>{{$user->name}}</h3></p>
                <p>
                    <strong>Email:</strong>
                    {{$user->email}}
                    @if (!$user->hasVerifiedEmail())
                        <strong><a href="{{route('verification.notice')}}" class="text-danger">Verify email</a></strong>
                    @else
                        <span class="text-success">Email verified</span>
                    @endif
                </p>
                <p>
                    <a class="text-primary" href="{{route('password.update')}}">Change password</a>
                </p>
                <br><p>
                    <a class="btn btn-danger" href="{{route('logout')}}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">Logout</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST">
                    @csrf
                </form>
                </p>
            </div>
        </div>
    </div>
</section>
@endsection
