@extends('layouts.admin')

@section('title')
    Products
@endsection

@section('content')
    <form action="{{route('admin.product.edit.form')}}" method="get" class="mb-5">
        @csrf

        <label>Edit product</label>
        <input name="id" type="number" min="1" placeholder="Product id" class="form-control mb-2" autocomplete="off">

        <input type="submit" class="btn btn-success" value="Edit">
    </form>

    <form action="{{route('admin.product.delete')}}" method="post" class="mb-5">
        @csrf

        <label>Delete product</label>
        <input name="id" type="number" min="1" placeholder="Product id" class="form-control mb-2" autocomplete="off">

        <input type="submit" class="btn btn-danger" value="Delete">
    </form>

    <a class="btn btn-success text-white mb-1" href="{{route('admin.product.create.form')}}">Add product</a>
    <?= $grid ?>
@endsection
