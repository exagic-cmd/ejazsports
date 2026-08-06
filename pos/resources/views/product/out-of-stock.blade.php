@extends('layouts.app')



@section('content')
    <div class="pos-content-container" id="pos-content-container">
        <div><div class="pos-home-main" style="    width: 98%;">

                <div class="pos-product-container" style="height: fit-content;">
                    <div class="product-list product-grid-5" id="product-list">

                        @foreach($result->data->featuredProducts as $pro)

                            @foreach($pro->variants as $v)
                                <?php $storeVariants = (array) $result->data->storeVariants;?>
                                @if($storeVariants[$v->id] == 0)
                                <div>


                                    <div class="product-layout" >


                                        <div class="product-thumb">

                                            <div class="ribbon-wrapper">

                                                <div class="ribbon">{{$storeVariants[$v->id]}}</div>
                                            </div>


                                            @if($v->image)
                                                <img src="{{env('BACKEND_IMAGE_URL').$v->image}}">
                                            @elseif($pro->thumbnail)

                                                <img src="{{env('BACKEND_IMAGE_URL').$pro->thumbnail->url}}">

                                            @else
                                                <img src="{{$path}}">
                                            @endif
                                        </div>



                                        <div class="product-name"  title="{{$pro->title}}">
                                            {{strlen($pro->product_heading) > 40 ? substr($pro->product_heading, 0, 40) . '...' : $pro->product_heading}}<br>
                                            <span style=" color: #db324d;"> {{$v->shade ? $v->shade : ''}}  {{$v->size ? ' - '.$v->size : ''}} </span>
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
                                @endif
                        @endforeach

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
@stop

@section('js')
    <script type="text/javascript">
    </script>
@stop


