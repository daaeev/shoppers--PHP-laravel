@extends('layouts.admin')

@section('title')
    Colors
@endsection

@section('content')
    <form action="{{route('admin.color.create')}}" method="post" class="mb-5">
        @csrf

        <label>Add color</label>
        <input name="name" type="text" placeholder="Color name" class="form-control mb-2" autocomplete="off" maxlength="255">

        <input type="submit" class="btn btn-success" value="Add">
    </form>

    <form action="{{route('admin.color.delete')}}" method="post" class="mb-5">
        @csrf

        <label>Delete color</label>
        <input name="id" type="number" min="1" placeholder="Color id" class="form-control mb-2" autocomplete="off"">

        <input type="submit" class="btn btn-danger" value="Delete">
    </form>

    <?= $grid ?>
@endsection
