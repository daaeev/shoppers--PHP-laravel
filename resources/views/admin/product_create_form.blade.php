@extends('layouts.admin')

@section('title')
    Create product
@endsection

@section('content')
    <form action="{{route('admin.product.create')}}" method="post" enctype="multipart/form-data">
        @csrf

        <h3>Create product</h3>

        <input name="name" type="text" placeholder="Name" class="form-control mb-2" autocomplete="off" required value="{{old('name')}}">
        <input name="subname" type="text" placeholder="Subname" class="form-control mb-2" autocomplete="off" required value="{{old('subname')}}">
        <textarea name="description" placeholder="Description" class="form-control mb-2" autocomplete="off" required>{{old('description')}}</textarea>

        @foreach($foreign_data as $foreign_column => $collection)
            <?php
                $label = (explode('_', $foreign_column))[0];
            ?>

            <label>{{ucfirst($label)}}</label>

            <select name="{{$foreign_column}}" class="form-control mb-2" required>
                @foreach($collection as $element)
                    <option value="{{$element->id}}">{{$element->name}}</option>
                @endforeach
            </select>
        @endforeach

        <input name="price" type="number" step="any" min="0" placeholder="Price (grivna)" class="form-control mb-2" autocomplete="off" required value="{{old('price')}}">
        <input name="discount_price" type="number" step="any" min="0" placeholder="Discount price (grivna)" class="form-control mb-2" autocomplete="off" value="{{old('discount_price')}}">
        <input name="count" type="number" min="0" placeholder="Count" class="form-control mb-2" autocomplete="off" required value="{{old('count')}}">

        <label for="main_image" class="form-control">Select main image</label>
        <label for="preview_image" class="form-control">Select preview image</label>

        <input type="submit" class="btn btn-success" value="Create">

        <input name="main_image" id="main_image" type="file" class="form-control mb-2" autocomplete="off" accept="image/*" required style="visibility: hidden">
        <input name="preview_image" id="preview_image" type="file" class="form-control mb-2" autocomplete="off" accept="image/*" style="visibility: hidden">
    </form>
@endsection
