@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-sm-12">
            <div class="home-tab">
                <div class="row">
                    <div class="col-lg-4 col-md-4 grid-margin stretch-card">
                        <div class="card rounded-1">
                            <div class="card-body card-1">
                                <div class="d-flex">
                                    <div class="avatar">
                                        <span class="avatar-title bg-soft-card-1 rounded">
                                            <i class="mdi mdi-shopping font-size-24"></i>
                                        </span>
                                    </div>
                                    <div class="ms-4">
                                        <p class="text-muted mb-0">Total Posts</p>
                                        <h4 class="mt-1 mb-0">
                                            {{ isset($total_post) && isset($total_post->total_post) ? $total_post->total_post : 0 }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 grid-margin stretch-card">
                        <div class="card rounded-1">
                            <div class="card-body card-2">
                                <div class="d-flex">
                                    <div class="avatar">
                                        <span class="avatar-title bg-soft-card-2 rounded">
                                            <i class="mdi mdi-account-multiple font-size-24"></i>
                                        </span>
                                    </div>
                                    <div class="ms-4">
                                        <p class="text-muted mb-0">Total Users</p>
                                        <h4 class="mt-1 mb-0">
                                            {{ isset($total_users) && isset($total_users->total_users) ? $total_users->total_users : 0 }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 grid-margin stretch-card">
                        <div class="card rounded-1">
                            <div class="card-body card-3">
                                <div class="d-flex">
                                    <div class="avatar">
                                        <span class="avatar-title bg-soft-card-3 rounded">
                                            <i class="mdi mdi-store font-size-24"></i>
                                        </span>
                                    </div>
                                    <div class="ms-4">
                                        <p class="text-muted mb-0">Total Products</p>
                                        <h4 class="mt-1 mb-0">
                                            {{ isset($total_product) && isset($total_product->total_product) ? $total_product->total_product : 0 }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12 d-flex flex-column">
                        <div class="row flex-grow">
                            <div class="col-12 col-lg-4 col-lg-12 grid-margin stretch-card">
                                <div class="card card-rounded">
                                    <div class="card-body">
                                        <div class="d-sm-flex justify-content-between align-items-start">
                                            <div>
                                                <h4 class="card-title card-title-dash">Media Posts</h4>
                                            </div>
                                            <div id="performance-line-legend"></div>
                                        </div>
                                        <div class="chartjs-wrapper mt-5">
                                            <canvas id="performaneLine"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> 

                <div class="row grid-margin">
                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                        <div class="card ">
                            <div class="card-body ">
                                <h4>Recent Post's</h4>
                                <hr>
                                <table class="table table-borderless recent_post_table">
                                    <thead>
                                        <tr>
                                            <th>Post Title</th>
                                            <th>Post Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recent_posts as $recent_post)
                                        <tr>
                                            <td>{{$recent_post->content}}</td>
                                            <td>{{date('Y-m-d', strtotime($recent_post->created_at))}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                        <div class="card ">
                            <div class="card-body ">
                                <h4>Recent Product's</h4>
                                <hr>
                                <table class="table table-borderless recent_products_table">
                                    <thead>
                                        <tr>
                                            <th>Product's Title</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recent_products as $recent_product)
                                        <tr>
                                            <td>{{$recent_product->name}}</td>
                                            <td>{{date('Y-m-d', strtotime($recent_product->created_at))}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row flex-grow">
                    <div class="col-12 grid-margin stretch-card">
                        <div class="card card-rounded">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <h4 class="card-title card-title-dash">Recent User's</h4>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            @foreach($total_user as $total_users)
                                            <div
                                                class="wrapper d-flex align-items-center justify-content-between py-2 border-bottom">
                                                <div class="d-flex">
                                                    <img class="img-sm rounded-10"
                                                        src="{{ url(config('app.profile_image')).'/'.$total_users->profile_image ?? '' }} " 
                                                        onerror="this.src='{{ url('/uploads/defaultimage.png') }}'"
                                                        >
                                                    <div class="wrapper ms-3">
                                                        <p class="ms-1 mb-1 fw-bold">
                                                            {{ $total_users->email}}</p>
                                                        <small class="text-muted mb-0">{{ $total_users->nickname }}</small>
                                                    </div>
                                                </div>

                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- content-wrapper ends -->
@endsection
@section('js')
<script>
var yearData = '"{!!$yearly_data!!}"';

if ($("#performaneLine").length) {
    var graphGradient = document.getElementById("performaneLine").getContext('2d');
    var graphGradient2 = document.getElementById("performaneLine").getContext('2d');
    var saleGradientBg = graphGradient.createLinearGradient(5, 0, 5, 100);
    saleGradientBg.addColorStop(0, 'rgba(26, 115, 232, 0.18)');
    saleGradientBg.addColorStop(1, 'rgba(26, 115, 232, 0.02)');
    var saleGradientBg2 = graphGradient2.createLinearGradient(100, 0, 50, 150);
    saleGradientBg2.addColorStop(0, 'rgba(0, 208, 255, 0.19)');
    saleGradientBg2.addColorStop(1, 'rgba(0, 208, 255, 0.03)');
    var salesTopData = {
        labels: ["MON", "TUE", "WED", "THU", "FRI", "SAT", "SUN"],
        datasets: [{
            label: 'This week',
            data: ["<?php echo $this_week_data; ?>"],
            backgroundColor: saleGradientBg,
            borderColor: [
                '#1F3BB3',
            ],
            borderWidth: 1.5,
            fill: true, // 3: no fill
            pointBorderWidth: 1,
            pointRadius: [4, 4, 4, 4, 4, 4, 4],
            pointHoverRadius: [2, 2, 2, 2, 2, 2, 2],
            pointBackgroundColor: ['#1F3BB3', '#1F3BB3', '#1F3BB3', '#1F3BB3', '#1F3BB3', '#1F3BB3',
                '#1F3BB3'
            ],
            pointBorderColor: ['#fff', '#fff', '#fff', '#fff', '#fff', '#fff', '#fff'],
        }, {
            label: 'Last week',
            data: ["<?php echo $last_week_data; ?>"],
            backgroundColor: saleGradientBg2,
            borderColor: [
                '#52CDFF',
            ],
            borderWidth: 1.5,
            fill: true, // 3: no fill
            pointBorderWidth: 1,
            pointRadius: [4, 4, 4, 4, 4, 4, 4],
            pointHoverRadius: [2, 2, 2, 2, 2, 2, 2],
            pointBackgroundColor: ['#52CDFF', '#52CDFF', '#52CDFF', '#52CDFF', '#52CDFF', '#52CDFF',
                '#52CDFF'
            ],
            pointBorderColor: ['#fff', '#fff', '#fff', '#fff', '#fff', '#fff', '#fff'],
        }]
    };

    var salesTopOptions = {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            yAxes: [{
                gridLines: {
                    display: true,
                    drawBorder: false,
                    color: "#F0F0F0",
                    zeroLineColor: '#F0F0F0',
                },
                ticks: {
                    beginAtZero: false,
                    autoSkip: true,
                    maxTicksLimit: 4,
                    fontSize: 10,
                    color: "#6B778C"
                }
            }],
            xAxes: [{
                gridLines: {
                    display: false,
                    drawBorder: false,
                },
                ticks: {
                    beginAtZero: false,
                    autoSkip: true,
                    maxTicksLimit: 7,
                    fontSize: 10,
                    color: "#6B778C"
                }
            }],
        },
        legend: false,
        legendCallback: function(chart) {
            var text = [];
            text.push('<div class="chartjs-legend"><ul>');
            for (var i = 0; i < chart.data.datasets.length; i++) {
                console.log(chart.data.datasets[i]); // see what's inside the obj.
                text.push('<li>');
                text.push('<span style="background-color:' + chart.data.datasets[i].borderColor + '">' +
                    '</span>');
                text.push(chart.data.datasets[i].label);
                text.push('</li>');
            }
            text.push('</ul></div>');
            return text.join("");
        },

        elements: {
            line: {
                tension: 0.4,
            }
        },
        tooltips: {
            backgroundColor: 'rgba(31, 59, 179, 1)',
        }
    }
    var salesTop = new Chart(graphGradient, {
        type: 'line',
        data: salesTopData,
        options: salesTopOptions
    });
    document.getElementById('performance-line-legend').innerHTML = salesTop.generateLegend();
}

if ($("#marketingOverview").length) {
    var marketingOverviewChart = document.getElementById("marketingOverview").getContext('2d');
    var marketingOverviewData = {
        labels: ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"],
        datasets: [{
            label: 'Orders',
            data: ["<?php echo $yearly_data; ?>"],
            backgroundColor: "#1F3BB3",
            borderColor: [
                '#1F3BB3',
            ],
            borderWidth: 0,
            fill: true, // 3: no fill

        }]
    };

    var marketingOverviewOptions = {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            yAxes: [{
                gridLines: {
                    display: true,
                    drawBorder: false,
                    color: "#F0F0F0",
                    zeroLineColor: '#F0F0F0',
                },
                ticks: {
                    beginAtZero: true,
                    autoSkip: true,
                    maxTicksLimit: 5,
                    fontSize: 10,
                    color: "#6B778C"
                }
            }],
            xAxes: [{
                stacked: true,
                barPercentage: 0.35,
                gridLines: {
                    display: false,
                    drawBorder: false,
                },
                ticks: {
                    beginAtZero: false,
                    autoSkip: true,
                    maxTicksLimit: 12,
                    fontSize: 10,
                    color: "#6B778C"
                }
            }],
        },
        legend: false,
        legendCallback: function(chart) {
            var text = [];
            text.push('<div class="chartjs-legend"><ul>');
            for (var i = 0; i < chart.data.datasets.length; i++) {
                console.log(chart.data.datasets[i]); // see what's inside the obj.
                text.push('<li class="text-muted text-small">');
                text.push('<span style="background-color:' + chart.data.datasets[i].borderColor + '">' +
                    '</span>');
                text.push(chart.data.datasets[i].label);
                text.push('</li>');
            }
            text.push('</ul></div>');
            return text.join("");
        },

        elements: {
            line: {
                tension: 0.4,
            }
        },
        tooltips: {
            backgroundColor: 'rgba(31, 59, 179, 1)',
        }
    }
    var marketingOverview = new Chart(marketingOverviewChart, {
        type: 'bar',
        data: marketingOverviewData,
        options: marketingOverviewOptions
    });
    document.getElementById('marketing-overview-legend').innerHTML = marketingOverview.generateLegend();
}
</script>
@endsection