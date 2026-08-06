@extends('layouts.app')


@section('content')


    <div class="content-header">
        <div>
            <h2 class="content-title card-title">Price up Notifications</h2>
            <p> information.</p>
        </div>
     

    </div>

    @if(session()->has('message'))
        <div class="alert alert-success text-center">
            {{ session()->get('message') }}
        </div>
    @endif

    <div class="alert alert-success alert-div text-center" style="display: none;">

    </div>

    <div class="card mb-4">

        <!-- card-header end// -->
        <div class="card-body" id="update-table">
            <div class="table-responsive" >
                <table id="myTable" class="table table-hover">
                    <thead>
                    <tr>
                        <th>#Sr</th>
                        <th scope="col">Product</th>
                        <th scope="col">Variant</th>
                        <th scope="col">Old Purchase</th>
                        <th scope="col">New Purchase</th>
                        <th scope="col">Old Wholesale</th>
                        <th scope="col">New Wholesale</th>
                        <th scope="col">Date</th>
                        
                        <th scope="col" class="text-right">Action</th>
                    </tr>
                    </thead>
                    <tbody><?php $sr = 1;?>
                    @foreach($notifications as $n)

                            <tr>

                                <td>{{$sr++}}</td>
                                <td><b>{{$n->product->title}}</b></td>
                                <td>{{$n->variant ? $n->variant->shade : ''}} {{$n->variant ? $n->variant->size : ''}}</td>
                                <td>{{$n->old_purchase}}</td>
                                <td>{{$n->new_purchase}}</td>
                                <td>{{$n->old_price}}</td>
                                <td>{{$n->new_price}}</td>
                               

                                <td>{{date('M d, Y',strtotime($n->created_at))}}</td>

                                <td class="text-end">
                                    
                                    <a href="{{route('products.edit',$n->product_id)}}" class="btn btn-md rounded font-sm">Update</a>
                                
                                        
                                    <!-- dropdown //end -->
                                </td>

                            </tr>
                            @endforeach

                    </tbody>
                </table>
            </div>
            <!-- table-responsive //end -->
        </div>
        <!-- card-body end// -->
    </div>





@stop


@section('js')
    <script>
        $(document).ready( function () {
            $('#myTable').DataTable({
                'ordering': false, 'sorting' : false, 'paging' : false, 'info' : false, 'searching':true
            });
        } );

        setTimeout(function() {
            $('.alert').fadeOut('fast');
        }, 1000);
    </script>
@stop
