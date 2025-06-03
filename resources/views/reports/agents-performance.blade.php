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
                                <h3 class="lite-text ">گزارش</h3>
                                <span class="lite-text ">عملکرد نماینده ها</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item "><a href="#"><i class="fas fa-home"></i></a></li>
                            <li class="breadcrumb-item active">گزارشات</li>
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

         
    <div class="row m-2 mb-1 bg-light p-5 rounded row justify-content-md-center">

        <h3 class="text-lg font-semibold mb-3">نمودار خطی عملکرد نماینده‌ها (۶ ماه اخیر)</h3>
        <canvas id="lineChart"></canvas>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const labels = @json($monthlyLabels);
                const rawData = @json($lineChartData);
                const colorPalette = @json($lineChartColors);
        
                const datasets = Object.keys(rawData).map((agentName, index) => {
                    const color = colorPalette[index] || '#000000'; // در صورت نبود، رنگ مشکی پیش‌فرض
                    return {
                        label: agentName,
                        data: labels.map(month => rawData[agentName][month] || 0),
                        borderColor: color,
                        backgroundColor: color,
                        fill: false,
                        tension: 0.3
                    };
                });
        
                const ctx = document.getElementById('lineChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: datasets
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `متراژ: ${context.raw.toLocaleString('fa-IR')} متر`;
                                    }
                                }
                            },
                            legend: {
                                labels: {
                                    font: {
                                        size: 14
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'ماه شمسی'
                                }
                            },
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'متراژ (m²)'
                                }
                            }
                        }
                    }
                });
            });
        </script>
        
        

      </div>


    <div>

        
        <form method="GET" class="m-4 pt-4 pb-4">
            <div class="row">
                <div class="col-md-3">
                    <label>سال شمسی <span class="text-danger">*</span></label>
                    <select name="year" class="form-control" required>
                        @for($y = 1404; $y <= \Morilog\Jalali\Jalalian::now()->getYear(); $y++)
                            <option value="{{ $y }}" {{ $currentYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
    
                <div class="col-md-3">
                    <label>فیلتر ماه</label>
                    <select name="month" class="form-control">
                        <option value="">-- همه ماه‌ها --</option>
                        <option value="1-6" {{ $currentMonth == '1-6' ? 'selected' : '' }}>۶ ماه اول سال</option>
                        <option value="7-12" {{ $currentMonth == '7-12' ? 'selected' : '' }}>۶ ماه دوم سال</option>
                        <option value="spring" {{ $currentMonth == 'spring' ? 'selected' : '' }}>بهار</option>
                        <option value="summer" {{ $currentMonth == 'summer' ? 'selected' : '' }}>تابستان</option>
                        <option value="autumn" {{ $currentMonth == 'autumn' ? 'selected' : '' }}>پاییز</option>
                        <option value="winter" {{ $currentMonth == 'winter' ? 'selected' : '' }}>زمستان</option>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $currentMonth == $m ? 'selected' : '' }}>ماه {{ $m }}</option>
                        @endfor
                    </select>
                </div>
    
                <div class="col-md-3 align-self-end">
                    <button class="btn btn-primary mt-2">اعمال فیلتر</button>
                </div>
            </div>
        </form>


    </div>
    <div class="row m-2 mb-1 bg-light p-5 rounded row justify-content-md-center">


       
        
       
       
        <h2 class="mb-4">نمودار عملکرد نماینده‌ها (براساس متراژ حواله‌های تکمیل‌شده)</h2>

        <canvas id="agentsChart"></canvas>
    
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const agentLabels = @json($agentLabels);
                const completedMeters = @json($completedMeters);
                const approvedMeters = @json($approvedMeters);
            
                const ctx = document.getElementById('agentsChart').getContext('2d');
            
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: agentLabels,
                        datasets: [
                            {
                                label: 'متراژ بارگیری شده',
                                data: completedMeters,
                                backgroundColor: '#36a2eb',
                                borderRadius: 5
                            },
                            {
                                label: 'متراژ رزرو شده',
                                data: approvedMeters,
                                backgroundColor: '#ffcd56',
                                borderRadius: 5
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: true
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
                                    text: 'نماینده‌ها'
                                },
                                stacked: false,
                            },
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'متراژ (m²)'
                                }
                            }
                        }
                    }
                });
            });
            </script>







    </div>





              
                  <div class="row m-2 mb-1 bg-light p-5 rounded row justify-content-md-center">

                    <h3 class="text-lg font-semibold mt-10 mb-3">سهم بازار نماینده‌ها (براساس متراژ تکمیل‌شده)</h3>
<canvas id="marketShareChart"></canvas>

<script>
document.addEventListener('DOMContentLoaded', function () {
const marketShareData = @json($marketShareData);

const labels = Object.keys(marketShareData);
const data = Object.values(marketShareData);

// تولید رنگ تصادفی برای هر نماینده
const backgroundColors = labels.map((_, i) => {
    const hue = i * (360 / labels.length);
    return `hsl(${hue}, 70%, 60%)`;
});

const ctx = document.getElementById('marketShareChart').getContext('2d');
new Chart(ctx, {
    type: 'doughnut', // یا 'pie'
    data: {
        labels: labels,
        datasets: [{
            data: data,
            backgroundColor: backgroundColors,
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.raw;
                        const total = context.chart._metasets[0].total;
                        const percent = ((value / total) * 100).toFixed(1);
                        return `${label}: ${value.toLocaleString('fa-IR')} متر (${percent}٪)`;
                    }
                }
            },
            legend: {
                position: 'right',
                labels: {
                    font: {
                        size: 14
                    }
                }
            },
            title: {
                display: false
            }
        }
    }
});
});
</script>


                  </div>


                  <div class="row m-2 mb-1 bg-light p-5 rounded row justify-content-md-center">

                    <h2 class="mb-4">متراژ رزرو شده نسبت به بارگیری شده (براساس متراژ )</h2>
        
                    <canvas id="pieChartStatus"></canvas>
                    
                    <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const totalCompleted = @json(array_sum($completedMeters));
                        const totalApproved = @json(array_sum($approvedMeters));
                    
                        const ctx = document.getElementById('pieChartStatus').getContext('2d');
                    
                        new Chart(ctx, {
                            type: 'pie',
                            data: {
                                labels: ['بارگیری شده', 'رزرو شده'],
                                datasets: [{
                                    data: [totalCompleted, totalApproved],
                                    backgroundColor: ['#36a2eb', '#ffcd56'],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        position: 'bottom'
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                let value = context.raw || 0;
                                                return `متراژ: ${value.toLocaleString('fa-IR')} متر`;
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    });
                    </script>
                    
                          </div>

 

 

    
 @endif
    </x-app-layout>
    