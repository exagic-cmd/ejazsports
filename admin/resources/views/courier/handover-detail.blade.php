@extends('layouts.app')

@section('content')

    <div class="content-header">
        <div>
            <h2 class="content-title card-title">Handover Detail </h2>
            <p>Details for {{date('d M,Y h:i',strtotime($handover->created_at))}}</p>
        </div>
    </div>


<div class="card">
    <header class="card-header">
        <div class="row align-items-center">
            <div class="col-lg-6 col-md-6 mb-lg-0 mb-15">
                <span> <i class="material-icons md-calendar_today"></i> <b>Total Amount : {{number_format($handover->total_amount)}} </b> </span> <br>
                <small class="text-muted">Total Orders : {{$handover->total_orders}}</small>
            </div>

        </div>
    </header>
    <!-- card-header end// -->
    <div class="card-body">
        <!-- row // -->
        <div class="row">
            <div class="col-lg-10">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th width="40%">Sr #</th>
                            <th width="40%">Cn No</th>
                            <th width="20%">Order No</th>
                            <th width="20%">Amount</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sr = 1;?>
                        @foreach($handover->orders as $order)
                            <tr>
                                <td>{{$sr++}}</td>
                                <td>{{$order->cn_no}}</td>
                                <td>VEGAS-{{$order->order_no}}</td>
                                <td>{{number_format($order->total_amount)}}</td>
                            </tr>
                        @endforeach


                        </tbody>
                    </table>
                </div>
                <!-- table-responsive// -->
            </div>
            <!-- col// -->
            <div class="col-lg-1"></div>

            <!-- col// -->
        </div>


    </div>
    <!-- card-body end// -->
</div>

    @stop
