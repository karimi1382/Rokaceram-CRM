<x-app-layout>
    <div class="container-fluid">
        <!-- عنوان صفحه و breadcrumb -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="fw-bold">حواله‌های تکمیل‌شده</h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i></a></li>
                            <li class="breadcrumb-item active" aria-current="page">حواله‌های تکمیل‌شده</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- نمایش خطا -->
        @if(session()->has('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <!-- فیلد جستجو -->
     

        <!-- فرم فیلتر مدرن و وسط‌چین -->
        <div class="card shadow-sm border-0 mb-3 pt-3" style="max-width: 600px; margin: 0 auto;">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end justify-content-center">
                    <div class="col-md-4 col-12">
                        <label for="month" class="form-label fw-semibold">ماه</label>
                        <select id="month" name="month" class="form-select form-select-sm rounded-pill shadow-sm">
                            @foreach($months as $num => $name)
                                <option value="{{ $num }}" {{ $num == ($month ?? '') ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 col-12">
                        <label for="year" class="form-label fw-semibold">سال</label>
                        <select id="year" name="year" class="form-select form-select-sm rounded-pill shadow-sm">
                            @foreach($years as $y)
                                <option value="{{ $y }}" {{ $y == ($year ?? '') ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 col-12 d-grid mt-md-0 mt-2">
                        <button type="submit" class="btn btn-success rounded-pill shadow-sm fw-bold">
                            <i class="fas fa-filter me-1 "></i> فیلتر
                        </button>
                    </div>
                </form>
            </div>
            <div class="m-3">
               <p class="mb-2"> دنبال چه اطلاعاتی هستید ؟</p>
                <input type="text" id="search" class="form-control" placeholder="کلمه یا حرف یا عدد مورد نظر را وارد کنید">
            </div>
        </div>

        <div class="row m-2 mb-1 bg-light p-5 rounded">
            <!-- جدول نتایج -->
            <div class="table-responsive">
                <table class="table table-striped table-hover text-center">
                    <thead>
                        <tr>
                            <th scope="col" style="width: 60px;">ردیف</th>
                            <th scope="col">نام نماینده</th>
                            <th scope="col">نام استان</th>
                            <th scope="col">نام شهرستان</th>
                            <th scope="col">نام کشور</th>
                            <th scope="col">شماره حواله</th>
                            <th scope="col" style="width: 100px;">تاریخ ارسال</th>
                            <th scope="col" style="width: 160px;">وضعیت درخواست</th>
                            <th scope="col" style="width: 100px;">جزئیات</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @php $n = 0; @endphp
                        @forelse ($uniqueRequests as $havaleNumber => $requestsForHavale)
                            @php $requestItem = $requestsForHavale->first(); @endphp
                            <tr>
                                <td>{{ ++$n }}</td>
                                <td>{{ $requestItem->user_name }}</td>
                                <td>{{ $requestItem->city_name ?? '-' }}</td>
                                <td>{{ $requestItem->ostan_name ?? '-' }}</td>
                                <td>{{ $requestItem->country_name ?? '-' }}</td>
                                <td>{{ $requestItem->havale_number }}</td>
                                <td>{{ $requestItem->jalali_created_at ?? '-' }}</td>
                                <td><span class="badge bg-success">تکمیل شده</span></td>
                                <td>
                                    <a href="{{ route('dis_requests.havale_data', $requestItem->havale_number) }}" class="btn btn-info btn-sm px-3">جزئیات</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-muted fst-italic">هیچ داده‌ای برای نمایش وجود ندارد.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            // زمانی که کاربر شروع به تایپ می‌کند
            $('#search').on('keyup', function() {
                var value = $(this).val().toLowerCase(); // گرفتن کلمه جستجو به صورت حروف کوچک

                // فیلتر کردن رکوردها در جدول بر اساس تمام ستون‌ها
                $('#tableBody tr').each(function() {
                    var rowText = $(this).text().toLowerCase(); // گرفتن متن داخل کل ردیف
                    $(this).toggle(rowText.indexOf(value) > -1); // بررسی وجود کلمه جستجو در متن ردیف
                });
            });
        });
    </script>
</x-app-layout>
