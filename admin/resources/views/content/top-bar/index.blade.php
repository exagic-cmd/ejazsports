@extends('layouts.app')


@section('content')


    <div class="content-header">
        <div>
            <h2 class="content-title card-title">All Top Bar Contents</h2>
            <p>category information.</p>
        </div>
        @can('Create Top Bar Content')
        <div>
            <a  href="{{route('top-bar.create')}}" class="btn btn-primary"><i class="text-muted material-icons md-post_add"></i>Add New</a>
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
                    <tr>
                        <th>#Sr</th>
                        <th scope="col">Text</th>
                        <th scope="col">Order #</th>
                        <th scope="col">Web Active</th>
                        <th scope="col">Mobile Active</th>
                        <th scope="col">Status</th>
                        <th scope="col">Created At</th>
                        <th scope="col" class="text-right">Action</th>
                    </tr>
                    </thead>
                    <tbody><?php $sr = 1;?>
                    @foreach($topBarContents as $tBC)

                            <tr>

                                <td>{{$sr++}}</td>
                                <td><b><?php echo $tBC->text;?></b></td>
                                <td>{{$tBC->serial_no}}</td>

                                <td><span class="badge rounded-pill {{($tBC->web_active) ? 'alert-success' : 'alert-danger'}}">{{($tBC->web_active) ? 'Active' : 'InActive'}}</span></td>

                                <td><span class="badge rounded-pill {{($tBC->mobile_active) ? 'alert-success' : 'alert-danger'}}">{{($tBC->mobile_active) ? 'Active' : 'InActive'}}</span></td>

                                <td><span class="badge rounded-pill {{($tBC->status) ? 'alert-success' : 'alert-danger'}}">{{($tBC->status) ? 'Active' : 'InActive'}}</span></td>

                                <td>{{date('M d, Y',strtotime($tBC->created_at))}}</td>

                                <td class="text-end">

                                        <div class="dropdown">
                                        <a href="#" data-bs-toggle="dropdown" class="btn btn-light rounded btn-sm font-sm"> <i class="material-icons md-more_horiz"></i> </a>
                                        <div class="dropdown-menu">
                                            @can('Edit Top Bar Content')
                                            <a class="dropdown-item" href="{{route('top-bar.edit',$tBC->id)}}">Edit info</a>
                                            @endcan

                                            @can('Delete Top Bar Content')
                                            <form onsubmit="return confirm('Do you really want to do this?');" id="delete-form" action="{{ route('top-bar.destroy',$tBC->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')

                                                <button class="dropdown-item text-danger" onclick="return confirm('Are you sure?')"  type="submit">Delete</button>
                                            </form>
                                                @endcan
                                        </div>
                                    </div>
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
