@extends('layouts.admin')

@section('title')
    Categories
@endsection

@section('content')
    <form action="{{route('admin.category.create')}}" method="post" class="mb-5">
        @csrf

        <label>Add category</label>
        <input name="name" type="text" placeholder="Category name" class="form-control mb-2" autocomplete="off" maxlength="255">

        <input type="submit" class="btn btn-success" value="Add">
    </form>

    <form action="{{route('admin.category.delete')}}" method="post" class="mb-5">
        @csrf

        <label>Delete category</label>
        <input name="id" type="number" min="1" placeholder="Category id" class="form-control mb-2" autocomplete="off">

        <input type="submit" class="btn btn-danger" value="Delete">
    </form>

    <?= $grid ?>
@endsection
