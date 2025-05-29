<x-app-layout>
    <div class="container-fluid ">

    <div class="row  m-1 pb-4 mb-3 ">
        <div class="col-xs-12  col-sm-12  col-md-12  col-lg-12 p-2">
            <div class="page-header breadcrumb-header ">
                <div class="row align-items-end ">
                    <div class="col-lg-8">
                        <div class="page-header-title text-left-rtl">
                            <div class="d-inline">
                                <h3 class="lite-text ">جزئیات عملکرد سرپرست ها</h3>
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

    <div class="col-md-12">
        @if(session()->has('success'))
            <div class="alert alert-success">
                {{ session('success') }}
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


    @php
    $monthNames = [
        1 => 'فروردین',
        2 => 'اردیبهشت',
        3 => 'خرداد',
        4 => 'تیر',
        5 => 'مرداد',
        6 => 'شهریور',
        7 => 'مهر',
        8 => 'آبان',
        9 => 'آذر',
        10 => 'دی',
        11 => 'بهمن',
        12 => 'اسفند',
    ];
@endphp

@if(request('year') && request('month'))
    <div class="alert alert-info">
        نمایش گزارش حواله‌ها برای ماه <strong>{{ $monthNames[request('month')] }}</strong> سال <strong>{{ request('year') }}</strong>
    </div>
@endif



    <div class="row m-2 mb-1 bg-light p-5 rounded row justify-content-md-center">
        <h1 class="m-4">جزئیات تارگت سرپرست فروش : <strong>{{ $parentUser->name }}</strong></h1>
        {{-- <p class="m-4"><strong>میزان تارگت مورد نظر</strong>: {{ $parentUser->target }}</p> --}}

        <div class="table-responsive">
            <table border="1" class="table table-bordered">
                <thead>
                    <tr>
                        <th>نام نماینده</th>
                        <th>متراژ رزرو شده</th>
                        <th>متراژ بارگیری شده</th>
                        {{-- <th>جمع کل</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach($childrenProfiles as $child)
                        <tr>
                            <td>{{ $child->user->name }}</td>
                            <td>{{ number_format($child->reserved_request_size, 2) }}</td>
                            <td>{{ number_format($child->completed_request_size, 2) }}</td>
                            {{-- <td>{{ number_format($child->reserved_request_size + $child->completed_request_size, 2) }}</td> --}}
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    </div>
</x-app-layout>
