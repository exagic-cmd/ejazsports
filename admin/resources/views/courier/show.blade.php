@extends('layouts.app')

@section('content')

    <div class="content-header">
        <div>
            <h2 class="content-title card-title">{{$courier->name}} Detail</h2>
            <p>Details for handovers orders</p>
        </div>
    </div>


    <div class="card">
        <header class="card-header">
            <div class="row align-items-center">
                <div class="col-lg-6 col-md-6 mb-lg-0 mb-15">
                    <span> <i class="material-icons md-calendar_today"></i> <b>Total Amount : {{number_format($courier->handovers->sum('total_amount'))}} </b> </span> <br>
                    <small class="text-muted">Total Orders : {{$courier->handovers->sum('total_orders')}}</small>
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
                                <th width="10%">Sr #</th>
                                <th width="20%">Date</th>
                                <th width="20%">Total Orders</th>
                                <th width="20%">Total Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $sr = 1;?>
                            @foreach($courier->handovers as $handover)
                                <tr>
                                    <td>{{$sr++}}</td>
                                    <td>{{date('d M,Y h:i',strtotime($handover->created_at))}}</td>
                                    <td>{{$handover->total_orders}}</td>

                                    <td>{{number_format($handover->total_amount)}}</td>
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
