@extends('layouts.app')


@section('content')


    <div class="content-header">
        <div>
            <h2 class="content-title card-title">Stock Audit List</h2>
            <p>Audit information.</p>
        </div>
        @can('Create Stock Audit')
            <div>
                <a  href="{{route('stock-audits.create')}}" class="btn btn-primary"><i class="text-muted material-icons md-post_add"></i>Add New</a>
            </div>
        @endcan

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
                    <tr class="text-center">
                        <th>#Sr</th>
                        <th scope="col">Store#</th>
                        <th scope="col">Date</th>
                        <th scope="col">Brand</th>
                        <th scope="col">Audit By</th>
                        <th scope="col">Approve by</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="text-right">Action</th>
                    </tr>
                    </thead>
                    <tbody><?php $sr = 1;?>
                    @foreach($stockAudits as $sA)

                        <tr style="text-align: center;">

                            <td>{{$sr++}}</td>
                            <td>{{$sA->storeId ? $sA->storeId->name : ''}}</td>
                            <td>{{date('d-m-Y',strtotime($sA->date))}}</td>
                            <td> <b>{{$sA->brand ? $sA->brand->title : ''}}</b></td>
                            <td> <b>{{$sA->auditBy ? $sA->auditBy->name : ''}}</b></td>
                            <td> <b>{{$sA->approveBy ? $sA->approveBy->name : ''}}</b></td>
                            <td>
                                @if($sA->status == \App\Models\StockAudit::PENDING)
                                    <span class="badge rounded-pill  alert-primary">PENDING</span>
                                @elseif($sA->status == \App\Models\StockAudit::APPROVED)
                                    <span class="badge rounded-pill  alert-success">APPROVED</span>
                                @endif
                            </td>


                            <td class="text-end">
                                @can('List Stock Audit')
                                    <a href="{{route('stock-audits.show',$sA->id)}}" class="btn btn-md rounded font-sm">Detail</a>
                                @endcan

                                @if($sA->status == \App\Models\StockAudit::PENDING)
                                <div class="dropdown ">
                                    <a href="#" data-bs-toggle="dropdown" class="btn btn-light rounded btn-sm font-sm"> <i class="material-icons md-more_horiz"></i> </a>
                                    <div class="dropdown-menu">
                                        @can('Edit Stock Audit')
                                            <a class="dropdown-item" href="{{route('stock-audits.edit',$sA->id)}}">Edit info</a>
                                        @endcan

                                        @can('Edit Stock Audit')
                                            <form onsubmit="return confirm('Do you really want to do this?');" id="delete-form" action="{{ route('stock-audits.destroy',$sA->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')

                                                <button class="dropdown-item text-danger" onclick="return confirm('Are you sure?')"  type="submit">Delete</button>
                                            </form>
                                        @endcan
                                    </div>
                                </div>
                                    @endif
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
                'ordering': true, 'sorting' : true, 'paging' : true,'pageLength' : 50, 'info' : true, 'searching':true
            });
        } );

        setTimeout(function() {
            $('.alert').fadeOut('fast');
        }, 1000);
    </script>
@stop
