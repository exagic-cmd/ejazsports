@extends('layouts.app')


@section('content')


    <div class="content-header">
        <div>
            <h2 class="content-title card-title">All Receiving</h2>
            <p>Receiving information.</p>
        </div>
        @can('Create Receiving')
            <div>
                <a href="{{ route('receiving.create') }}" class="btn btn-primary"><i
                        class="text-muted material-icons md-post_add"></i>Add New</a>
            </div>
        @endcan

    </div>

    @if (session()->has('message'))
        <div class="alert alert-success text-center">
            {{ session()->get('message') }}
        </div>
    @endif

    <div class="alert alert-success alert-div text-center" style="display: none;">

    </div>

    <div class="card mb-4">

        <!-- card-header end// -->
        <div class="card-body" id="update-table">
            <div class="">
                <table id="myTable" class="table table-hover">
                    <thead>
                        <tr>
                            <th>#Sr</th>
                            <th scope="col">Date</th>
                            <th scope="col">Cargo #</th>
                            <th scope="col">Supplier</th>
                            <th scope="col">Invoice #</th>
                            <th scope="col">Total Product</th>
                            <th scope="col">Total Qty</th>
                            <th scope="col">Net Amount</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody><?php $sr = 1; ?>
                        @foreach ($receiving as $r)
                            <tr>

                                <td @class('text-center')>{{ $sr++ }}</td>
                                <td @class('text-center')>{{ date('d-m-Y', strtotime($r->date)) }}</td>
                                <td @class('text-center')>{{ $r->cargo_no }}</td>
                                <td @class('text-center')>
                                    {{ optional(optional($r->purchaseOrder)->supplier)->name ?? (optional($r->supplier)->name ?? 'N/A') }}
                                </td>
                                <td @class('text-center')>{{ $r->invoice_no }}</td>
                                <td @class('text-center')>{{ $r->total_products }}</td>
                                <td @class('text-center')>{{ number_format($r->total_qty) }}</td>
                                <td @class('text-center')>{{ number_format($r->net_amount) }}</td>

                                <td>

                                    @if ($r->status == \App\Models\Receiving::APPROVAL_PENDING)
                                        <span class="badge rounded-pill  alert-danger">
                                            APPROVAL PENDING
                                        </span>
                                    @elseif($r->status == \App\Models\Receiving::APPROVED)
                                        <span class="badge rounded-pill  alert-success">
                                            APPROVED
                                        </span>
                                    @endif
                                </td>



                                <td class="text-end">

                                    @can('View Receiving')
                                        <a href="{{ route('receiving.show', $r->id) }}"
                                            class="btn btn-md rounded font-sm">Detail</a>
                                    @endcan

                                    <div class="dropdown">
                                        <a href="#" data-bs-toggle="dropdown"
                                            class="btn btn-light rounded btn-sm font-sm"> <i
                                                class="material-icons md-more_horiz"></i> </a>
                                        <div class="dropdown-menu">

                                            @can('Edit Receiving')
                                                <a class="dropdown-item"
                                                    href="{{ route('receiving.incomplete.edit', $r->id) }}">Edit info</a>
                                            @endcan



                                            @can('Delete Receiving')
                                                <form onsubmit="return confirm('Do you really want to do this?');"
                                                    id="delete-form" action="{{ route('receiving.destroy', $r->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button class="dropdown-item text-danger"
                                                        onclick="return confirm('Are you sure?')" type="submit">Delete</button>
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
        $(document).ready(function() {
            $('#myTable').DataTable({
                'ordering': false,
                'sorting': false,
                'paging': true,
                'pageLength': 50,
                'info': false,
                'searching': true;
            });
        });

        setTimeout(function() {
            $('.alert').fadeOut('fast');
        }, 1000);
    </script>
@stop
