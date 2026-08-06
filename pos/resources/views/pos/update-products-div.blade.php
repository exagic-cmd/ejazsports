<?php $products = (array) $result->data->products;?>
@if(count($products) == 0)
    <span style="text-align: center;color: red"> No result found...</span>
    @endif
@foreach($result->data->products as $pro)

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
                    <span style=" color: #db324d;"> {{$v->shade ? $v->shade : ''}}  {{$v->size ? ' - '.$v->size : ''}} </span>
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
<input type="hidden" id="route" name="route" value="2">
