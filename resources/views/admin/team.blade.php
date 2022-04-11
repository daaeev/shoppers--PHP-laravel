@extends('layouts.admin')

@section('title')
    Team
@endsection

@section('content')
    <form action="{{route('admin.team.edit.form')}}" method="get" class="mb-5">
        <label>Edit teammate</label>
        <input name="id" type="number" min="1" placeholder="Teammate id" class="form-control mb-2" autocomplete="off">

        <input type="submit" class="btn btn-success" value="Edit">
    </form>

    <form action="{{route('admin.team.delete')}}" method="post" class="mb-5">
        @csrf

        <label>Delete teammate</label>
        <input name="id" type="number" min="1" placeholder="Teammate id" class="form-control mb-2" autocomplete="off">

        <input type="submit" class="btn btn-danger" value="Delete">
    </form>

    <a class="btn btn-success text-white mb-1" href="{{route('admin.team.create.form')}}">Add teammate</a>
    <?= $grid ?>
@endsection
