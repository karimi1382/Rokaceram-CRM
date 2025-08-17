<x-app-layout>
    <div class="container-fluid">
        <div class="row m-1 pb-4 mb-3">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p-2">
                <div class="page-header breadcrumb-header">
                    <div class="row align-items-end">
                        <div class="col-lg-8">
                            <div class="page-header-title text-left-rtl">
                                <div class="d-inline">
                                    <h3 class="lite-text">نماینده‌های من</h3>
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

                @if(isset($error))
                    <div class="alert alert-danger">
                        {{ $error }}
                    </div>
                @else
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-body">
                            <h5 class="mb-3 font-weight-bold text-primary">فیلتر بر اساس تاریخ</h5>
                            <form method="GET" class="row align-items-end">
                                <div class="col-md-4 mb-4">
                                    <label for="year" class="form-label">انتخاب سال:</label>
                                    <select name="year" id="year" class="form-control">
                                        @for($i = 1404; $i <= 1406; $i++)
                                            <option value="{{ $i }}" {{ $i == $shamsiYear ? 'selected' : '' }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-4 mb-4">
                                    <label for="month" class="form-label">انتخاب ماه:</label>
                                    <select name="month" id="month" class="form-control">
                                        @php
                                            $months = [
                                                '01' => 'فروردین','02' => 'اردیبهشت','03' => 'خرداد','04' => 'تیر',
                                                '05' => 'مرداد','06' => 'شهریور','07' => 'مهر','08' => 'آبان',
                                                '09' => 'آذر','10' => 'دی','11' => 'بهمن','12' => 'اسفند',
                                            ];
                                        @endphp
                                        @foreach($months as $key => $name)
                                            <option value="{{ $key }}" {{ $key == $shamsiMonth ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 mb-4 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-filter ml-1"></i> اعمال فیلتر
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        @if(!isset($error))
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

            <div class="table-responsive">
                @php
                    // مرتب‌سازی بر اساس نام سرپرست
                    $sortedUsers = collect($userDetails ?? [])->sortBy(function($u) {
                        return $u['personel_name'] ?? '';
                    }, SORT_NATURAL | SORT_FLAG_CASE, false)->values();
                @endphp

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width:70px">ردیف</th>
                            <th>نماینده</th>
                            <th>سرپرست</th>
                            <th>درخواست‌های رزرو شده (متر)</th>
                            <th>درخواست‌های ارسال شده (متر)</th>
                            <th>تارگت ماه (متر)</th>
                            <th>درصد تحقق تارگت</th> {{-- ستون جدید --}}
                            <th>کل ارسال سال (متر)</th>
                            <th>حواله‌های بیشتر</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($sortedUsers->isEmpty())
                            <tr>
                                <td colspan="9" class="text-center">هیچ داده‌ای یافت نشد.</td>
                            </tr>
                        @else
                            @foreach ($sortedUsers as $user)
                                @php
                                    $approved = (float) ($user['approved_request_size'] ?? 0);
                                    $completed = (float) ($user['completed_request_size'] ?? 0);
                                    $target    = (float) ($user['target'] ?? 0);

                                    // درصد تحقق تارگت: رُند به بالا
                                    $target_percent = $target > 0 ? ceil(($completed * 100) / $target) : 0;
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $user['user_name'] ?? 'نامشخص' }}</td>
                                    <td>{{ $user['personel_name'] ?? 'نامشخص' }}</td>

                                    {{-- رُند به بالا + فرمت هزارگان --}}
                                    <td>{{ number_format(ceil($approved), 0) }}</td>
                                    <td>{{ number_format(ceil($completed), 0) }}</td>
                                    <td>{{ number_format(ceil($target), 0) }} متر</td>

                                    {{-- ستون جدید: درصد تحقق تارگت --}}
                                    <td>{{ $target_percent }}%</td>

                                    <td>{{ number_format(ceil($user['completed_year_total'] ?? 0), 0) }}</td>

                                    <td>
                                        <a href="{{ route('user.havales', ['userId' => $user['user_id'], 'year' => $shamsiYear, 'month' => $shamsiMonth]) }}" class="btn btn-info">
                                            مشاهده حواله‌ها
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

    <!-- Modal برای نمایش حواله‌ها -->
    <div class="modal fade" id="havaleModal" tabindex="-1" role="dialog" aria-labelledby="havaleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="havaleModalLabel">جزئیات حواله‌ها</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="havaleDetails"></div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
