@extends('layouts.admin')

@section('title')
    Categories
@endsection

@section('content')
    @include('admin.errors')

    <form action="" method="post" class="mb-5">
        @csrf

        <label>Add category</label>
        <input name="id" type="number" min="1" placeholder="Category id" class="form-control mb-2" autocomplete="off" value="{{old('id')}}">

        <input type="submit" class="btn btn-success" value="Add">
    </form>

    <form action="" method="post" class="mb-5">
        @csrf

        <label>Delete category</label>
        <input name="id" type="number" min="1" placeholder="Category id" class="form-control mb-2" autocomplete="off" value="{{old('id')}}">

        <input type="submit" class="btn btn-danger" value="Delete">
    </form>

    <?= $grid ?>
@endsection
