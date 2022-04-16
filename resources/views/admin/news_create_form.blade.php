@extends('layouts.admin')

@section('title')
    Create news
@endsection

@section('content')
    <form action="{{route('admin.news.create')}}" method="post" class="mb-5">
        @csrf

        <h3>Create news</h3>

        <input name="title" type="text" placeholder="Title" class="form-control mb-2" autocomplete="off" required value="{{old('title')}}">
        <textarea name="content" placeholder="Content" class="form-control mb-2" autocomplete="off" required>{{old('content')}}</textarea>

        <input type="submit" class="btn btn-success" value="Create">
    </form>
@endsection
