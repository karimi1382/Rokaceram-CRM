<x-app-layout>


<div class="container-fluid ">

<!-- content -->
<!-- breadcrumb -->

<div class="row  m-1 pb-4 mb-3 ">
    <div class="col-xs-12  col-sm-12  col-md-12  col-lg-12 p-2">
        <div class="page-header breadcrumb-header ">
            <div class="row align-items-end ">
                <div class="col-lg-8">
                    <div class="page-header-title text-left-rtl">
                        <div class="d-inline">
                            <h3 class="lite-text ">داشبورد</h3>
                            <span class="lite-text ">داشبورد مدیریت</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item "><a href="#"><i class="fas fa-home"></i></a></li>
                        <li class="breadcrumb-item active">داشبورد</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- alert -->
<!-- <div class="row m-1 pb-3 ">

    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p-2">
        <div class="alert alert-danger alert-shade alert-dismissible fade show" role="alert">
            <strong>Danger!</strong> Your Disk is Low.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
    </div>

</div> -->
<!-- widget -->

@if(isset($error))
<div class="alert alert-danger">
    {{ $error }}
</div>
@else


<div class="row m-1 mb-2">
    <div class="col-xl-3 col-md-6 col-sm-6 p-2">
        <div class="box-card text-right mini animate__animated animate__flipInY   "><i
                class="fab far fa-chart-bar b-first" aria-hidden="true"></i>
            <span class="mb-1 c-first">تعداد درخواست های باز</span>
            <span>{{$totalUnconfirmedCount}}</span>
            <!-- <p class="mt-3 mb-1 text-right"><i class="far fas fa-wallet mr-1 c-first"></i> در حال
                پیشرفت</p> -->
        </div>
    </div>
  
    <div class="col-xl-3 col-md-6 col-sm-6 p-2">
        <div class="box-card text-right mini animate__animated animate__flipInY   "><i
                class="fab far fa-comments b-third" aria-hidden="true"></i>
            <span class="mb-1 c-third">تعداد حواله های منتظر تایید</span>
            <span>{{$totalInProgressHavale}}</span>
            <!-- <p class="mt-3 mb-1 text-right"><i class="fab fa-whatsapp mr-1 c-third"></i>در حال پیشرفت
            </p> -->
        </div>
    </div>
        <div class="col-xl-3 col-md-6 col-sm-6 p-2">
        <div class="box-card text-right mini animate__animated animate__flipInY    "><i
                class="fab far fa-clock b-second" aria-hidden="true"></i>
            <span class="mb-1 c-second">متراژ رزرو شده</span>
            <span>{{$formattedReservedSize = number_format($totalReservedSize, 2, '/', ',');}}</span>
            <!-- <p class="mt-3 mb-1 text-right"><i class="far fas fa-wifi mr-1 c-second"></i>در حال پیشرفت
            </p> -->
        </div>
    </div>
    <div class="col-xl-3 col-md-6 col-sm-6 p-2">
        <div class="box-card text-right mini animate__animated animate__flipInY   "><i
                class="fab far fa-gem b-forth" aria-hidden="true"></i>
            <span class="mb-1 c-forth">متراژ فروخته شده</span>
            <span>
            {{$formattedTotalCompletedSize = number_format($totalCompletedSize, 2, '/', ',');
        }}
            </span>
            <!-- <p class="mt-3 mb-1 text-right"><i class="fab fa-bluetooth mr-1 c-forth"></i>در حال پیشرفت
            </p> -->
        </div>
    </div>
</div>


<div class="row m-2 mb-1">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p-2">
        <div class="alert text-dir-rtl text-right  alert-third alert-shade alert-dismissible fade show"
            role="alert">
            @if( auth()->user()->target == 1)
            <strong>مدیر فروش گرامی، </strong> لطفا جهت دیدن حواله های رزرو شده و تکمیل شده به زیر منو حواله مراجعه نمایید
            @else
            <strong>مدیر  گرامی، </strong> لطفا جهت دیدن حواله های رزرو شده و تکمیل شده به زیر منو حواله مراجعه نمایید

            @endif
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
    </div>
</div>


<div class="row m-1">
    <div class="col-xs-1 col-sm-1 col-md-8 col-lg-8 p-2">
        <div class="card shade h-100">
            <div class="card-body">
                <h5 class="card-title">متراژ فروخته شده به تفکیک ماه</h5>
                <hr>
                <canvas id="monthlySalesChart"></canvas>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const monthLabels = @json($monthLabels);
                        const soldMeters = @json($soldMetersData);
    
                        var ctx = document.getElementById('monthlySalesChart').getContext('2d');
                        new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: monthLabels,
                                datasets: [{
                                    label: 'متراژ فروخته شده (متر)',
                                    data: soldMeters,
                                    fill: false,
                                    borderColor: 'rgb(75, 192, 192)',
                                    tension: 0.1
                                }]
                            },
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        title: {
                                            display: true,
                                            text: 'متراژ (متر)'
                                        }
                                    },
                                    x: {
                                        title: {
                                            display: true,
                                            text: 'ماه'
                                        }
                                    }
                                },
                                plugins: {
                                    legend: {
                                        position: 'top',
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                return context.dataset.label + ': ' + context.raw.toFixed(2).replace('.', '/');
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    });
                </script>
                <hr class="hr-dashed">
            </div>
        </div>
    </div>
    

    <div class="col-xs-1 col-sm-1 col-md-4 col-lg-4 p-2">
        <div class="card shade h-100">
            <div class="card-body">
                <h5 class="card-title">۵ محصول پر فروش </h5>
                <hr>
                <canvas id="myChart4" width="10" height="11"></canvas>
    
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                    
                        const labels = @json($productLabels);           // فقط برند
                        const data = @json($productData);               // متراژها
                        const colors = ['#ff6384', '#4bc0c0', '#ffcd56', '#c9cbcf', '#36a2eb'];
                    
                        var ctx = document.getElementById('myChart4').getContext('2d');
                        var myChart4 = new Chart(ctx, {
                            type: 'doughnut',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'میزان فروش (متر مربع)',
                                    data: data,
                                    backgroundColor: colors,
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        position: 'top',  // فقط اسم برند در بالا
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                let label = context.label || '';
                                                let value = context.raw || 0;

                                                return `${label}\nمتراژ: ${value.toLocaleString('fa-IR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} متر مربع`;
                                            }
                                        }
                                    }

                                }
                            }
                        });
                    });
                    </script>
                    
                    
                    
                    
                <hr class="hr-dashed">
            </div>
        </div>
    </div>
    
</div>

<div class="row m-1">
   
    <div class="col-xs-1 col-sm-1 col-md-6 col-lg-6 p-2">
        <div class="card shade ">
            <div class="card-body">
                <h5 class="card-title">5 نماینده برتر از نظر متراژ خریداری شده</h5>
                <hr>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col" style="width:70px">ردیف</th>
                            <th scope="col">نام نماینده</th>
                            <th scope="col">شهر</th>
                            <th scope="col">متراژ خریداری شده</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $n = 0; ?>
                        @foreach($topDistributorsWithSizes as $distributor)
                            <?php $n++; ?>
                            <tr>
                                <th scope="row">{{ $n }}</th>
                                <td>{{ $distributor->name }}</td>
                                <td>{{ $distributor->city_name }}</td>
                                <td>{{ $distributor->product_mr }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    
    
    
    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 p-2">
        <div class="card shade h-100">
            <div class="card-body">
                <h5 class="card-title">متراژ فروخته شده بر اساس سایز</h5>
                <hr>
                <canvas id="myChartSize" width="10" height="5"></canvas>
    
                <script>
                document.addEventListener('DOMContentLoaded', function() {
    
                    const sizeLabels = @json($sizeLabels);
                    const sizeData = @json($sizeValues);
    
                    var ctx = document.getElementById('myChartSize').getContext('2d');
    
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: sizeLabels,  // سایزها در پایین
                            datasets: [{
                                label: 'متراژ فروخته شده (متر مربع)',
                                data: sizeData,    // متراژ هر سایز
                                backgroundColor: '#4bc0c0'
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: { display: false },  // چون لیبل بالای نمودار لازم نیست
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return 'سایز: ' + context.label + ' — متراژ: ' + 
                                                   context.raw.toLocaleString('fa-IR', {minimumFractionDigits:2, maximumFractionDigits:2}) + ' متر مربع';
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'متراژ (متر مربع)'
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: 'سایز محصول'
                                    }
                                }
                            }
                        }
                    });
                });
                </script>
    
            </div>
        </div>
    </div>
    
</div>




@endif


</div>



    <!-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div> -->
</x-app-layout>
