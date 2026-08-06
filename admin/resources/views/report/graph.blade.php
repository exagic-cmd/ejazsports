@extends('layouts.app')


@section('content')


    <div class="content-header">
        <div>
            <h2 class="content-title card-title">Graph Reports</h2>
            <p>Whole data about your business here</p>
        </div>

    </div>

    <div class="row">
        <div class="col-xl-10 col-lg-12">
            <div class="card mb-4">
                <article class="card-body">
                    <h5 class="card-title">Sale statistics Month wise</h5>
                    <canvas id="monthlySales" height="355" width="890" style="display: block; box-sizing: border-box; height: 284px; width: 712.4px;"></canvas>
                </article>
            </div>

        </div>

        <div class="col-xl-10 col-lg-12">
            <div class="card mb-4">
                <article class="card-body">
                    <h5 class="card-title">Orders statistics Month wise</h5>
                    <canvas id="monthlyOrders" height="355" width="890" style="display: block; box-sizing: border-box; height: 284px; width: 712.4px;"></canvas>
                </article>
            </div>

        </div>

        <div class="col-xl-10 col-lg-12">
            <div class="card mb-4">
                <article class="card-body">
                    <h5 class="card-title">Sale statistics Day wise</h5>
                    <canvas id="dailySales" height="355" width="890" style="display: block; box-sizing: border-box; height: 284px; width: 712.4px;"></canvas>
                </article>
            </div>

        </div>

        <div class="col-xl-10 col-lg-12">
            <div class="card mb-4">
                <article class="card-body">
                    <h5 class="card-title">Orders statistics Day wise</h5>
                    <canvas id="dailyOrders" height="355" width="890" style="display: block; box-sizing: border-box; height: 284px; width: 712.4px;"></canvas>
                </article>
            </div>

        </div>

    </div>

@stop

@section('js')
    <script>


        var colors = @json($colors);
        var borders = @json($borders);


        if ($('#monthlySales').length) {

            var storeMonthlySales = Object.entries(@json($storeMonthlySales));

            dataset = [];
            for(i = 0 ; i < storeMonthlySales.length;i++) { temp = {
                'label': storeMonthlySales[i][1].label,
                'tension': 0.3,
                'fill': true,
                'backgroundColor': colors[i],
                'borderColor': borders[i],
                'data': storeMonthlySales[i][1].monthlySales
            };

                dataset.push(temp);
            }

            var ctx = document.getElementById('monthlySales').getContext('2d');
            var chart = new Chart(ctx, {
                // The type of chart we want to create
                type: 'line',

                // The data for our dataset
                data: {
                    labels: @json($monthNames),
                    datasets: dataset
                },
                options: {
                    plugins: {
                        legend: {
                            labels: {
                                usePointStyle: true,
                            },
                        }
                    }
                }
            });
        } //End if

        if ($('#monthlyOrders').length) {

            var storeMonthlyOrders = Object.entries(@json($storeMonthlyOrders));

            dataset = [];
            for(i = 0 ; i < storeMonthlyOrders.length;i++) { temp = {
                'label': storeMonthlyOrders[i][1].label,
                'tension': 0.3,
                'fill': true,
                'backgroundColor': colors[i],
                'borderColor': borders[i],
                'data': storeMonthlyOrders[i][1].monthlyOrders
            };

                dataset.push(temp);
            }

            var ctx = document.getElementById('monthlyOrders').getContext('2d');
            var chart = new Chart(ctx, {
                // The type of chart we want to create
                type: 'line',

                // The data for our dataset
                data: {
                    labels: @json($monthNames),
                    datasets: dataset
                },
                options: {
                    plugins: {
                        legend: {
                            labels: {
                                usePointStyle: true,
                            },
                        }
                    }
                }
            });
        } //End if

        if ($('#dailySales').length) {

            var storeDailySales = Object.entries(@json($storeDailySales));

            dataset = [];
            for(i = 0 ; i < storeDailySales.length;i++) { temp = {
                'label': storeDailySales[i][1].label,
                'tension': 0.3,
                'fill': true,
                'backgroundColor': colors[i],
                'borderColor': borders[i],
                'data': storeDailySales[i][1].dailySales
            };

                dataset.push(temp);
            }

            var ctx = document.getElementById('dailySales').getContext('2d');
            var chart = new Chart(ctx, {
                // The type of chart we want to create
                type: 'line',

                // The data for our dataset
                data: {
                    labels: @json($dayNames),
                    datasets: dataset
                },
                options: {
                    plugins: {
                        legend: {
                            labels: {
                                usePointStyle: true,
                            },
                        }
                    }
                }
            });
        } //End if

        if ($('#dailyOrders').length) {

            var storeDailyOrders = Object.entries(@json($storeDailyOrders));

            dataset = [];
            for(i = 0 ; i < storeDailyOrders.length;i++) { temp = {
                'label': storeDailyOrders[i][1].label,
                'tension': 0.3,
                'fill': true,
                'backgroundColor': colors[i],
                'borderColor': borders[i],
                'data': storeDailyOrders[i][1].dailyOrders
            };

                dataset.push(temp);
            }

            var ctx = document.getElementById('dailyOrders').getContext('2d');
            var chart = new Chart(ctx, {
                // The type of chart we want to create
                type: 'line',

                // The data for our dataset
                data: {
                    labels: @json($dayNames),
                    datasets: dataset
                },
                options: {
                    plugins: {
                        legend: {
                            labels: {
                                usePointStyle: true,
                            },
                        }
                    }
                }
            });
        } //End if
    </script>

@stop

