
@if(count($products) == 0)
    <span style="text-align: center;color: red"> No result found...</span>
    @endif
@foreach($products as $pro)

@if(count($pro->variants) > 0)
    @foreach($pro->variants as $v)
        <div style="display:inline-block;width:250px">
            
        <div class="product-layout" style="display:inline-block; cursor:pointer" onclick="addNewProduct({{$pro->id}},{{$v->id}})">


                <div class="product-thumb">

                   

                  
                    @if($pro->thumbnail)

                        <img width="150px" height="150px" src="{{asset('storage/'.$pro->thumbnail->url)}}">
                        @endif
                </div>



                <div class="product-name"  title="{{$pro->title}}"><h6>
                    {{strlen($pro->title) > 40 ? substr($pro->title, 0, 40) . '...' : $pro->title}}</h6>
                    <span style=" color: #db324d;"><h6> {{$v->shade ? $v->shade : ''}}  {{$v->size ? ' - '.$v->size : ''}} </h6></span>
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
                                                       {{number_format($pro->price)}}
                                                </span>

                    </div>
                @endif


            </div>
        </div>
    @endforeach
    
    @else
    
    <div style="display:inline-block;width:250px">
    
    <div class="product-layout" style="display:inline-block;cursor:pointer" onclick="addNewProduct({{$pro->id}},0)">

                <div class="product-thumb abv">


                    @if($pro->thumbnail)

                        <img width="150px" height="150px" src="{{asset('storage/'.$pro->thumbnail->url)}}">
                    @endif
                </div>



                <div class="product-name"  title="{{$pro->title}}"><h6>
                    {{strlen($pro->title) > 40 ? substr($pro->title, 0, 40) . '...' : $pro->title}}</h6>
                 
                </div>

                @if($pro->discount_status)
                    <div class="product-price"><span class="price price-cross">
                                            {{number_format($pro->price)}}
                                        </span> <span class="special-price">
                                           Price:  {{number_format($pro->price - $pro->discount_amount)}}
                                        </span></div>
                @else
                    <div class="product-price">
                                                <span class="price">
                                                    Price:   {{number_format($pro->price)}}
                                                </span>

                    </div>
                @endif


            </div>
        </div>
    
    
    
    @endif

@endforeach

