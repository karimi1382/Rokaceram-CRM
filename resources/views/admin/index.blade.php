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
                            <span class="lite-text ">داشبورد  ادمین</span>
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
<!-- widget -->
<div class="row m-1 mb-2">
    <div class="col-xl-3 col-md-6 col-sm-6 p-2">
        <div class="box-card text-right mini animate__animated animate__flipInY   "><i
                class="fab far fa-chart-bar b-first" aria-hidden="true"></i>
            <span class="mb-1 c-first">تعداد درخواست های ارسال شده</span>
            <span>{{$totalCompletedRequests}}</span>
            <!-- <p class="mt-3 mb-1 text-right"><i class="far fas fa-wallet mr-1 c-first"></i> در حال
                پیشرفت</p> -->
        </div>
    </div>
  
    <div class="col-xl-3 col-md-6 col-sm-6 p-2">
        <div class="box-card text-right mini animate__animated animate__flipInY   "><i
                class="fab far fa-comments b-third" aria-hidden="true"></i>
            <span class="mb-1 c-third">تعداد درخواست های باز</span>
            <span>{{$totalnotCompletedRequests}}</span>
            <!-- <p class="mt-3 mb-1 text-right"><i class="fab fa-whatsapp mr-1 c-third"></i>در حال پیشرفت
            </p> -->
        </div>
    </div>
        <div class="col-xl-3 col-md-6 col-sm-6 p-2">
        <div class="box-card text-right mini animate__animated animate__flipInY    "><i
                class="fab far fa-clock b-second" aria-hidden="true"></i>
            <span class="mb-1 c-second">متراژ کل رزرو شده</span>
            <span>{{$totalReserveRequestSize}}</span>
            <!-- <p class="mt-3 mb-1 text-right"><i class="far fas fa-wifi mr-1 c-second"></i>در حال پیشرفت
            </p> -->
        </div>
    </div>
    <div class="col-xl-3 col-md-6 col-sm-6 p-2">
        <div class="box-card text-right mini animate__animated animate__flipInY   "><i
                class="fab far fa-gem b-forth" aria-hidden="true"></i>
            <span class="mb-1 c-forth">متراژ کل ارسال شده</span>
            <span>
            {{number_format($totalCompletedRequestSize, 0)}}
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
            <strong>مدیر فروش گرامی، </strong> لطفا جهت مشاهده درخواست های در انتظار تایید به زیر منو درخواست بخش در انتظار تایید  مراجعه نمایید
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
            <h5 class="card-title">درخواست های ارسال شده به تفکیک سایز</h5>

                <hr>
                <canvas id="myChart5"></canvas>
                <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data passed from the controller
        const sizes = @json($sizeLabels);
        const completedData = @json($completedData);
        const notCompletedData = @json($nonCompletedData);

        var mixChart = document.getElementById('myChart5');
        var mixedChart = new Chart(mixChart, {
            type: 'bar',
            data: {
                labels: sizes, // Dynamic size labels
                datasets: [
                    {
                        label: 'درخواست ارسال شده',
                        data: completedData, // Dynamic completed data
                        backgroundColor: ['#ff6384', '#4bc0c0', '#ffcd56', '#c9cbcf', '#36a2eb']
                    },
                  
                ]
            },
            options: {}
        });
    });
</script>
                <hr class="hr-dashed">
                <!-- <p class="text-center c-danger">یک نمونه از نمودار</p> -->
            </div>

        </div>
    </div>

    <div class="col-xs-1 col-sm-1 col-md-4 col-lg-4 p-2">
        <div class="card shade h-100">
            <div class="card-body">
            <h5 class="card-title">5 درخواست برتر به تفکیک مدل</h5>

                <hr>
                <canvas id="myChart4" width="10" height="11"></canvas>

                <script>
document.addEventListener('DOMContentLoaded', function() {
    // Data passed from the controller
    const models = @json($modelLabels);
    const modelData = @json($modelData);
    const colors = @json($colors);

    var ctx = document.getElementById('myChart4').getContext('2d');
    var myChart4 = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: models, // Dynamic model labels
            datasets: [{
                label: '# of Requests',
                data: modelData, // Dynamic model data
                backgroundColor: colors, // Dynamic model colors
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.label + ': ' + tooltipItem.raw + ' requests';
                        }
                    }
                }
            }
        }
    });
});
</script>


                <hr class="hr-dashed">
                <!-- <p class="text-center c-danger">نمونه ای از نمودار</p> -->
            </div>

        </div>
    </div>
</div>

<div class="row m-1">
   
    <div class="col-xs-1 col-sm-1 col-md-8 col-lg-8 p-2">
        <div class="card shade ">
            <div class="card-body">
            <h5 class="card-title">5 نماینده برتر از نظر متراژ سفارش</h5>

                <hr>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col" style="width:70px">ردیف</th>
                            <th scope="col">نام</th>
                            <th scope="col">شهر</th>
                            <th scope="col">تارگت فروش</th>

                            <th scope="col">متراژ خریداری شده</th>
                            <th scope="col">درصد تارگت محقق شده</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php $n=0 ?>
                    @foreach($topDistributors as $distributor)
                    @if($distributor->target  =! NULL)
                   <?php $n++ ?>
                        <tr>
                            <th scope="row">{{$n}}</th>
                            <td>{{ $distributor->name }}</td>
                            <td>{{ $distributor->city_name  }}</td>
                            <td>{{ $distributor->target  }}</td>

                            <td>{{ $requestSizes[$distributor->id] }}</td>
                            <td>{{ ($requestSizes[$distributor->id] *100 )/$distributor->target  }} %</td>

                        </tr>
                        @endif
                    @endforeach
                      
                    </tbody>
                </table>
            </div>

        </div>
    </div>
    
    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 p-2">
        <div class="card shade h-100">
            <div class="card-body">
            <h5 class="card-title">5 درخواست برتر ارسال شده</h5>

                <hr>
                <canvas id="myChart2" width="10" height="13"></canvas>
                <script>
document.addEventListener('DOMContentLoaded', function() {
    // Data passed from the controller
    const models = @json($modelLabelsCompleted);
    const modelData = @json($modelDataCompleted);
    const colors = @json($colorsCompleted);

    var ctx = document.getElementById('myChart2').getContext('2d');
    var myChart4 = new Chart(ctx, {
        type: 'polarArea',
        data: {
            labels: models, // Dynamic model labels
            datasets: [{
                label: '# of Requests',
                data: modelData, // Dynamic model data
                backgroundColor: colors, // Dynamic model colors
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.label + ': ' + tooltipItem.raw + ' requests';
                        }
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
