@extends('layouts.app')



@section('content')
    <div class="pos-content-container" id="pos-content-container">
        <div><div class="pos-home-main" style="    width: 98%;">
            <div class="pos-categories">
                    <ul class="category-section"><li title="All" class="related_category focus-category" data-brand-id="0">
                            All
                        </li>
                        <ul class="parent-categories">
                            @foreach($result->data->menuBrands as $b)
                                <li data-brand-id="{{$b->id}}" title="{{$b->title}}" class="related_category">
                                    {{$b->title}}
                                </li>
                            @endforeach


                        </ul>
                        <!-- All Categories -->

                        <!----></ul>
                </div>

                <div class="pos-product-container" style="height: fit-content;">
                    <div class="product-list product-grid-5" id="product-list">

                        @foreach($result->data->featuredProducts as $pro)
                        
                        @if($pro->variants)

                            @foreach($pro->variants as $v)
                                <div>
                                    

                                        <div class="product-layout" onclick="productVariantDetail({{$v->id}})">


                                                    <div class="product-thumb">

                                                        <div class="ribbon-wrapper">

                                                            <div class="ribbon">{{$v->available_stock}}</div>
                                                        </div>
                                                       
                                                        @if($pro->thumbnail)

                                                            <img src="{{env('BACKEND_IMAGE_URL').$pro->thumbnail->url}}">
                                            @else
                                <img src="{{asset('images/download.webp')}}">

                                                        @endif
                                                    </div>



                                                    <div class="product-name"  title="{{$pro->title}}">
                                                         {{strlen($pro->title) > 40 ? substr($pro->title, 0, 40) . '...' : $pro->title}}<br>
                                                        <span style=" color: #db324d;"> {{$v->shade ? $v->shade : ''}}  {{$v->size ? ' , '.$v->size : ''}} </span>
                                                    </div>

                                                   
                                                        <div class="product-price">
                                                <span class="price">
                                                       Rs. {{number_format($v->additional_price)}}
                                                </span>

                                                        </div>
                                                    

                                                </div>
                                        </div>
                                    @endforeach
                                    
                                    @else
                                    <div>
                                    

                                        <div class="product-layout" onclick="productDetail({{$pro->id}})">


                                                    <div class="product-thumb">

                                                        <div class="ribbon-wrapper">

                                                            <div class="ribbon">{{$pro->available_stock}}</div>
                                                        </div>
                                                       
                                                        @if($pro->thumbnail)

                                                            <img src="{{env('BACKEND_IMAGE_URL').$pro->thumbnail->url}}">
                                                            @else
                                <img src="{{asset('images/download.webp')}}">
    
                                                        @endif
                                                    </div>



                                                    <div class="product-name"  title="{{$pro->title}}">
                                                         {{strlen($pro->title) > 40 ? substr($pro->title, 0, 40) . '...' : $pro->title}}<br>
                                               
                                                    </div>

                                                    @if($pro->discount_status)
                                                        <div class="product-price"><span class="price price-cross">
                                            {{number_format($pro->price)}}
                                        </span> <span class="special-price">
                                            {{number_format($pro->price - $pro->discount_amount)}}
                                        </span></div>
                                                    @else
                                                        <div class="product-price">
                                                <span class="price">
                                                       Rs. {{number_format($pro->price)}}
                                                </span>

                                                        </div>
                                                    @endif


                                                </div>
                                        </div>
                                    
                                    @endif

                                    @endforeach


                                    <!----> <!----> <!----> <!----> <!---->
                                </div>


                    </div>
                </div>

        </div>
    </div>
    </div>
    </div>

    <input type="hidden" name="customer_id" id="customer_id" value="1">
    <input type="hidden" id="route" name="route" value="2">

    <div style="display: none" id="product-modal">
        <div id="addCustomer">
            <div class="pos-modal-overlay"></div>
            <div class="pos-modal-container" style="width:700px;">
                <div class="modal-header">
                    <h4> Product Detail</h4>
                    <i onclick="hideProductModal()" class="icon remove-icon"></i>
                </div> <div class="modal-body" id="product-modal-body">

                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        function hideProductModal() {
            document.getElementById('product-modal').style.display = 'none';
        }
    </script>
    @stop


