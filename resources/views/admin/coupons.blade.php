@extends('layouts.admin')

@section('title')
    Coupons
@endsection

@section('content')
    <form action="{{route('admin.coupon.create')}}" method="post" class="mb-5">
        @csrf

        <label>Create coupon</label>
        <input name="percent" type="number" min="1" max="100" placeholder="Percent" class="form-control mb-2" autocomplete="off">
        <input name="token" type="text" placeholder="Token" class="form-control mb-2" autocomplete="off" maxlength="30">

        <input type="submit" class="btn btn-success" value="Add">
    </form>

    <form action="{{route('admin.coupon.delete')}}" method="post" class="mb-5">
        @csrf

        <label>Delete coupon</label>
        <input name="id" type="number" min="1" placeholder="Coupon id" class="form-control mb-2" autocomplete="off">

        <input type="submit" class="btn btn-danger" value="Delete">
    </form>

    <?= $grid ?>
@endsection
