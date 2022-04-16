@extends('layouts.admin')

@section('title')
    News
@endsection

@section('content')
    <form action="{{route('admin.news.send')}}" method="post" class="mb-5">
        @csrf

        <label>Send news</label>
        <input name="id" type="number" min="1" placeholder="News id" class="form-control mb-2" autocomplete="off">

        <input type="submit" class="btn btn-danger" value="Send">
    </form>

    <a class="btn btn-success text-white mb-1" href="{{route('admin.news.create.form')}}">Create news</a>
    <?= $grid ?>
@endsection
