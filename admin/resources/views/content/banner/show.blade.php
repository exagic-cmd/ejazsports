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
                        <img src="{{asset('storage/'.$banner->web_image)}}" style="max-width: 80%;!important;" class="center-xy img-fluid" alt="Logo Brand">
                    </div>
                </div>
                <!--  col.// -->
                <div class="col-xl col-lg">

                    <p><span class="badge rounded-pill {{($banner->status) ? 'alert-success' : 'alert-danger'}}">{{($banner->status) ? 'Active' : 'InActive'}}</span>

                    </p>
                </div>
                <!--  col.// -->
                <div class="col-xl-4 text-md-end">
                    @can('Edit Banner')
                    <a class="dropdown-item btn btn-primary d-inline" href="{{route('banners.edit',$banner->id)}}">Edit info</a>
                    @endcan

                    @can('Delete Banner')
                    <form @class('d-inline') onsubmit="return confirm('Do you really want to do this?');" id="delete-form" action="{{ route('banners.destroy',$banner->id) }}" method="POST">
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

                <!--  col.// -->
                <div class="col-sm-6 col-lg-4 col-xl-3">
                    <h6>Basic</h6>
                    <p>
                        <b>Web Heading : </b>{{$banner->web_heading}} <br>
                        <b>Web Sub Heading :  </b> {{$banner->web_sub_heading}} <br>
                        <b>Serial #: </b> {{$banner->serial_no}} <br>
                    </p>
                </div>
                <!--  col.// -->
                <div class="col-sm-6 col-lg-4 col-xl-3">
                    <h6>Basic</h6>
                    <p>
                        <b>Mobile Heading : </b> {{$banner->mobile_heading}}
                    </p>
                    <p>

                        <b>Mobile Sub Heading : </b> {{$banner->mobile_sub_heading}} <br>

                    </p>
                </div>
                <!--  col.// -->
                <div class="col-sm-6 col-xl-6 text-xl-end" style=" display: grid;grid-template-columns: 1fr 1fr 1fr;">
                    @if($banner->mobile_image)
                    <div class="col p-1">
                        <div class="card card-product-grid" >
                            <a href="#" class="img-wrap" style="min-height: 124px;"> <img style="height: 124px;object-fit: contain;" src="{{asset('storage/'.$banner->mobile_image)}}" alt="Brand"> </a>
                            <div class="info-wrap  text-center font-sm">
                                <a href="#" class="title text-truncate">Mobile Banner</a>
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

@stop

@section('js')
    <script>

    </script>


    @stop
