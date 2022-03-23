@extends('layouts.admin')

@section('title')
    Colors
@endsection

@section('content')
    @include('admin.errors')

    <form action="" method="post" class="mb-5">
        @csrf

        <label>Add color</label>
        <input name="id" type="number" min="1" placeholder="Color id" class="form-control mb-2" autocomplete="off" value="{{old('id')}}">

        <input type="submit" class="btn btn-success" value="Add">
    </form>

    <form action="" method="post" class="mb-5">
        @csrf

        <label>Delete color</label>
        <input name="id" type="number" min="1" placeholder="Color id" class="form-control mb-2" autocomplete="off" value="{{old('id')}}">

        <input type="submit" class="btn btn-danger" value="Delete">
    </form>

    <?= $grid ?>
@endsection
