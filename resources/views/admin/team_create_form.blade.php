@extends('layouts.admin')

@section('title')
    Add teammate
@endsection

@section('content')
    <form action="{{route('admin.team.create')}}" method="post" enctype="multipart/form-data" class="mb-5">
        @csrf

        <h3>Add teammate</h3>

        <input name="full_name" type="text" maxlength="30" placeholder="Name" class="form-control mb-2" autocomplete="off" required value="{{old('name')}}">
        <input name="position" type="text" maxlength="30" placeholder="Position" class="form-control mb-2" autocomplete="off" required value="{{old('position')}}">
        <textarea name="description" placeholder="Description" class="form-control mb-2" autocomplete="off" required>{{old('description')}}</textarea>

        <input name="image" id="main_image" type="file" class="form-control mb-2" autocomplete="off" accept="image/*" required>

        <input type="submit" class="btn btn-success" value="Add">
    </form>
@endsection
