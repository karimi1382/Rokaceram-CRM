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
            </div>
        </div>

        @if(!$connectionIsOk)
            <div class="alert alert-danger">ارتباط با سرور قطع می‌باشد.</div>
        @endif

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

            {{-- فیلتر تاریخ --}}
            <div class="card shadow-sm border-0 mb-4 w-100">
                <div class="card-body">
                    <h5 class="mb-3 fw-bold text-primary">فیلتر بر اساس تاریخ</h5>
                    <form method="GET" action="{{ route('user.children', ['id' => $userId]) }}" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="year" class="form-label">انتخاب سال</label>
                            <select name="year" id="year" class="form-select">
                                @for($i = 1402; $i <= 1406; $i++)
                                    <option value="{{ $i }}" {{ $i == $shamsiYear ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="month" class="form-label">انتخاب ماه</label>
                            <select name="month" id="month" class="form-select">
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

                        <div class="col-md-4 d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter ms-1"></i> اعمال فیلتر
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- جدول --}}
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width:70px">ردیف</th>
                            <th>نام و نام خانوادگی</th>
                            <th>شماره تماس</th>
                            <th>استان | شهرستان</th>
                            <th>نام کاربری</th>
                            <th>رزرو شده (متر)</th>
                            <th>ارسال شده (متر)</th>
                            <th>تارگت ماه (متر)</th>      {{-- ستون جدید --}}
                            <th>درصد تحقق تارگت</th>     {{-- ستون جدید --}}
                            <th>ارسال شده (سال جاری)</th>
                            <th>حواله‌های بیشتر</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($childrenProfiles->isEmpty())
                            <tr>
                                <td colspan="11" class="text-center">هیچ داده‌ای یافت نشد.</td>
                            </tr>
                        @else
                            @foreach ($childrenProfiles as $index => $profile)
                                @php
                                    $reserved  = (float) ($profile->reserved_request_size ?? 0);
                                    $completed = (float) ($profile->completed_request_size ?? 0);
                                    $yearly    = (float) ($profile->yearly_completed_request_size ?? 0);
                                    $target    = (float) ($profile->target_month ?? 0);
                                    $percent   = (int)   ($profile->target_percent ?? 0);
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $profile->user->name ?? 'نامشخص' }}</td>
                                    <td>{{ $profile->phone ?? 'نامشخص' }}</td>
                                    <td>{{ $profile->city->name ?? 'نامشخص' }} | {{ $profile->city->state ?? 'نامشخص' }}</td>
                                    <td>{{ $profile->user->email ?? 'نامشخص' }}</td>

                                    {{-- رند به بالا + فرمت --}}
                                    <td>{{ number_format(ceil($reserved), 0) }}</td>
                                    <td>{{ number_format(ceil($completed), 0) }}</td>
                                    <td>{{ number_format(ceil($target), 0) }}</td>
                                    <td>{{ $percent }}%</td>
                                    <td>{{ number_format(ceil($yearly), 0) }}</td>

                                    <td>
                                        <a href="{{ route('user.havales', ['userId' => $profile->user->id, 'year' => $shamsiYear, 'month' => $shamsiMonth]) }}" class="btn btn-info">
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
    </div>

    {{-- Modal نمایش حواله‌ها --}}
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
