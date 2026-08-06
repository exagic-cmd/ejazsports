@extends('layouts.app')

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('content')

    <div class="content-header">
        <div>
            <h2 class="content-title card-title">Stats Reports</h2>
            <p>Select the specific date</p>
        </div>

    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card card-body mb-4">
                <form method="post" action="{{route('report.stats')}}">
                    @csrf
                    <div class="row mb-4">
                        <label class="col-lg-3 col-form-label">Date Range<span style="color: red;"> *</span></label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control "id="daterange-btn" value='{{old('date_range')}}' name='date_range'>
                            @error('date_range')
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
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
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

