@extends('layouts.index')

@section('title')
    {{$product->slug}}
@endsection

@section('content')
    <div class="bg-light py-3">
      <div class="container">
        <div class="row">
          <div class="col-md-12 mb-0"><a href="{{route('home')}}">Home</a> <span class="mx-2 mb-0">/</span> <strong class="text-black">{{$product->name}}</strong></div>
        </div>
      </div>
    </div>

    <div class="site-section">
      <div class="container">
        <div class="row">
          <div class="col-md-6">
            <img src="{{asset('storage/products_images/' . $product->main_image)}}" alt="Image" class="img-fluid">
          </div>
          <div class="col-md-6">
            <h2 class="text-black">{{$product->name}} ({{$product->size->name}})</h2>
            <p>{{$product->description}}</p>
            @if($product->discount_price)
                <p><strong class="text-primary h4"><s>{{number_format($product->price, 2)}} {{$product->currency}}</s> {{number_format($product->discount_price, 2)}} {{$product->currency}}</strong></p>
            @else
                <p><strong class="text-primary h4">{{number_format($product->price, 2)}} {{$product->currency}}</strong></p>
            @endif

            <p id="button_add-to-card_block"><button id="add-to-cart-btn" data-href="{{route('ajax.cart.add', ['product_id' => $product->id])}}" class="buy-now btn btn-sm btn-primary">Add To Cart</button></p>

          </div>
        </div>
      </div>
    </div>

    @if(!empty($similar->all())))
        <div class="site-section block-3 site-blocks-2 bg-light">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-7 site-section-heading text-center pt-4">
            <h2>Similar Products</h2>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="nonloop-block-3 owl-carousel">

                  @foreach($similar as $item)
                      <div class="item">
                        <div class="block-4 text-center">
                          <figure class="block-4-image">
                            <a href="{{route('catalog.single', ['product' => $item->slug])}}"><img src="{{asset('storage/products_images/' . ($item->preview_image ?? $item->main_image))}}" alt="Image placeholder" class="img-fluid"></a>
                          </figure>
                          <div class="block-4-text p-4">
                            <h3 style="overflow: hidden; text-overflow: ellipsis;"><a href="{{route('catalog.single', ['product' => $item->slug])}}">{{$item->name}} ({{$item->size->name}})</a></h3>
                            <p class="mb-0" style="overflow: hidden; text-overflow: ellipsis;">{{$item->subname}}</p>
                              @if($item->discount_price)
                                  <p class="text-primary font-weight-bold"><span class="text-warning">Discount!</span> <s>{{number_format($item->price, 2)}} {{$item->currency}}</s> {{number_format($item->discount_price, 2)}} {{$item->currency}}</p>
                              @else
                                  <p class="text-primary font-weight-bold">{{number_format($item->price, 2)}} {{$item->currency}}</p>
                              @endif
                          </div>
                        </div>
                      </div>
                    @endforeach

            </div>
          </div>
        </div>
      </div>
    </div>
    @endif
@endsection
