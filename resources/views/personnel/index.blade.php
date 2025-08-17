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
                            <span class="lite-text ">داشبورد پرسنل</span>
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

{{-- @if(!$connectionIsOk)
    <div class="alert alert-danger">
        ارتباط با سرور اطلاعات حواله برقرار نشد. لطفاً دوباره تلاش کنید.
    </div>
@endif --}}

    @if(isset($error))
        <div class="alert alert-danger">
            {{ $error }}
        </div>
    @else
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
        <div class="box-card text-right mini animate__animated animate__flipInY   "><i
                class="fab far fa-chart-bar b-first" aria-hidden="true"></i>
            <span class="mb-1 c-first">تعداد درخواست های باز</span>
            <span>{{ $openRequests }}</span>
            <!-- <p class="mt-3 mb-1 text-right"><i class="far fas fa-wallet mr-1 c-first"></i> در حال
                پیشرفت</p> -->
        </div>
    </div>
    <div class="col-xl-3 col-md-6 col-sm-6 p-2">
        <div class="box-card text-right mini animate__animated animate__flipInY    "><i
                class="fab far fa-clock b-second" aria-hidden="true"></i>
            <span class="mb-1 c-second">تعداد حواله های منتظر تایید</span>
            <span>{{$In_Progress_request}}</span>
            <!-- <p class="mt-3 mb-1 text-right"><i class="far fas fa-wifi mr-1 c-second"></i>در حال پیشرفت
            </p> -->
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 col-sm-6 p-2">
        <div class="box-card text-right mini animate__animated animate__flipInY   "><i
                class="fab far fa-comments b-third" aria-hidden="true"></i>
            <span class="mb-1 c-third">متراژ رزرو شده نماینده های من  </span>
            <span>{{ number_format($approvedMeter, 2, '/', ',') }}</span>
            <!-- <p class="mt-3 mb-1 text-right"><i class="fab fa-whatsapp mr-1 c-third"></i>در حال پیشرفت
            </p> -->
        </div>
    </div>
    <div class="col-xl-3 col-md-6 col-sm-6 p-2">
        <div class="box-card text-right mini animate__animated animate__flipInY   "><i
                class="fab far fa-gem b-forth" aria-hidden="true"></i>
            <span class="mb-1 c-forth">متراژ فروخته شده</span>
            <span>{{ number_format($completed_MR, 2, '/', ',') }}</span>
            <!-- <p class="mt-3 mb-1 text-right"><i class="fab fa-bluetooth mr-1 c-forth"></i>در حال پیشرفت
            </p> -->
        </div>
    </div>



           
           
            <!-- <p class="mt-3 mb-1 text-right"><i class="fab fa-whatsapp mr-1 c-third"></i>در حال پیشرفت
            </p> -->
   



</div>






<div class="row m-2 mb-1">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p-2">
        <div class="alert text-dir-rtl text-right  alert-third alert-shade alert-dismissible fade show"
            role="alert">
            <span class="m-2 ">در حال حاظر تعداد
                <span>{{$childCount}}</span>
                نماینده زیر مجموعه شما هستند
            </span>
        </div>
    </div>
    <div class="alert col-12 alert-success alert-shade-white bd-side alert-dismissible fade show" role="alert"> 

        @php
            $monthNames = ['فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 
                           'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'];
            $monthName = $monthNames[$currentMonth - 1] ?? 'نامشخص';
    
            $formattedSoldMeter = number_format($soldMeterInThisMonth, 2, '.', '');
            $formattedTarget = number_format($userTarget, 2, '.', '');
    
            $progress = ($userTarget != 0) ? round(($soldMeterInThisMonth * 100) / $userTarget) : 0;
        @endphp
        @php
            $monthNames = [
                1 => 'فروردین', 2 => 'اردیبهشت', 3 => 'خرداد', 4 => 'تیر',
                5 => 'مرداد', 6 => 'شهریور', 7 => 'مهر', 8 => 'آبان',
                9 => 'آذر', 10 => 'دی', 11 => 'بهمن', 12 => 'اسفند'
            ];

            $monthName = $monthNames[$currentMonth] ?? '---';
            $formattedSoldMeter = number_format($monthlyData[$currentMonth - 1] ?? 0, 2);
        @endphp
        <div class="alert alert-info">
            تارگت ماه جاری ({{ $monthName }}) شما: {{ number_format($userTarget) }}
            /
            میزان تارگت محقق شده‌ی ماه جاری شما: {{ $formattedSoldMeter }}
        </div>

    
        <div class="progress mt-3">
            <div class="progress-bar progress-bar-striped progress-bar-animated text-center" 
                 role="progressbar" 
                 style="width: {{ $progress }}%;" 
                 aria-valuenow="{{ $progress }}" 
                 aria-valuemin="0" 
                 aria-valuemax="100">
                {{ $progress }}%
            </div>
        </div>
    
    </div>
    
    


</div>


<div class="row m-1">
    <div class="col-xs-1 col-sm-1 col-md-8 col-lg-8 p-2">
        <div class="card shade h-100">
            <div class="card-body">
                <h5 class="card-title">درخواست های ارسال شده هر ماه از سال جاری</h5>

                <hr>
                <div class="card p-3 mb-4 shadow-sm rounded-3">
                    <h5 class="mb-3">نمودار متراژ درخواست‌های ارسال شده در سال جاری</h5>
                
                    <canvas id="myChart5"></canvas>
                
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                
                            // داده‌های ارسال شده از کنترلر
                            const monthLabels = @json($monthLabels);  // ماه‌های سال
                            const monthlyData = @json($monthlyData);  // متراژ درخواست‌های ارسال شده در هر ماه
                
                            const ctx = document.getElementById('myChart5').getContext('2d');
                
                            const chartData = {
                                labels: monthLabels.map(month => `ماه ${month}`),
                                datasets: [
                                    {
                                        label: 'متراژ درخواست‌های ارسال شده',
                                        data: monthlyData,
                                        backgroundColor: [
                                            '#ff6384', '#4bc0c0', '#ffcd56', '#c9cbcf', '#36a2eb', '#ff6347',
                                            '#ff9966', '#66b3ff', '#ff6666', '#99ff99', '#cc99ff', '#ffcc66'
                                        ],
                                        borderRadius: 5
                                    }
                                ]
                            };
                
                            const chartOptions = {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        display: true,
                                        labels: {
                                            font: { size: 14 },
                                            color: '#333'
                                        }
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                let value = context.raw || 0;
                                                return `متراژ: ${value.toLocaleString('fa-IR', { minimumFractionDigits: 2 })}`;
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    x: {
                                        title: {
                                            display: true,
                                            text: 'ماه‌ها',
                                            color: '#333',
                                            font: { size: 14 }
                                        }
                                    },
                                    y: {
                                        beginAtZero: true,
                                        title: {
                                            display: true,
                                            text: 'مقدار متراژ (m²)',
                                            color: '#333',
                                            font: { size: 14 }
                                        }
                                    }
                                }
                            };
                
                            new Chart(ctx, {
                                type: 'bar',
                                data: chartData,
                                options: chartOptions
                            });
                        });
                    </script>
                </div>
                


                <hr class="hr-dashed">
                <!-- <p class="text-center c-danger">یک نمونه از نمودار</p> -->
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
    const models = @json($modelLabels_2);
    const modelData = @json($modelData_2);
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
<!-- <div class="row mb-2 m-2">
    <div class="col-xl-8 col-md-6 col-sm-6 p-2">
        <div class="box-dash h-100 pastel animate__animated animate__flipInY b-second   "><i
                class="fab far fa-clock" aria-hidden="true"></i>

            <span>27</span>
            <hr class="m-0 ">
            <span>بازدید</span>
            <a href="#" class="small-box-footer">اطلاعات بیشتر <i
                    class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 col-sm-6 p-2">
        <div class="box-card h-100 flat f-main animate__animated animate__flipInY   ">

            <iframe
                src="https://www.zeitverschiebung.net/clock-widget-iframe-v2?language=en&size=medium&timezone=Asia%2FTehran"
                width="100%" height="115" frameborder="0" seamless></iframe>
        </div>
    </div>



</div> -->
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
                            <th scope="col">متراژ سال جاری</th>

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
                                <td>{{ number_format($distributor->total_mr, 2) }} </td>

                                <td>{{ $distributor->city_name  }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    
                </table>
            </div>

        </div>
    </div>

    <div class="col-xs-1 col-sm-1 col-md-6 col-lg-6 p-2">
        <div class="card shade h-100">
            <div class="card-body">
                <h5 class="card-title">درخواست‌های ارسال شده به سایز</h5>
    
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

<!-- 
<div class="row m-1">
    <div class="col-xs-1 col-sm-1 col-md-8 col-lg-8 p-2">
        <div class="alert col-12  alert-success alert-shade-white bd-side alert-dismissible fade show"
            role="alert">
            <strong>هشدار!</strong>این یک متن هشدار است.

        </div>
        <div id="accordion " class="accordion card shade outlined o-forth w-100">
            <div class="">
                <div class="card-header mr-3 ml-3 pr-0 pl-0" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link c-grey w-100 m-0 text-right" data-toggle="collapse"
                            data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            عنوان شماره یک
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </h5>
                </div>

                <div id="collapseOne" class="collapse show" aria-labelledby="headingOne"
                    data-parent="#accordion">
                    <div class="card-body">
                        لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ، و با استفاده از
                        طراحان گرافیک است، چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که
                        لازم است، و برای شرایط فعلی تکنولوژی مورد نیاز، و کاربردهای متنوع با هدف بهبود
                        ابزارهای کاربردی می باشد، کتابهای زیادی در شصت و سه درصد گذشته حال و آینده،
                        شناخت فراوان جامعه و متخصصان را می طلبد، تا با نرم افزارها شناخت بیشتری را برای
                        طراحان رایانه ای علی الخصوص طراحان خلاقی، و فرهنگ پیشرو در زبان فارسی ایجاد کرد،
                        در این صورت می توان امید داشت که تمام و دشواری موجود در ارائه راهکارها، و شرایط
                        سخت تایپ به پایان رسد و زمان مورد نیاز شامل حروفچینی دستاوردهای اصلی، و جوابگوی
                        سوالات پیوسته اهل دنیای موجود طراحی اساسا مورد استفاده قرار گیرد.
                    </div>
                </div>
            </div>
            <div class="">
                <div class="card-header mr-3 ml-3 pr-0 pl-0" id="headingTwo">
                    <h5 class="mb-0">
                        <button class="btn btn-link c-grey collapsed w-100 m-0 text-right"
                            data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false"
                            aria-controls="collapseTwo">
                            عنوان شماره دو
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </h5>
                </div>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo"
                    data-parent="#accordion">
                    <div class="card-body">
                        لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ، و با استفاده از
                        طراحان گرافیک است، چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که
                        لازم است، و برای شرایط فعلی تکنولوژی مورد نیاز، و کاربردهای متنوع با هدف بهبود
                        ابزارهای کاربردی می باشد، کتابهای زیادی در شصت و سه درصد گذشته حال و آینده،
                        شناخت فراوان جامعه و متخصصان را می طلبد، تا با نرم افزارها شناخت بیشتری را برای
                        طراحان رایانه ای علی الخصوص طراحان خلاقی، و فرهنگ پیشرو در زبان فارسی ایجاد کرد،
                        در این صورت می توان امید داشت که تمام و دشواری موجود در ارائه راهکارها، و شرایط
                        سخت تایپ به پایان رسد و زمان مورد نیاز شامل حروفچینی دستاوردهای اصلی، و جوابگوی
                        سوالات پیوسته اهل دنیای موجود طراحی اساسا مورد استفاده قرار گیرد.
                    </div>
                </div>
            </div>
            <div class="">
                <div class="card-header mr-3 ml-3 pr-0 pl-0" id="headingThree">
                    <h5 class="mb-0">
                        <button class="btn btn-link c-grey collapsed w-100 m-0 text-right"
                            data-toggle="collapse" data-target="#collapseThree" aria-expanded="false"
                            aria-controls="collapseThree">
                            عنوان شماره سه
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </h5>
                </div>
                <div id="collapseThree" class="collapse" aria-labelledby="headingThree"
                    data-parent="#accordion">
                    <div class="card-body">
                        لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ، و با استفاده از
                        طراحان گرافیک است، چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که
                        لازم است، و برای شرایط فعلی تکنولوژی مورد نیاز، و کاربردهای متنوع با هدف بهبود
                        ابزارهای کاربردی می باشد، کتابهای زیادی در شصت و سه درصد گذشته حال و آینده،
                        شناخت فراوان جامعه و متخصصان را می طلبد، تا با نرم افزارها شناخت بیشتری را برای
                        طراحان رایانه ای علی الخصوص طراحان خلاقی، و فرهنگ پیشرو در زبان فارسی ایجاد کرد،
                        در این صورت می توان امید داشت که تمام و دشواری موجود در ارائه راهکارها، و شرایط
                        سخت تایپ به پایان رسد و زمان مورد نیاز شامل حروفچینی دستاوردهای اصلی، و جوابگوی
                        سوالات پیوسته اهل دنیای موجود طراحی اساسا مورد استفاده قرار گیرد.
                    </div>
                </div>
            </div>
        </div>
    </div>
  

</div> -->




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
