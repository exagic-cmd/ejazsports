@extends('layouts.app')

@section('content')

        <div class="content-header">
            <div>
                <h2 class="content-title card-title">Deals List</h2>
                <p>latest deal details.</p>
            </div>
            <div>

                <a href="{{route('deals.create')}}" class="btn btn-primary btn-sm rounded">Create new</a>
            </div>
        </div>
        <div class="card mb-4">
            <header class="card-header">
                <div class="row align-items-center">

                    <div class="col-md-3 col-12 me-auto mb-md-0 mb-3">
                        <select class="form-select select2" id="category_id">
                            <option selected="" value="">All category</option>
                            @foreach($categories as $cat)
                            <option value="{{$cat->id}}">{{$cat->category_heading}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-12 me-auto mb-md-0 mb-3">
                        <select class="form-select select2" id="brand_id">
                            <option selected="" value="">All Brand</option>
                            @foreach($brands as $b)
                                <option value="{{$b->id}}">{{$b->brand_heading}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-12 me-auto mb-md-0 mb-3">
                        <select class="form-select select2" id="status">
                            <option selected="" value="">Status</option>
                            <option value="1">Active</option>
                            <option value="0">Disabled</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-12 me-auto mb-md-0 mb-3">
                        <button type="button" class="form-control btn btn-primary search" >Search</button>
                    </div>
                </div>
            </header>
            <!-- card-header end// -->
            <div class="card-body" id="result">

                @foreach($deals as $deal)
                <article class="itemlist">
                    <div class="row align-items-center">
                        <div class="col col-check flex-grow-0">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox">
                            </div>
                        </div>
                        <div class="col-lg-2 col-sm-2 col-4 flex-grow-1 col-name">
                            <a class="itemside" href="{{route('deals.show',$deal->id)}}">
                                <div class="left">
                                    @if($deal->thumbnail)
                                    <img src="{{asset('storage/'.$deal->thumbnail->url)}}" class="img-sm img-thumbnail" alt="Item">
                                    @else
                                        <img src ="{{asset('storage/default.jpeg')}}" class="img-sm img-thumbnail" alt="Item">

                                    @endif
                                </div>
                                <div class="info">
                                    <h6 class="mb-0">{{$deal->title}}</h6><br>
                                    <small >{{$deal->available_stock}} (stock)</small>
                                    @if($deal->have_variants)
                                        <small class="right-0"> <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{count($deal->variants)}}</b> (variants)</small>
                                        @endif
                                </div>
                            </a>
                        </div>


                        <div class="col-lg-2 col-sm-2 col-4 col-status">
                            <span>@if($deal->brands)  @foreach($deal->brands as $brand) @if($loop->last) <b> @if($brand->brand){{$brand->brand->title }} @endif </b> @else <b>  @if($brand->brand) {{$brand->brand->title }} </b> , @endif @endif @endforeach @endif</span>
                        </div>
                        <div class="col-lg-2 col-sm-2 col-4 col-date">
                            <span>@if($deal->categories)  @foreach($deal->categories as $category) @if($loop->last) <b> @if($category->category){{$category->category->title }} @endif </b> @else <b>  @if($category->category) {{$category->category->title }} </b> , @endif @endif @endforeach @endif</span>
                        </div>
                        <div class="col-lg-2 col-sm-2 col-4 col-date">
                            <span>@if($deal->products)  @foreach($deal->products as $product) @if($loop->last) <b> @if($product->product){{$product->product->title }} @endif </b> @else <b>  @if($product->product) {{$product->product->title }} </b> , @endif @endif @endforeach @endif</span>
                        </div>

                        <div class="col-lg-1 col-sm-2 col-4 col-price"><span>
                             <strike style="color: #c4bbbb;">{{number_format($deal->price)}}</strike><br> {{number_format($deal->price - $deal->discount_amount)}}
                            </span></div>

                        <div class="col-lg-1 col-sm-2 col-4 col-status">
                            <span class="badge rounded-pill {{$deal->status ?'alert-success' : 'alert-danger'}}">@if($deal->status == 1)
                                    Published
                                @elseif($deal->status == 2)
                                    Dis continue
                                @else
                                    Un Published
                                @endif</span>
                        </div>
                        <div class="col-lg-1 col-sm-2 col-4 col-action text-end">
{{--                            <a href="#" class="btn btn-sm font-sm rounded btn-brand"> <i class="material-icons md-edit"></i> Edit </a>--}}
                            <a href="{{route('deals.show',$deal->id)}}" class="btn btn-sm font-sm btn-light rounded"> <i class="material-icons md-view_carousel"></i> Detail </a>
                        </div>
                    </div>
                    <!-- row .// -->
                </article>

                @endforeach

                <!-- itemlist  .// -->
            </div>
            <!-- card-body end// -->
        </div>
        <!-- card end// -->
        <div class="pagination-area mt-30 mb-50">
            <nav aria-label="Page navigation example" id="link">
                {{$deals->links()}}
            </nav>
        </div>


    @stop

@section('js')
<script>
    $('.select2').select2();
    $(document).on('click','.search',function(e) {

        category_id = $('#category_id').val();
        brand_id = $('#brand_id').val();
        status = $('#status').val();

        if(category_id || brand_id || status) {

            $.confirm({
                title: 'Product Search!',
                content: 'Are you sure you want to do this!',
                buttons: {
                    confirm: function () {
                        $.ajax({
                            url: "{{route('product.search')}}",
                            type: 'GET',
                            data: {category_id: category_id,brand_id:brand_id, status:status},
                            success: function (data) {
                                document.getElementById('result').innerHTML = data;
                                document.getElementById('link').innerHTML = '';
                            }
                        });
                    },
                    cancel: function () {
                    }
                }
            });
        }
        else {
            toastr.warning('Please select any option');
        }
    });
   </script>
 @stop
