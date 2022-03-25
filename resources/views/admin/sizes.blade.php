@extends('layouts.admin')

@section('title')
    Sizes
@endsection

@section('content')
    <form action="" method="post" class="mb-5">
        @csrf

        <label>Add size</label>
        <input name="id" type="number" min="1" placeholder="Size id" class="form-control mb-2" autocomplete="off">

        <input type="submit" class="btn btn-success" value="Add">
    </form>

    <form action="" method="post" class="mb-5">
        @csrf

        <label>Delete size</label>
        <input name="id" type="number" min="1" placeholder="Size id" class="form-control mb-2" autocomplete="off">

        <input type="submit" class="btn btn-danger" value="Delete">
    </form>

    <?= $grid ?>
@endsection
