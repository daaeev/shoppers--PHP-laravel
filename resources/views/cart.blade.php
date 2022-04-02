@extends('layouts.index')

@section('title')
    Корзина
@endsection

@section('content')
<div class="bg-light py-3">
      <div class="container">
        <div class="row">
          <div class="col-md-12 mb-0"><a href="{{route('home')}}">Home</a> <span class="mx-2 mb-0">/</span> <strong class="text-black">Cart</strong></div>
        </div>
      </div>
    </div>

    <div class="site-section">
      <div class="container">
        <div class="row mb-5">
          <div class="col-md-12">
            <div class="site-blocks-table cart-table-block">

                @if(!empty($products->all()))
                  <table class="table table-bordered cart-products-table">
                    <thead>
                      <tr>
                        <th class="product-thumbnail">Image</th>
                        <th class="product-name">Product</th>
                        <th class="product-quantity">Quantity</th>
                        <th class="product-price">Price</th>
                        <th class="product-remove">Remove</th>
                      </tr>
                    </thead>
                    <tbody>

                    @foreach($products as $product)
                      <tr id="product-{{$product->id}}" class="cart-product">
                        <td class="product-thumbnail">
                          <img src="{{asset('storage/products_images/' . ($product->preview_image ?? $product->main_image))}}" alt="Image" class="img-fluid">
                        </td>
                        <td class="product-name">
                          <h2 class="h5 text-black"><a href="{{route('catalog.single', ['product' => $product->slug])}}">{{$product->name}} ({{$product->size->name}})</a></h2>
                        </td>
                          <td>
                              <div class="input-group mx-auto mb-3" style="max-width: 120px;">
                                  <div class="input-group-prepend">
                                      <button class="btn btn-outline-primary js-btn-minus" type="button">&minus;</button>
                                  </div>
                                  <input type="text" class="form-control text-center" value="{{$cart_array[$product->id]['count']}}" placeholder="" aria-label="Example text with button addon" aria-describedby="button-addon1">
                                  <div class="input-group-append">
                                      <button class="btn btn-outline-primary js-btn-plus" type="button">&plus;</button>
                                  </div>
                              </div>

                          </td>
                        <td>
                            @if($product->discount_price)
                                <s>{{$product->price}}₴</s> <span class="product_price">{{$product->discount_price}}</span>₴
                            @else
                                <span class="product_price">{{$product->price}}</span>₴
                            @endif
                        </td>
                        <td><button data-href="{{route('ajax.cart.remove', ['product_id' => $product->id])}}" class="btn btn-primary btn-sm remove-from-cart-btn">X</button></td>
                      </tr>
                    @endforeach

                    </tbody>
                  </table>
                @else
                    <h2 class="text-center">Cart is empty!</h2>
                @endif

            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-8 pl-5">
            <div class="row justify-content-end">
              <div class="col-md-7">
                <div class="row">
                  <div class="col-md-12 text-right border-bottom mb-5">
                    <h3 class="text-black h4 text-uppercase">Cart Totals</h3>
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-md-6">
                    <span class="text-black">Subtotal</span>
                  </div>
                  <div class="col-md-6 text-right">
                    <strong class="text-black">230.00₴</strong>
                  </div>
                </div>
                <div class="row mb-5">
                  <div class="col-md-6">
                    <span class="text-black">Total</span>
                  </div>
                  <div class="col-md-6 text-right">
                    <strong class="text-black">230.00₴</strong>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-12">
                    <button class="btn btn-primary btn-lg py-3 btn-block" onclick="window.location='checkout.html'">Proceed To Checkout</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
@endsection
