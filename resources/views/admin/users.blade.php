<?php

use App\Models\User

?>
@extends('layouts.admin')

@section('title')
    Users
@endsection

@section('content')
    <form action="{{route('admin.users.role')}}" method="post" class="mb-5">
        @csrf

        <label>Set role</label>
        <input name="id" type="number" min="1" placeholder="User id" class="form-control mb-2" autocomplete="off" value="{{old('id')}}">

        <select name="role" class="form-control h-50 mb-2">
            <option value="{{User::$status_user}}">User</option>
            <option value="{{User::$status_admin}}">Admin</option>
            <option value="{{User::$status_banned}}">Banned</option>
        </select>

        <input type="submit" class="btn btn-success" value="Set">
    </form>

    <?= $grid ?>
@endsection
