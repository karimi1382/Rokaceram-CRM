<x-app-layout>
    <div class="container-fluid">
        <div class="row m-1 pb-4 mb-3">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p-2">
                <div class="page-header breadcrumb-header">
                    <div class="row align-items-end">
                        <div class="col-lg-8">
                            <div class="page-header-title text-left-rtl">
                                <div class="d-inline">
                                    <h3 class="lite-text">اطلاعات حواله‌های کاربر</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i></a></li>
                                <li class="breadcrumb-item active">اطلاعات حواله</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row m-2 mb-1 bg-light p-5 rounded row justify-content-md-center">
            <div class="col-md-12">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
            </div>


            @php
    $date = \Morilog\Jalali\Jalalian::fromFormat('Y/m/d', "$shamsiYear/$shamsiMonth/01");
@endphp
<div class="alert alert-primary text-center" role="alert">
    حواله‌های نماینده در 
    <strong>{{ $date->format('%B') }} {{ $shamsiYear }}</strong>
</div>


            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>شماره حواله</th>
                            <th>وضعیت</th>
                            <th>متراژ (متر)</th>
                            <th>تاریخ ثبت</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($userHavaleStats as $havaleNumber => $havaleInfo)
                            @foreach ($havaleInfo as $info)
                            @if($info['status'] != 'In Progress' )
                                <tr>
                                    <td>{{ $havaleNumber }}</td>
                                    <td>
                                        @if($info['status']== 'Approved' )
                                            رزرو شده
                                        @endif
                                        @if($info['status']== 'Completed' )
                                            ارسال شده
                                        @endif
                                    </td>
                                    <td>{{ $info['request_size'] }} متر</td>
                                    <td>{{ $info['date'] }}</td>


                                </tr>
                                @endif
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">هیچ داده‌ای یافت نشد.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
