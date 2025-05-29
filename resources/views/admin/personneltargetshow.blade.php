<x-app-layout>
    <div class="container-fluid">

        <!-- Breadcrumb -->
        <div class="row m-1 pb-4 mb-3">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p-2">
                <div class="page-header breadcrumb-header">
                    <div class="row align-items-end">
                        <div class="col-lg-8">
                            <div class="page-header-title text-left-rtl">
                                <div class="d-inline">
                                    <h3 class="lite-text">عملکرد سرپرست‌ها</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i></a></li>
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


        <!-- فرم فیلتر -->
        <form method="GET" action="{{ route('ManagerController.personneltargetshow') }}" class="mb-4 p-4 rounded shadow-sm bg-light border">
            <div class="row align-items-end">
                <div class="col-md-4 mb-3">
                    <label for="year" class="form-label">انتخاب سال</label>
                    <select name="year" id="year" class="form-control">
                        @for($y = 1404; $y <= 1406; $y++)
                            <option value="{{ $y }}" {{ $y == $shamsiYear ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>
        
                <div class="col-md-4 mb-3">
                    <label for="month" class="form-label">انتخاب ماه</label>
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
                    <select name="month" id="month" class="form-control">
                        @foreach($monthNames as $num => $name)
                            <option value="{{ $num }}" {{ $num == $shamsiMonth ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>
        
                <div class="col-md-2 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter ml-1"></i> اعمال فیلتر
                    </button>
                </div>
            </div>
        </form>
        

        <!-- Alerts -->
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

        <!-- Table -->
        <div class="row m-2 mb-1 bg-light p-5 rounded row justify-content-md-center">
            <div class="table-responsive">
                <table border="1" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>نام سرپرست</th>
                            <th>تعداد نماینده</th>
                            <th>متراژ رزرو شده</th>
                            <th>متراژ ارسال شده</th>
                            <th>هدف</th>
                            <th>محقق شده</th>
                            <th>کل متراژ بارگیری شده سال جاری</th>

                            <th style="width:50px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($results as $item)
                            <tr>
                                <td>{{ $item['personnel_name'] }}</td>
                                <td>{{ $item['children_count'] }}</td>
                                <td>{{ number_format($item['approved_total'], 2, '/', ) }} متر</td>
                                <td>{{ number_format($item['completed_total'], 2, '/', ) }} متر</td>
                                <td>{{ number_format($item['target'], 0) }} متر</td>
                                <td>
                                    {{ $item['target'] > 0 ? number_format(($item['completed_total'] * 100) / $item['target'], 2) : 0 }}%
                                </td>
                                <td>{{ number_format($item['yearly_completed_total'], 2, '/', ) }} متر</td>

                                <td>
                                    <a href="{{ route('ManagerController.personneltargetshowdetile', ['id' => $item['personnel_id']]) }}?year={{ $shamsiYear }}&month={{ $shamsiMonth }}" class="btn btn-warning">
                                        جزئیات
                                    </a>
                                    
                                                                    </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">داده‌ای یافت نشد.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @endif
    </div>
</x-app-layout>
