<!-- resources/views/dis_requests/havale_show.blade.php -->

<x-app-layout>
    <div class="container-fluid">
        <div class="row m-1 pb-4 mb-3">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p-2">
                <div class="page-header breadcrumb-header">
                    <div class="row align-items-end">
                        <div class="col-lg-8">
                            <div class="page-header-title text-left-rtl">
                                <h3 class="lite-text">مشاهده اطلاعات حواله برای شماره حواله {{ $request->file_path }}</h3>
                            </div>
                        </div>
                       
                    </div>
                </div>
            </div>
        </div>

        <!-- Show success or error messages -->
        @if(session()->has('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
            @if($remainingDays>0 && $havaleData[0]->mali == 0)
        <div class="alert alert-warning">
        <h3>مهلت تایید مالی : 
            {{$remainingDays}} روز</h3>
            <p class="pt-3">بعد از مهلت مشخص شده در صورت عدم تایید مالی رزرو لغو شده و کالاهای رزرو شده به موجودی باز خواهد گشت</p>
        </div>
        @endif
<h2 class="pb-4">نام مشتری : {{$disRequest->user->name}}</h2>
        <!-- First Data (havale, mali, tavali) - 3 Columns -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card shadow-lg rounded border-0">
                    <div class="card-body text-center">
                        <p><strong>شماره حواله:</strong> <span class="font-weight-bold">{{ $havaleData[0]->havale }}</span></p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-lg rounded border-0">
                    <div class="card-body text-center">
                        <p>
                            <strong>تایید مالی:</strong>
                            @if($havaleData[0]->mali == 0)
                                <span class="text-danger font-weight-bold">تایید مالی ندارد</span>
                            @elseif($havaleData[0]->mali == 1)
                                <span class="text-success font-weight-bold">تایید مالی</span>
                            @else
                                <span class="text-muted font-weight-bold">نامشخص</span>
                            @endif
                        </p>
                       
                        
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-lg rounded border-0">
                    <div class="card-body text-center">
                        <p>
                            <strong>وضعیت :</strong>
                            @if($havaleData[0]->mali == 1)

                                @if(is_null($havaleData[0]->tavali))
                                    <span class="text-warning font-weight-bold">در انتظار بارگیری</span>
                                @else
                                    <span class="text-success font-weight-bold">بارگیری شده</span>
                                @endif
                            @else
                                <span class="text-warning font-weight-bold">رزرو موقت</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Show Send_Info Once if not null -->
        @if(!is_null($havaleData[0]->Send_Info))
        <div class="row justify-content-center mb-4">
            <div class="col-md-12">
                <div class="card shadow-lg rounded border-0">
                    <div class="card-body ">
                        <strong>اطلاعات ارسال:</strong>
                        <p class="font-weight-bold">{{ $havaleData[0]->Send_Info }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Product Data Cards - Two Cards Per Row -->
        <div class="row">
            @foreach ($havaleData as $index => $havale)
                <!-- Start Card -->
                <div class="col-md-6 mb-4">
                    <div class="card shadow-lg rounded border-0">
                        <div class="card-body">
                            <!-- Box for Product Data -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card border-primary">
                                        <div class="card-header text-center">
                                            <p class="card-title text-center"> {{ $havale->product_name }}</p>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group list-group-flush">
                                                <!-- Product Code -->
                                                <li class="list-group-item d-flex justify-content-between">
                                                    <strong>کد محصول:</strong>
                                                    <span class="font-weight-bold">{{ $havale->product_code }}</span>
                                                </li>
                                                <!-- Product MR -->
                                                <li class="list-group-item d-flex justify-content-between">
                                                    <strong>متراژ:</strong>
                                                    <span class="font-weight-bold">{{ number_format( $havale->product_MR, 2, '/', ',') }}</span>
                                                </li>
                                                <!-- Carton Count (remove .00) -->
                                                <li class="list-group-item d-flex justify-content-between">
                                                    <strong>تعداد کارتن:</strong>
                                                    <span class="font-weight-bold">{{ number_format($havale->product_carton_MR, 0) }}</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Card -->

                @if (($index + 1) % 2 == 0) <!-- To display two cards per row -->
                    </div>
                    <div class="row">
                @endif
            @endforeach
        </div>
    </div>
</x-app-layout>
