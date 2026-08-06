@extends('layouts.app')

@section('css')
    <style>
        .img-wrap img:hover{
            -ms-transform: scale(1.2); /* IE 9 */
            -webkit-transform: scale(1.2); /* Safari 3-8 */
            transform: scale(1.2);
        }
        .img-wrap img {
            transition: 1s;
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    @stop
@section('content')

    <div class="content-header">
        <a href="javascript:history.back()"><i class="material-icons md-arrow_back"></i> Go back </a>
    </div>
    <div class="card mb-4">
        <div class="card-header bg-brand-2" style="height: 150px"></div>
        <div class="card-body">
            <div class="row">
                <div class="col-xl col-lg flex-grow-0" style="flex-basis: 230px">
                    <div class="img-thumbnail shadow w-100 bg-white position-relative text-center" style="height: 190px; width: 200px; margin-top: -120px">
                        <img src="{{asset('storage/'.$brand->image)}}" style="max-width: 80%;!important;" class="center-xy img-fluid" alt="Logo Brand">
                    </div>
                </div>
                <!--  col.// -->
                <div class="col-xl col-lg">
                    <h3>{{$brand->title}}</h3>
                    <p><span class="badge rounded-pill {{($brand->status) ? 'alert-success' : 'alert-danger'}}">{{($brand->status) ? 'Active' : 'InActive'}}</span>
                       @if($brand->is_premium)
                        <span class="badge rounded-pill alert-success">Premium</span>
                        @endif
                        @if($brand->is_featured)
                        <span class="badge rounded-pill alert-success">Featured</span>
                            @endif
                        @if($brand->show_in_menu)
                            <span class="badge rounded-pill alert-success">Shown In Menu</span>
                        @endif
                    </p>
                </div>
                <!--  col.// -->
                <div class="col-xl-4 text-md-end">
                    @can('Edit Brand')
                    <a class="dropdown-item btn btn-primary d-inline" href="{{route('brands.edit',$brand->id)}}">Edit info</a>
                    @endcan

                    @can('Delete Brand')
                    <form @class('d-inline') onsubmit="return confirm('Do you really want to do this?');" id="delete-form" action="{{ route('brands.destroy',$brand->id) }}" method="POST">
                        @csrf
                        @method('DELETE')

                        <button style="width: min-content;" class=" btn btn-instagram d-inline"  type="submit">Delete</button>
                    </form>
                        @endcan

                </div>
                <!--  col.// -->
            </div>
            <!-- card-body.// -->
            <hr class="my-4">
            <div class="row g-4">
                <div class="col-md-12 col-lg-4 col-xl-2">
                    <article class="box">
                        <p class="mb-0 text-muted d-inline">Total products: <h5 class="text-success text-center d-inline">{{number_format($totalProducts)}}</h5></p>
                        <p class="mb-0 text-muted d-inline">Active products: <h5 class="text-success text-center d-inline">{{number_format($activeProducts)}}</h5></p>
                        <p class="mb-0 text-muted d-inline">InActive products: <h5 class="text-success text-center d-inline">{{number_format($inActiveProducts)}}</h5></p>
                        <p class="mb-0 text-muted">Total Sales: <h5 class="text-success mb-0 text-center">PKR 0</h5></p>

                    </article>
                </div>
                <!--  col.// -->
                <div class="col-sm-6 col-lg-4 col-xl-3">
                    <h6>Basic</h6>
                    <p>
                        <b>Menu Text: </b>{{$brand->menu_text}} <br>
                        <b>Brand Heading: </b> {{$brand->brand_heading}} <br>
                        <b>Slug: </b> {{$brand->slug}} <br>
                        <b>Serial #: </b> {{$brand->serial_no}} <br>
                    </p>
                </div>
                <!--  col.// -->
                <div class="col-sm-6 col-lg-4 col-xl-3">
                    <h6>Seo</h6>
                    <p>
                        <b>Meta Description: </b> {{$brand->meta_description}}
                    </p>
                    <p>

                        <b>Keywords: </b> {{$brand->keywords}} <br>

                    </p>
                </div>
                <!--  col.// -->
                <div class="col-sm-6 col-xl-4 text-xl-end" style=" display: grid;grid-template-columns: 1fr 1fr 1fr;">
                    @if($brand->banner_image)
                    <div class="col p-1">
                        <div class="card card-product-grid" style="width: 115px;">
                            <a href="#" class="img-wrap" style="min-height: 124px;"> <img style="height: 124px;object-fit: contain;" src="{{asset('storage/'.$brand->banner_image)}}" alt="Brand"> </a>
                            <div class="info-wrap  text-center font-sm">
                                <a href="#" class="title text-truncate">Banner</a>
                                 </div>
                        </div>
                        <!-- card-product  end// -->
                    </div>
                    @endif
                    @if($brand->mobile_banner_image)
                            <div class="col p-1">
                                <div class="card card-product-grid" style="width: 115px;">
                                    <a href="#" class="img-wrap" style="min-height: 124px;"> <img title="Mobile Banner Image" style="height: 124px;object-fit: contain;" src="{{asset('storage/'.$brand->mobile_banner_image)}}" alt="Brand"> </a>
                                    <div class="info-wrap  text-center font-sm">
                                        <a href="#" class="title text-truncate">Mobile Banner</a>
                                    </div>
                                </div>
                                <!-- card-product  end// -->
                            </div>
                        @endif
                        @if($brand->featured_image)
                            <div class="col p-1">
                                <div class="card card-product-grid" style="width: 115px;">
                                    <a href="#" class="img-wrap" style="min-height: 124px;"> <img title="Featured Image" style="height: 124px;object-fit: contain;" src="{{asset('storage/'.$brand->featured_image)}}" alt="Brand"> </a>
                                    <div class="info-wrap text-center font-sm">
                                        <a href="#" class="title text-truncate">Featured Image</a>
                                    </div>
                                </div>
                                <!-- card-product  end// -->
                            </div>
                        @endif
                        @if($brand->premium_image)
                            <div class="col p-1">
                                <div class="card card-product-grid" style="width: 115px;">
                                    <a href="#" class="img-wrap" style="min-height: 124px;"> <img title="Premium Image" style="height: 124px;object-fit: contain;" src="{{asset('storage/'.$brand->premium_image)}}" alt="Brand"> </a>
                                    <div class="info-wrap text-center font-sm">
                                        <a href="#" class="title text-truncate">Premium Image</a>
                                    </div>
                                </div>
                                <!-- card-product  end// -->
                            </div>
                        @endif

                </div>
                <!--  col.// -->
            </div>
            <!--  row.// -->
        </div>
        <!--  card-body.// -->
    </div>
    <!--  card.// -->
    <div class="card mb-4">
        <div class="card-body">
            <h3 class="card-title d-inline">Products by Brand</h3> <div class="d-inline " style="margin-left: 54%"><input style="width: 25%;" type="text" class="form-control d-inline" placeholder="Search any product.." name="search" id="search"></div>
            <div class="row" id="result">

                @foreach($brandProducts as $pro)
                <div class="col-xl-2 col-lg-3 col-md-6">
                    <div class="card card-product-grid">
                        <a href="#" class="img-wrap"> <img src="{{asset('storage/default.jpeg')}}" alt="Product"> </a>
                        <div class="info-wrap">
                            <a target="_blank" href="{{route('products.show',$pro->id)}}" title="{{$pro->title}}" class="title text-truncate">{{$pro->title}}</a>
                            <div class="price mt-1">{{number_format($pro->price)}} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="badge rounded-pill {{($pro->status) ? 'alert-success' : 'alert-danger'}}">{{($pro->status) ? 'Active' : 'InActive'}}</span></div>
                            <!-- price-wrap.// -->
                        </div>
                    </div>
                    <!-- card-product  end// -->
                </div>
                @endforeach

                <!-- col.// -->
                <!-- col.// -->
            </div>
            <!-- row.// -->
        </div>
        <!--  card-body.// -->
    </div>
    <!--  card.// -->
    <div class="pagination-area mt-30 mb-50">
        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-start">
                {{$brandProducts->links()}}
            </ul>
        </nav>
    </div>
@stop

@section('js')
    <script>

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#search').keyup(function (e){
                e.preventDefault();

                var value = $("input[name=search]").val();
                var brand_id = {{$brand->id}};
                if(value.length > 2) {

                    $.ajax({
                        type:'POST',
                        url:"{{ route('brands.search-product') }}",
                        data:{value:value,brand_id:brand_id},
                        success:function(data){
                            document.getElementById('result').innerHTML = data;

                            $('.pagination').addClass('d-none');
                        }
                    });
                }
                else if(value.length == 0) {
                    $.ajax({
                        type:'POST',
                        url:"{{ route('brands.search-product') }}",
                        data:{value:null,brand_id:brand_id},
                        success:function(data){
                            document.getElementById('result').innerHTML = data;

                            $('.pagination').addClass('d-none');
                        }
                    });
                }


        });
    </script>


    @stop
