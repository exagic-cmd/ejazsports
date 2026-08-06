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
                        <h2 style="margin-top: 60px">{{$promotion->name}}</h2>
                    </div>
                </div>
                <!--  col.// -->
                <div class="col-xl col-lg">

                    <p><span class="badge rounded-pill {{($promotion->status) ? 'alert-success' : 'alert-danger'}}">{{($promotion->status) ? 'Active' : 'InActive'}}</span>

                    </p>
                </div>
                <!--  col.// -->
                <div class="col-xl-4 text-md-end">
                    @can('Edit Promotion')
                        <a class="dropdown-item btn btn-primary d-inline" href="{{route('promotion.edit',$promotion->id)}}">Edit info</a>
                    @endcan


                </div>
                <!--  col.// -->
            </div>
            <!-- card-body.// -->
            <hr class="my-4">
            <div class="row g-4">

                <!--  col.// -->
                <div class="col-sm-6 col-lg-4 col-xl-3">
                    <h6>Basic</h6>
                    <p>
                        <b>Web Count : </b>{{$promotion->web_count}} pics in a row<br>
                        <b>Mobile Count :  </b> {{$promotion->mobile_count}} pics in a row<br>
                        <b>Serial #: </b> {{$promotion->serial_no}} <br>
                    </p>
                </div>
                <!--  col.// -->
                @foreach($promotion->banners as $banner)
                <div class="col-sm-6 col-lg-4 col-xl-3">
                    <div class="col p-1">
                        <div class="card card-product-grid" >
                            <a href="#" class="img-wrap" > <img style="height: 350px;object-fit: contain;" src="{{asset('storage/'.$banner->image)}}" alt="Brand"> </a>

                            <div class="info-wrap  text-center font-sm">
                                <a href="{{$banner->url}}" target="_blank" class="title text-truncate"><i class="icon material-icons md-link"></i> {{$banner->url}}</a>
                                <a href="#" class="title text-truncate">Order # <b>{{$banner->serial_no}}</b></a>
                            </div>
                        </div>
                        <!-- card-product  end// -->
                    </div>

                </div>
                <!--  col.// -->
                @endforeach

                <!--  col.// -->
            </div>
            <!--  row.// -->
        </div>
        <!--  card-body.// -->
    </div>

@stop

@section('js')
    <script>

    </script>


@stop
