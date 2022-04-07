@extends('layouts.index')

@section('title')
    Каталог
@endsection

@section('content')
    <div class="bg-light py-3">
      <div class="container">
        <div class="row">
          <div class="col-md-12 mb-0"><a href="{{route('home')}}">Home</a> <span class="mx-2 mb-0">/</span> <strong class="text-black">Shop</strong></div>
        </div>
      </div>
    </div>

    <div class="site-section">
      <div class="container">

        <div class="row mb-5">
          <div class="col-md-9 order-2">

            <div class="row">
              <div class="col-md-12 mb-5">
                <div class="float-md-left mb-4"><h2 class="text-black h5">Shop All</h2></div>
                <div class="d-flex">
                  <div class="ml-md-auto"></div>
                  <div class="btn-group">
                    <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" id="dropdownMenuReference" data-toggle="dropdown">Reference</button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuReference">
                      <a class="dropdown-item" href="?{{http_build_query(array_merge($get_params, ['order' => 'name_asc']))}}">Name, A to Z</a>
                      <a class="dropdown-item" href="?{{http_build_query(array_merge($get_params, ['order' => 'name_desc']))}}">Name, Z to A</a>
                      <div class="dropdown-divider"></div>
                      <a class="dropdown-item" href="?{{http_build_query(array_merge($get_params, ['order' => 'price_asc']))}}">Price, low to high</a>
                      <a class="dropdown-item" href="?{{http_build_query(array_merge($get_params, ['order' => 'price_desc']))}}">Price, high to low</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row mb-5">

                @if(!empty($catalog->items()))
                    @foreach($catalog as $product)
                        <div class="col-sm-6 col-lg-4 mb-4" data-aos="fade-up">
                            <div class="block-4 text-center border">
                                  <figure class="block-4-image">
                                        <a href="{{route('catalog.single', ['product' => $product->slug])}}"><img src="{{asset('storage/products_images/' . ($product->preview_image ?? $product->main_image))}}" alt="Image placeholder" class="img-thumbnail" style="border-left: none;border-right: none;border-top: none;"></a>
                                  </figure>
                                  <div class="block-4-text p-4">
                                        <h3 style="overflow: hidden; text-overflow: ellipsis;"><a href="{{route('catalog.single', ['product' => $product->slug])}}">{{$product->name}} ({{$product->size->name}})</a></h3>
                                        <p class="mb-0" style="overflow: hidden; text-overflow: ellipsis;">{{$product->subname}}</p>

                                        @if($product->discount_price)
                                            <p class="text-primary font-weight-bold"><span class="text-warning">Discount!</span> <s>{{number_format($product->price, 2)}}₴</s> {{number_format($product->discount_price, 2)}}₴</p>
                                        @else
                                          <p class="text-primary font-weight-bold">{{number_format($product->price, 2)}}₴</p>
                                        @endif
                                  </div>
                            </div>
                      </div>
                    @endforeach
                @else
                    <h3 class="w-100 text-center">Empty</h3>
                @endif

            </div>

              {{$catalog->links()}}

          </div>

          <div class="col-md-3 order-1 mb-5 mb-md-0">

              @if(!empty($filters_data['Categories']))
                <div class="border p-4 rounded mb-4">
                  <h3 class="mb-3 h6 text-uppercase text-black d-block">Categories</h3>
                  <ul class="list-unstyled mb-0">
                      @foreach($filters_data['Categories'] as $category)
                            <li class="mb-1"><a href="?{{http_build_query(array_merge($get_params, ['filt_category' => $category->id]))}}" class="d-flex"><span {{(isset($get_params['filt_category']) && $get_params['filt_category'] == $category->id) ? 'class=text-warning' : ''}}>{{$category->name}}</span> <span class="text-black ml-auto">({{$category->products_count}})</span></a></li>
                      @endforeach
                  </ul>
                </div>
              @endif

            <div class="border p-4 rounded mb-4">


            @if(!empty($filters_data['Sizes']))
              <div class="mb-4">
                <h3 class="mb-3 h6 text-uppercase text-black d-block">Size</h3>
                  <ul class="list-unstyled mb-0">
                      @foreach($filters_data['Sizes'] as $size)
                        <li class="mb-1"><a href="?{{http_build_query(array_merge($get_params, ['filt_size' => $size->id]))}}" class="d-flex"><span {{(isset($get_params['filt_size']) && $get_params['filt_size'] == $size->id) ? 'class=text-warning' : ''}}>{{$size->name}}</span> <span class="text-black ml-auto">({{$size->products_count}})</span></a></li>
                      @endforeach
                  </ul>
              </div>
            @endif

            @if(!empty($filters_data['Colors']))
              <div class="mb-4">
                <h3 class="mb-3 h6 text-uppercase text-black d-block">Color</h3>

                @foreach($filters_data['Colors'] as $color)
                    <a href="?{{http_build_query(array_merge($get_params, ['filt_color' => $color->id]))}}" class="d-flex color-item align-items-center" >
                      <span class="color d-inline-block rounded-circle mr-2" style="background-color: {{$color->hex}}"></span> <span {{(isset($get_params['filt_color']) && $get_params['filt_color'] == $color->id) ? 'class=text-warning' : 'class=text-black'}}>{{$color->name}} ({{$color->products_count}})</span>
                    </a>
                @endforeach
              </div>
            @endif
                <a href="{{route('catalog')}}" class="btn btn-primary">Reset filters</a>

            </div>
          </div>
        </div>
      </div>
    </div>
@endsection
