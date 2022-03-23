@extends('layouts.admin')

@section('title')
    Products
@endsection

@section('content')
    @include('admin.errors')

    <form action="" method="post" class="mb-5">
        @csrf

        <label>Edit product</label>
        <input name="id" type="number" min="1" placeholder="Product id" class="form-control mb-2" autocomplete="off" value="{{old('id')}}">

        <input type="submit" class="btn btn-success" value="Edit">
    </form>

    <form action="" method="post" class="mb-5">
        @csrf

        <label>Delete product</label>
        <input name="id" type="number" min="1" placeholder="Product id" class="form-control mb-2" autocomplete="off" value="{{old('id')}}">

        <input type="submit" class="btn btn-danger" value="Edit">
    </form>

    <a class="btn btn-success text-white mb-1">Add product</a>
    <?= $grid ?>
@endsection
