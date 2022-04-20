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
                      <tr id="product-{{$product->id}}" class="cart-product" data-price="{{$product->discount_price ? $product->discount_price : $product->price}}" data-currency="{{$product->currency}}">
                        <td class="product-thumbnail">
                          <img src="{{asset('storage/products_images/' . ($product->preview_image ?? $product->main_image))}}" alt="Image" class="img-fluid">
                        </td>
                        <td class="product-name">
                          <h2 class="h5 text-black"><a href="{{route('catalog.single', ['product' => $product->slug])}}">{{$product->name}} ({{$product->size->name}})</a></h2>
                        </td>
                          <td>
                              <div class="input-group mx-auto mb-3" style="max-width: 120px;">
                                  <div class="input-group-prepend">
                                      <button class="btn btn-outline-primary js-btn-minus product-count-minus-btn" data-product="{{$product->id}}" data-href="{{route('ajax.cart.product.minus', ['product_id' => $product->id])}}" type="button">&minus;</button>
                                  </div>
                                  <input type="text" class="form-control text-center product-count" value="{{$cart_array[$product->id]['count']}}" placeholder="" aria-label="Example text with button addon" aria-describedby="button-addon1">
                                  <div class="input-group-append">
                                      <button class="btn btn-outline-primary js-btn-plus product-count-plus-btn" data-product="{{$product->id}}" data-href="{{route('ajax.cart.product.plus', ['product_id' => $product->id])}}" type="button">&plus;</button>
                                  </div>
                              </div>

                          </td>
                        <td>
                            @if($product->discount_price)
                                <s>{{number_format($product->price, 2)}} {{$product->currency}}</s> {{number_format($product->discount_price, 2)}} {{$product->currency}}
                            @else
                                {{number_format($product->price, 2)}} {{$product->currency}}
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
            <div class="col-md-6">
                <div class="row mb-5">

                    @if(!empty($products->all()))
                        <div class="col-md-6 mb-3 mb-md-0 block-update-cart-btn">
                            <button class="btn btn-primary btn-sm btn-block update-cart-btn" data-href="{{route('ajax.cart.update')}}">Update Cart</button>
                        </div>
                    @endif

                    <div class="col-md-6">
                        <a href="{{route('catalog')}}" class="btn btn-outline-primary btn-sm btn-block">Continue Shopping</a>
                    </div>
                </div>
                <div class="row coupon-block">

                    @if($user?->coupon_id)
                        <div class="col-md-12">
                            <label class="text-black h4" for="coupon">Coupon</label>
                            <h3><span class="text-danger">{{$user->coupon->token}}</span> <span class="text-success coupon-percent">{{$user->coupon->percent}}</span><span class="text-success">%</span></h3>
                        </div>
                    @elseif($user)
                        @if($user->hasVerifiedEmail())
                            <div class="col-md-12">
                                <label class="text-black h4" for="coupon">Coupon</label>
                                <p>Enter your coupon code if you have one.</p>
                            </div>
                            <div class="col-md-8 mb-3 mb-md-0 coupon-input-block">
                                <input type="text" class="form-control py-3" id="coupon-token-input" placeholder="Coupon Code" maxlength="30">
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-primary btn-sm apply-coupon-btn" data-href="{{route('ajax.coupon.activate')}}">Apply Coupon</button>
                            </div>
                        @else
                            <div class="col-md-12">
                                <label class="text-black h4" for="coupon">Coupon</label>
                                <h3><a class="h3" href="{{route('profile')}}">To apply coupon you must confirm your email</a></h3>
                            </div>
                        @endif
                    @else
                        <div class="col-md-12">
                            <label class="text-black h4" for="coupon">Coupon</label>
                            <h3><a class="h3" href="{{route('login')}}">To apply coupon you must authorize and confirm your email</a></h3>
                        </div>
                    @endif
                </div>
            </div>
          <div class="col-md-6">
            <div class="row justify-content-end">
              <div class="col-md-7">
                <div class="row">
                  <div class="col-md-12 text-right border-bottom mb-5">
                    <h3 class="text-black h4 text-uppercase">Cart Totals</h3>
                  </div>
                </div>

                  @foreach(config('exchange.currencies', ['UAH']) as $cur)
                      <input type="hidden" class="exchange-rate" data-code="{{$cur}}" data-sale="{{$exchange[$cur]}}">
                  @endforeach

                <div class="row mb-3">
                  <div class="col-md-6">
                    <span class="text-black">Subtotal</span>
                  </div>
                  <div class="col-md-6 text-right">
                    <strong class="text-black"><span class="subtotal-price">0.00</span> {{config('exchange.base', 'UAH')}}</strong>
                  </div>
                </div>
                <div class="row mb-5">
                  <div class="col-md-6">
                    <span class="text-black">Total</span>
                  </div>
                  <div class="col-md-6 text-right">
                    <strong class="text-black"><span class="total-price">0.00</span> <span id="total-currency">{{config('exchange.base', 'UAH')}}</span></strong>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-12">
                      @auth
                          @if($user->hasVerifiedEmail())
                            <a class="btn btn-primary btn-lg py-3 btn-block" href="{{route('cart.buy')}}">Proceed To Checkout</a>
                          @else
                            <h3><a class="h3" href="{{route('profile')}}">To purchase, you need to confirm your email</a></h3>
                          @endif
                      @else
                          <h3><a class="h3" href="{{route('login')}}">To purchase, you need to log in and confirm your email</a></h3>
                      @endauth
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
    <script src="{{asset('js/calculate-costs.js')}}"></script>
@endsection
