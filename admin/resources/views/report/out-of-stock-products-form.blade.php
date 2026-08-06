@extends('layouts.app')

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('content')

    <div class="content-header">
        <div>
            <h2 class="content-title card-title">Out Of Stock Product Reports</h2>
            <p>Select the specific Brand</p>
        </div>

    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card card-body mb-4">
                <form method="post" action="{{route('report.out-of-stock.products')}}">
                    @csrf

                    <div class="row mb-4">
                        <label class="col-lg-3 col-form-label">Brand<span style="color: red;"> *</span></label>
                        <div class="col-lg-9">
                            <select class="form-control select2" name="brand_id">

                                @foreach($brands as $b)
                                    <option value="{{$b->id}}">{{$b->title}}</option>
                                @endforeach
                            </select>
                            @error('brand_id')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <!-- col.// -->
                    </div>

                    <div class="form-actions" style="text-align: right">
                        <button type="submit" class=" btn btn-success-light"> <i class="fa fa-check" ></i> Generate</button>

                    </div>
                </form>
            </div>
        </div>
    </div>

@stop

@section('js')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>



        $('#daterange-btn').daterangepicker(
            {
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'Last 90 Days': [moment().subtract(90, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'This Year': [moment().startOf('year'), moment().endOf('year')],
                    'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],
                    'All Time': [moment().subtract(5.5, 'year'), moment()]
                },
                startDate: moment(),
                endDate: moment()
            },
            function (start, end) {
                $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }
        );
    </script>

@stop

