@extends('layouts.index')

@section('title')
    Благодарность
@endsection

@section('content')
    <div class="bg-light py-3">
      <div class="container">
        <div class="row">
          <div class="col-md-12 mb-0"><a href="{{route('home')}}">Home</a> <span class="mx-2 mb-0">/</span> <strong class="text-black">Payment error</strong></div>
        </div>
      </div>
    </div>

    <div class="site-section">
      <div class="container">
        <div class="row">
          <div class="col-md-12 text-center">
            <span class="icon-error display-3 text-danger"></span>
            <h2 class="display-3 text-black">Oops</h2>
            <p class="lead mb-5">An error occurred while paying for the item.</p>
            <p><a href="{{route('catalog')}}" class="btn btn-sm btn-primary">Back to shop</a></p>
          </div>
        </div>
      </div>
    </div>
@endsection
