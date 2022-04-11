@extends('layouts.admin')

@section('title')
    Edit teammate data
@endsection

@section('content')
    <form action="{{route('admin.team.edit')}}" method="post" enctype="multipart/form-data" class="mb-5">
        @csrf

        <h3>Add teammate</h3>

        <input type="hidden" name="id" value="{{$model->id}}">

        <input name="full_name" type="text" maxlength="30" placeholder="Name" class="form-control mb-2" autocomplete="off" required value="{{$model->full_name}}">
        <input name="position" type="text" maxlength="30" placeholder="Position" class="form-control mb-2" autocomplete="off" required value="{{$model->position}}">
        <textarea name="description" placeholder="Description" class="form-control mb-2" autocomplete="off" required>{{$model->description}}</textarea>

        <input name="image" id="main_image" type="file" class="form-control mb-2" autocomplete="off" accept="image/*">

        <input type="submit" class="btn btn-success" value="Edit">
    </form>
@endsection
