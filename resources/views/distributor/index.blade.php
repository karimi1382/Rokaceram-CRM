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
                            <span class="lite-text ">داشبورد نماینده فروش</span>
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

@if(isset($error))
<div class="alert alert-danger">
    {{ $error }}
</div>
@else




<div class="col-md-12">
    @if(session()->has('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session()->has('error')) 
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>

{{-- <div class="row m-1">
    <div class="col-12 text-center">
        <a href="{{ asset('/attention/28111403.pdf')}}" class="btn btn-danger btn-lg font-weight-bold p-3" style="width: 100%; font-size: 24px;">
            ⚠️ جهت مشاهده اطلاعیه کلیک نمایید ⚠️
        </a>
    </div>
</div> --}}



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
<div class="row m-1 mb-2">
    <div class="col-xl-3 col-md-6 col-sm-6 p-2">
        <div class="box-card text-right mini animate__animated animate__flipInY   ">
                        <i><img style="border-radius:10px" class="shadow" src="{{ asset('storage/' . $personel->userData->profile_picture) }}" /></i>

            <span class="mb-1 c-first">سرپرست منطقه شما</span>
            <span>{{$personel->name}}</span>
            <!-- <p class="mt-3 mb-1 text-right"><i class="far fas fa-wallet mr-1 c-first"></i> در حال
                پیشرفت</p> -->
        </div>
    </div>
    <div class="col-xl-3 col-md-6 col-sm-6 p-2">
        <div class="box-card text-right mini animate__animated animate__flipInY    "><i
                class="fab far fa-clock b-second" aria-hidden="true"></i>
            <span class="mb-1 c-second">تعداد حواله موقت</span>
            <span>{{$totalInProgressHavaleCount}}</span>
            <!-- <p class="mt-3 mb-1 text-right"><i class="far fas fa-wifi mr-1 c-second"></i>در حال پیشرفت
            </p> -->
        </div>
    </div>
    <div class="col-xl-3 col-md-6 col-sm-6 p-2">
        <div class="box-card text-right mini animate__animated animate__flipInY   "><i
                class="fab far fa-comments b-third" aria-hidden="true"></i>
            <span class="mb-1 c-third"> تعداد درخواست های باز</span>
            <span>{{$totalOpenRequestsCount}}</span>
            <!-- <p class="mt-3 mb-1 text-right"><i class="fab fa-whatsapp mr-1 c-third"></i>در حال پیشرفت
            </p> -->
        </div>
    </div>
    <div class="col-xl-3 col-md-6 col-sm-6 p-2">
        <div class="box-card text-right mini animate__animated animate__flipInY   "><i
                class="fab far fa-gem b-forth" aria-hidden="true"></i>
            <span class="mb-1 c-forth">متراژ کل ارسال شده سال جاری</span>
            <span>{{number_format($totalCompletedRequests, 0)}}</span>
            <!-- <p class="mt-3 mb-1 text-right"><i class="fab fa-bluetooth mr-1 c-forth"></i>در حال پیشرفت
            </p> -->
        </div>
    </div>
</div>



<div class="row m-2 mb-1">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p-2">
        <div class="alert text-dir-rtl text-right  alert-third alert-shade alert-dismissible fade show"
            role="alert">
            <strong>نماینده فروش گرامی، </strong> لطفا جهت بررسی درخواست های باز به زیر منو درخواست بخش مدیریت درخواست ها مراجعه نمایید
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
    </div>

    <div class="alert col-12  alert-success alert-shade-white bd-side alert-dismissible fade show "
            role="alert"> 
            
            @php
            $monthNames = ['فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 
                           'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'];
            $monthName = $monthNames[ $userTargetData['month'] - 1 ] ?? 'نامشخص';
        @endphp
                <?php $totalCompletedRequestsThisMonth = number_format($totalCompletedRequestsThisMonth, 2, '.', '') ?>

        <div class="alert alert-info">
            تارگت ماه جاری ({{ $monthName }}) شما: {{ number_format($userTargetData['target']) }}
            /
            میزان تارکت محقق شده ی ماه جاری شما : {{$totalCompletedRequestsThisMonth}}
        </div>
        
        


            @if( $userTargetData['target'] != 0)
            {{-- {{ number_format( ( $totalCompletedRequestsThisMonth * 100 )/ $userTargetData['target']  , 0)}} % --}}
          
            <div class="progress mt-3">
            <div class="progress-bar progress-bar-striped progress-bar-animated text-center" role="progressbar" style="width:
            {{ number_format( ( $totalCompletedRequestsThisMonth * 100 )/ $userTargetData['target']  , 0)}}%;"
            aria-valuenow="
            {{ number_format( ( $totalCompletedRequestsThisMonth * 100 )/ $userTargetData['target']  , 0)}}"
            aria-valuemin="0" aria-valuemax="100"> 
            {{ number_format( ( $totalCompletedRequestsThisMonth * 100 )/ $userTargetData['target']  , 0)}}%
            </div>

            </div>

        </div>
        @else
        0%
        <div class="progress mt-3">
            <div class="progress-bar progress-bar-striped progress-bar-animated text-center" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>

            </div>

        </div>
            @endif


</div>


<div class="row m-1">
    <div class="col-xs-1 col-sm-1 col-md-8 col-lg-8 p-2">
        <div class="card shade h-100">
            <div class="card-body">
                <h5 class="card-title">درخواست‌های ارسال شده به تفکیک ماه</h5>
    
                <hr>
                <canvas id="myChart5"></canvas>
    
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // داده‌های ارسال شده از کنترلر
                        const monthLabels = @json($monthLabels);  // ماه‌های سال
                        const monthlyData = @json($monthlyData);  // متراژ ارسال شده در هر ماه
    
                        var mixChart = document.getElementById('myChart5');
                        var mixedChart = new Chart(mixChart, {
                            type: 'bar',
                            data: {
                                labels: monthLabels.map(month => month + ' ماه'),  // نمایش "ماه" بعد از شماره ماه
                                datasets: [
                                    {
                                        label: 'متراژ درخواست‌های ارسال شده',
                                        data: monthlyData,  // داده‌های متراژ برای هر ماه
                                        backgroundColor: ['#ff6384', '#4bc0c0', '#ffcd56', '#c9cbcf', '#36a2eb', '#ff6347', '#ff9966', '#66b3ff', '#ff6666', '#99ff99', '#cc99ff', '#ffcc66']
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    x: {
                                        title: {
                                            display: true,
                                            text: 'ماه‌ها'
                                        }
                                    },
                                    y: {
                                        title: {
                                            display: true,
                                            text: 'متراژ'
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
    

    <!-- <div class="col-xs-1 col-sm-1 col-md-4 col-lg-4 p-2">
        <div class="card flat f-first h-100">
            <div class="card-body">
                <h5 class="card-title">افزونه آب و هوا</h5>

                <hr>
                <a class="weatherwidget-io" href="https://forecast7.com/en/37d5545d08/urmia/"
                    data-label_1="URMIA" data-label_2="WEATHER" data-icons="Climacons Animated"
                    data-days="5" data-textcolor="#fafafaad"></a>


            </div>

        </div>
    </div> -->

    <div class="col-xs-1 col-sm-1 col-md-4 col-lg-4 p-2">
        <div class="card shade h-100">
            <div class="card-body">
            <h5 class="card-title">بیشترن محصولات ارسال شده</h5>

                <hr>
                <canvas  id="myChart4" width="10" height="11"></canvas>

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
            </div>

        </div>
    </div>
</div>

<div class="row m-1">
 
    <div class="col-xs-1 col-sm-1 col-md-6 col-lg-6 p-2">
        <div class="card shade ">
            <div class="card-body">
                <h5 class="card-title">5 نماینده برتر از نظر متراژ فروش</h5>

                <hr>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col" style="width:70px">ردیف</th>
                            <th scope="col">نام</th>
                            <th scope="col">شهر</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $n=0 ?>
                        @foreach($topDistributors as $distributor)
                        <?php $n++ ?>
                            <tr>
                                <th scope="row">{{$n}}</th>
                                <td>{{ $distributor->name }}</td>
                                <td>{{ $distributor->city_name  }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    
                </table>
            </div>

        </div>
    </div>
    {{-- <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 p-2">
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
    </div> --}}

    <div class="col-xs-1 col-sm-1 col-md-6 col-lg-6 p-2">
        <div class="card shade h-100">
            <div class="card-body">
                <h5 class="card-title">درخواست‌های ارسال شده به تفکیک ماه</h5>
    
                <hr>
                <canvas id="myChart16"></canvas>
    
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
    // داده‌های ارسال شده از کنترلر
    const sizeLabels = @json($sizeLabels);  // سایز محصولات
    const sizeData = @json($sizeData);      // مجموع متراژ خرید هر سایز

    var mixChart = document.getElementById('myChart16');
    var mixedChart = new Chart(mixChart, {
        type: 'bar',
        data: {
            labels: sizeLabels,  // سایز محصول به جای ماه‌ها
            datasets: [
                {
                    label: 'مجموع متراژ خریداری شده',
                    data: sizeData,
                    backgroundColor: ['#ff6384', '#4bc0c0', '#ffcd56', '#c9cbcf', '#36a2eb', '#ff6347', '#ff9966', '#66b3ff', '#ff6666', '#99ff99', '#cc99ff', '#ffcc66']
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'سایز محصولات'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'متراژ کل'
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
