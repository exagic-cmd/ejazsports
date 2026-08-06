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
                    <img src="{{asset('imgs/people/avatar-4.png')}}" style="max-width: 80%;!important;" class="center-xy img-fluid" alt="Logo Brand">
                </div>
            </div>
            <!--  col.// -->
            <div class="col-xl col-lg">
                <h3>{{$store->name}}</h3>
                <p><span class="badge rounded-pill {{($store->status) ? 'alert-success' : 'alert-danger'}}">{{($store->status) ? 'Active' : 'InActive'}}</span>

                </p>
            </div>
            <!--  col.// -->
            <div class="col-xl-4 text-md-end">
                @can('Edit Store')
                <a class="dropdown-item btn btn-primary d-inline" href="{{route('stores.edit',$store->id)}}">Edit info</a>
                @endcan

                @can('Delete Store')
                <form @class('d-inline') onsubmit="return confirm('Do you really want to do this?');" id="delete-form" action="{{ route('stores.destroy',$store->id) }}" method="POST">
                @csrf
                @method('DELETE')

                <button style="width: min-content;" class="dropdown-item btn btn-instagram d-inline"  type="submit">Delete</button>
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
                    <p class="mb-0 text-muted d-inline">Total Sales : <h5 class="text-success text-center d-inline">0</h5></p>
                    <p class="mb-0 text-muted d-inline">Last Month Sales: <h5 class="text-success text-center d-inline">0</h5></p>
                    <p class="mb-0 text-muted d-inline">Current Month Sales: <h5 class="text-success text-center d-inline">0</h5></p>


                </article>
            </div>
            <!--  col.// -->
            <div class="col-sm-6 col-lg-4 col-xl-3">
                <h6>Basic</h6>
                <p>
                   
                    <b>Phone Number: </b> {{$store->phone_number}} <br>
                    <b>Address: </b> {{$store->address}} <br>

                </p>
            </div>
            <!--  col.// -->

            <!--  col.// -->
            
        </div>
        <!--  row.// -->
    </div>
    <!--  card-body.// -->
</div>
<!--  card.// -->
<div class="card mb-4">
    <div class="card-body">

    </div>
    <!--  card-body.// -->
</div>
<!--  card.// -->

@stop
