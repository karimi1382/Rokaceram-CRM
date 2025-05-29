<x-app-layout>

    <div class="container-fluid">

        <div class="row m-1 pb-4 mb-3">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p-2">
                <div class="page-header breadcrumb-header">
                    <div class="row align-items-end">
                        <div class="col-lg-8">
                            <div class="page-header-title text-left-rtl">
                                <div class="d-inline">
                                    <h3 class="lite-text">مدیریت تارگت پرسنل</h3>
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

        <!-- Success or Error Messages -->
        @if(session()->has('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session()->has('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @foreach($users as $user)
        <div class="row m-2 mb-1 bg-light p-5 rounded justify-content-md-center">

            <!-- Form for updating target (First Row) -->
            <form method="POST" action="{{ route('personnel.updateTarget', ['id' => $user->id]) }}">
                @csrf
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>نام</th>
                            <th>ماه</th>
                            <th>سال</th>
                            <th>متراژ تارگت</th>
                            <th>فعالیت</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>
                                <select name="month" required>
                                    <option value="">انتخاب ماه</option>
                                    <option value="1">فروردین</option>
                                    <option value="2">اردیبهشت</option>
                                    <option value="3">خرداد</option>
                                    <option value="4">تیر</option>
                                    <option value="5">مرداد</option>
                                    <option value="6">شهریور</option>
                                    <option value="7">مهر</option>
                                    <option value="8">آبان</option>
                                    <option value="9">آذر</option>
                                    <option value="10">دی</option>
                                    <option value="11">بهمن</option>
                                    <option value="12">اسفند</option>
                                </select>
                            </td>
                            <td>
                                <input type="number" name="year" value="{{ old('year') }}" required>
                            </td>
                            <td>
                                <input type="number" name="target" value="{{ old('target', $user->target) }}" step="0.01" required>
                            </td>
                            <td>
                                <button type="submit" class="btn btn-primary">به روز رسانی</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>

            <!-- Display User's Targets (Second Row) -->
            <div class="col-md-12">
                <h4>تارگت های این پرسنل:</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ماه</th>
                            <th>سال</th>
                            <th>متراژ تارگت</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($user->targets as $target)
                            <tr>
                                <!-- Convert the month number to Persian month name -->
                                <td>
                                    @php
                                        $persianMonths = [
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
                                        $monthName = $persianMonths[$target->month] ?? 'نامشخص';
                                    @endphp
                                    {{ $monthName }}
                                </td>
                                <td>{{ $target->year }}</td>
                                <td>{{ $target->target }}</td>
                                <td>
                                    <!-- Delete Button -->
                                    <form action="{{ route('personnel.deleteTarget', ['id' => $user->id, 'targetId' => $target->id]) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">حذف</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach
    </div>

</x-app-layout>
