<x-app-layout>
    <div class="container-fluid">
        <div class="row m-1 pb-4 mb-3">
            <div class="col-xs-12">
                <div class="page-header breadcrumb-header">
                    <div class="row align-items-end">
                        <div class="col-lg-8">
                            <h3 class="lite-text">همه حواله‌های ثبت‌شده</h3>
                        </div>
                        <div class="col-lg-4">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i></a></li>
                                <li class="breadcrumb-item active">حواله‌ها</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(session()->has('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row m-2 mb-1 bg-light p-5 rounded">
            <div class="table-responsive">
                <h3 class="bg-warning text-center text-white p-4 ">حواله های رزرو شده بدون تایید مالی </h3>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ردیف</th>
                            <th>نام نماینده</th>
                            <th>نام شهر</th>
                            <th>شماره حواله</th>
                            <th>وضعیت درخواست</th>
                            <th>مهلت تایید مالی</th>
                            <th>تاریخ ایجاد</th>
                            <th style="width:20px" class="text-center">جزئیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $n=0 ?>
                        @foreach ($uniqueRequests as $filePath => $requestsForFile)
                        @php $request = $requestsForFile->first(); @endphp
                        @if($request->status == 'In Progress')
                            <tr>
                                <td>{{ ++$n }}</td>
                                <td>{{ $request->user_name }}</td> {{-- از select در کنترلر گرفتیم --}}
                                <td>{{ $request->city_name ?? '-' }}</td> {{-- بعداً باید در کنترلر اضافه بشه --}}
                                <td>{{ $request->havale_number }}</td>
                                <td>
                                    @if($request->status == 'In Progress')
                                        رزرو شده موقت - در انتظار تایید مالی
                                    @elseif($request->status == 'Approved')
                                        در انتظار بارگیری
                                    @elseif($request->status == 'Completed')
                                        تکمیل شده
                                    @endif
                                </td>
                                <td>
                                    @if($request->status == 'In Progress')
                                        @php
                                            $remainingDays = $remainingDaysArray[$request->id] ?? null;
                                        @endphp
                                        {{ $remainingDays !== null ? $remainingDays . ' روز' : 'تایید شده' }}
                                    @else
                                        تایید شده
                                    @endif
                                </td>
                                <td>{{ $request->jalali_created_at }}</td>
                                <td>
                                    <a href="{{ route('dis_requests.havale_data', $request->havale_number) }}" class="btn btn-info">مشاهده جزئیات</a>
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                    
                </table>
            </div>
        </div>



        <div class="row m-2 mb-1 bg-light p-5 rounded">
            <div class="table-responsive">
                <h3 class="bg-success text-center text-white p-4 ">حواله های رزرو شده با تایید مالی  - در انتظار بارگیری</h3>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ردیف</th>
                            <th>نام نماینده</th>
                            <th>نام شهر</th>
                            <th>شماره حواله</th>
                            <th>وضعیت درخواست</th>
                            <th>وضعیت حواله  </th>
                            <th>تاریخ ایجاد</th>
                            <th style="width:20px" class="text-center">جزئیات</th>
                        </tr>
                    </thead>
                    <tbody>
                  
                        <?php $q=0 ?>
                        @foreach ($uniqueRequests as $filePath => $requestsForFile)
                        @php $request = $requestsForFile->first(); @endphp
                        @if($request->status == 'Approved')
                           
                            <tr>
                                <td>{{ ++$q }}</td>
                                <td>{{ $request->user_name }}</td> {{-- از select در کنترلر گرفتیم --}}
                                <td>{{ $request->city_name ?? '-' }}</td> {{-- بعداً باید در کنترلر اضافه بشه --}}
                                <td>{{ $request->havale_number }}</td>
                                <td>
                                    @if($request->status == 'In Progress')
                                        رزرو شده موقت - در انتظار تایید مالی
                                    @elseif($request->status == 'Approved')
                                        در انتظار بارگیری
                                    @elseif($request->status == 'Completed')
                                        تکمیل شده
                                    @endif
                                </td>
                                <td>
                                    @if($request->status == 'In Progress')
                                        @php
                                            $remainingDays = $remainingDaysArray[$request->id] ?? null;
                                        @endphp
                                        {{ $remainingDays !== null ? $remainingDays . ' روز' : 'تایید شده' }}
                                    @else
                                        تایید شده
                                    @endif
                                </td>
                                <td>{{ $request->jalali_created_at }}</td>
                                <td>
                                    <a href="{{ route('dis_requests.havale_data', $request->havale_number) }}" class="btn btn-info">مشاهده جزئیات</a>
                                </td>
                            </tr>
                            @endif
                        @endforeach

                        
                    </tbody>
                    
                </table>
            </div>
        </div>



        
    </div>
</x-app-layout>
