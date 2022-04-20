@extends('layouts.admin')

@section('title')
    Exchange
@endsection

@section('content')
    <?= $grid ?>

    <p><a class="btn btn-success text-white mb-1" href="{{route('admin.exchange.update')}}">Update exchange rates in DB</a></p>
@endsection
