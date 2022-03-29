@extends('layouts.admin')

@section('title')
    Sizes
@endsection

@section('content')
    <form action="{{route('admin.size.create')}}" method="post" class="mb-5">
        @csrf

        <label>Add size</label>
        <input name="name" type="text" placeholder="Size" class="form-control mb-2" autocomplete="off" maxlength="255">

        <input type="submit" class="btn btn-success" value="Add">
    </form>

    <form action="{{route('admin.size.delete')}}" method="post" class="mb-5">
        @csrf

        <label>Delete size</label>
        <input name="id" type="number" min="1" placeholder="Size id" class="form-control mb-2" autocomplete="off">

        <input type="submit" class="btn btn-danger" value="Delete">
    </form>

    <?= $grid ?>
@endsection
