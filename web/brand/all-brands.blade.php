@extends('layouts.app')


@section('content')

<!-- ========== MAIN CONTENT ========== -->
        <main id="content" role="main">
            <!-- breadcrumb -->
            <div class="bg-gray-13 bg-md-transparent">
                <div class="container">
                    <!-- breadcrumb -->
                    <div class="my-md-3">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-3 flex-nowrap flex-xl-wrap overflow-auto overflow-xl-visble">
                                <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1"><a href="{{route('home')}}">Home</a></li>
                                <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1 active" aria-current="page">All Brands</li>
                            </ol>
                        </nav>
                    </div>
                    <!-- End breadcrumb -->
                </div>
            </div>
            <!-- End breadcrumb -->

            <div class="container">
                <div class="row">
                    <div class="col-xl-12 col-wd-9gdot5">
                        <div class="d-flex justify-content-between align-items-center border-bottom border-color-1 flex-lg-nowrap flex-wrap mb-4">
                            <h3 class="section-title section-title__full mb-0 pb-2 font-size-22">All Brands</h3>
                        </div>
                        <ul class="row list-unstyled products-group no-gutters mb-6">
                        @foreach($brands as $c)
                        	
                            <li class="col-6 col-md-2 col-xl-1gdot7 product-item">
                                <div class="product-item__outer h-100 w-100">
                                <div class="product-item__inner px-xl-4 p-3">
                                    <div class="product-item__body pb-xl-2">
                                            <div class="mb-2">
                                                <a href="{{ route('brands.products', $c->id) }}" class="d-block text-center">
                                                	@if($c->image)
                                                	<img class="img-fluid" src="{{env('BACKEND_IMAGE_URL').$c->image}}" alt="">
                                                	@else
                                                	<img class="img-fluid" src="{{asset('assets/img/logo.png')}}" alt="">
                                                	@endif

                                                </a>
                                            </div>
                                            <h5 class="text-center mb-1 product-item__title"><a href="#" class="font-size-15 text-gray-90">{{$c->title}}</a></h5>
                                        </div>
                                    </div>
                                </div>
                            </li>
                    @endforeach
                        </ul>
                        <div class="container">
                <!-- Prodcut-cards-carousel -->
                <div class="space-top-2">
                    <div class=" d-flex justify-content-between border-bottom border-color-1 flex-md-nowrap flex-wrap border-sm-bottom-0">
                        <h3 class="section-title mb-0 pb-2 font-size-22">Bestsellers</h3>
                        <ul class="nav nav-pills mb-2 pt-3 pt-md-0 mb-0 border-top border-color-1 border-md-top-0 align-items-center font-size-15 font-size-15-md flex-nowrap flex-md-wrap overflow-auto overflow-md-visble">
                            <li class="nav-item flex-shrink-0 flex-md-shrink-1">
                                <a class="text-gray-90 btn btn-outline-primary border-width-2 rounded-pill py-1 px-4 font-size-15 text-lh-19 font-size-15-md" href="#!">Top 20</a>
                            </li>
                        </ul>
                    </div>
                    <div class="js-slick-carousel u-slick u-slick--gutters-2 overflow-hidden u-slick-overflow-visble pt-3 pb-6"
                        data-pagi-classes="text-center right-0 bottom-1 left-0 u-slick__pagination u-slick__pagination--long mb-0 z-index-n1 mt-4">
                        @foreach($bestSellers->chunk(6) as $products)
                        <div class="js-slide">
                            <ul class="row list-unstyled products-group no-gutters mb-0 overflow-visible">                           
                            	@foreach($products as $bS)
                                <li class="col-wd-3 col-md-4 product-item product-item__card pb-2 mb-2 pb-md-0 mb-md-0 border-bottom border-md-bottom-0">
                                    <div class="product-item__outer h-100">
                                        <div class="product-item__inner p-md-3 row no-gutters">
                                            <div class="col col-lg-auto product-media-left">
                                                <a href="{{ route('product.detail', $bS->id) }}" class="max-width-150 d-block"><img class="img-fluid h150"  src="{{env('BACKEND_IMAGE_URL').($bS->thumbnail ? $bS->thumbnail->url : '')}}" alt="{{$bS->title}}"></a>
                                            </div>
                                            <div class="col product-item__body pl-2 pl-lg-3 mr-xl-2 mr-wd-1">
                                                <div class="mb-4">
                                                    <div class="mb-2"><a href="{{ route('product.detail', $bS->id) }}" class="font-size-12 text-gray-5">{{$bS->brand->title}}</a></div>
                                                    <h5 class="product-item__title"><a href="{{ route('product.detail', $bS->id) }}" class="text-blue font-weight-bold">{{$bS->title}}</a></h5>
                                                </div>
                                                <div class="flex-center-between mb-3">
                                                    <div class="prodcut-price">
                                                        <div class="text-gray-100">Rs. {{$bS->price}}</div>
                                                    </div>
                                                    <div class="d-none d-xl-block prodcut-add-cart">
                                                        <a href="#" class="btn-add-cart btn-primary transition-3d-hover"><i class="ec ec-add-to-cart"></i></a>
                                                    </div>
                                                </div>
                                                <div class="product-item__footer">
                                                    <div class="border-top pt-2 flex-center-between flex-wrap">
                                                        <a href="#" class="text-gray-6 font-size-13"><i class="ec ec-compare mr-1 font-size-15"></i> Compare</a>
                                                        <a href="#" class="text-gray-6 font-size-13"><i class="ec ec-favorites mr-1 font-size-15"></i> Wishlist</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li> 
                                @endforeach                              
                            </ul>
                        </div>
                         @endforeach
                    </div>
                </div>
                <!-- End Prodcut-cards-carousel -->
                <!-- Full banner -->
                <div class="mb-6">
                    <a href="#" class="d-block text-gray-90">
                        <div class="" style="background-image: url(assets/img/1400X206/img1.jpg);">
                            <div class="space-top-2-md p-4 pt-6 pt-md-8 pt-lg-6 pt-xl-8 pb-lg-4 px-xl-8 px-lg-6">
                                <div class="flex-horizontal-center mt-lg-3 mt-xl-0 overflow-auto overflow-md-visble">
                                    <h1 class="text-lh-38 font-size-32 font-weight-light mb-0 flex-shrink-0 flex-md-shrink-1">SHOP AND <strong>SAVE BIG</strong> ON HARD BALL ACCESSORIES</h1>
                                    <div class="ml-5 flex-content-center flex-shrink-0">
                                        <div class="bg-primary rounded-lg px-6 py-2">
                                            <em class="font-size-14 font-weight-light">STARTING AT</em>
                                            <div class="font-size-30 font-weight-bold text-lh-1">
                                                <sup class="">Rs.</sup>790
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <!-- End Full banner -->
                <!-- Recently viewed -->
                <div class="mb-6">
                    <div class="position-relative">
                        <div class="border-bottom border-color-1 mb-2">
                            <h3 class="section-title mb-0 pb-2 font-size-22">Top rated Products</h3>
                        </div>
                        <div class="js-slick-carousel u-slick position-static overflow-hidden u-slick-overflow-visble pb-7 pt-2 px-1"
                            data-pagi-classes="text-center right-0 bottom-1 left-0 u-slick__pagination u-slick__pagination--long mb-0 z-index-n1 mt-3 mt-md-0"
                            data-slides-show="7"
                            data-slides-scroll="1"
                            data-arrows-classes="position-absolute top-0 font-size-17 u-slick__arrow-normal top-10"
                            data-arrow-left-classes="fa fa-angle-left right-1"
                            data-arrow-right-classes="fa fa-angle-right right-0"
                            data-responsive='[{
                              "breakpoint": 1400,
                              "settings": {
                                "slidesToShow": 6
                              }
                            }, {
                                "breakpoint": 1200,
                                "settings": {
                                  "slidesToShow": 4
                                }
                            }, {
                              "breakpoint": 992,
                              "settings": {
                                "slidesToShow": 3
                              }
                            }, {
                              "breakpoint": 768,
                              "settings": {
                                "slidesToShow": 2
                              }
                            }, {
                              "breakpoint": 554,
                              "settings": {
                                "slidesToShow": 2
                              }
                            }]'>
                            @foreach($bestSellers as $rV)
                            <div class="js-slide products-group">
                                <div class="product-item">
                                    <div class="product-item__outer h-100">
                                        <div class="product-item__inner px-wd-4 p-2 p-md-3">
                                            <div class="product-item__body pb-xl-2">
                                                <div class="mb-2"><a href="#" class="font-size-12 text-gray-5">{{$rV->brand->title}}</a></div>
                                                <h5 class="mb-1 product-item__title"><a href="{{ route('product.detail', $rV->id) }}" class="text-blue font-weight-bold">{{$rV->title}}</a></h5>
                                                <div class="mb-2">
                                                    <a href="{{ route('product.detail', $rV->id) }}" class="d-block text-center"><img class="img-fluid h162" src="{{env('BACKEND_IMAGE_URL').($rV->thumbnail ? $rV->thumbnail->url : '')}}" alt="{{$rV->title}}"></a>
                                                </div>
                                                <div class="flex-center-between mb-1">
                                                    <div class="prodcut-price">
                                                        <div class="text-gray-100">Rs. {{$rV->price}}</div>
                                                    </div>
                                                    <div class="d-none d-xl-block prodcut-add-cart">
                                                        <a href="{{route('cart')}}" class="btn-add-cart btn-primary transition-3d-hover"><i class="ec ec-add-to-cart"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="product-item__footer">
                                                <div class="border-top pt-2 flex-center-between flex-wrap">
                                                    <a href="#" class="text-gray-6 font-size-13"><i class="ec ec-compare mr-1 font-size-15"></i> Compare</a>
                                                    <a href="#" class="text-gray-6 font-size-13"><i class="ec ec-favorites mr-1 font-size-15"></i> Wishlist</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <!-- End Recently viewed -->
            </div>
                       
                    </div>
                </div>
            </div>
        </main>
        <!-- ========== END MAIN CONTENT ========== -->

@stop

@section('js')

<script>
	$(document).ready(function() {
  $('#basicsCollapseOne').removeClass('show');
});
</script>

@stop