@extends('layouts.admin')

@section('title')
    Messages
@endsection

@section('content')
    <form action="{{route('admin.message.status-answered')}}" method="post" class="mb-5">
        @csrf

        <label>Set answered status</label>
        <input name="id" type="number" min="1" placeholder="Message id" class="form-control mb-2" autocomplete="off">

        <input type="submit" class="btn btn-success" value="Set">
    </form>

    <a class="btn btn-danger text-white mb-1" href="{{route('admin.messages.clear')}}">Delete answered messages</a>
    <?= $grid ?>
@endsection
