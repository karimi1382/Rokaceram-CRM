<x-app-layout>
    <div class="container-fluid ">

        <div class="row m-1 pb-4 mb-3 ">
            <div class="col-12 p-2">
                <div class="page-header breadcrumb-header">
                    <div class="row align-items-end">
                        <div class="col-lg-8">
                            <h3 class="lite-text">مدیریت تارگت نماینده</h3>
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

        <!-- Display Success or Error Messages -->
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

            <!-- Form to Insert/Update Distributor Target -->
            <form method="POST" action="{{ route('distributor.updateTarget', ['id' => $user->id]) }}">
                @csrf
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>نام نماینده</th>
                            <th>شهر</th>
                            <th>ماه</th>
                            <th>سال</th>
                            <th>متراژ تارگت</th>
                            <th>ثبت</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->UserData->city->name }}</td>
                            <td>
                                <select name="month" class="form-control" required>
                                    <option value="">انتخاب ماه</option>
                                    @foreach([1 => 'فروردین', 2 => 'اردیبهشت', 3 => 'خرداد', 4 => 'تیر', 5 => 'مرداد', 6 => 'شهریور', 7 => 'مهر', 8 => 'آبان', 9 => 'آذر', 10 => 'دی', 11 => 'بهمن', 12 => 'اسفند'] as $num => $name)
                                        <option value="{{ $num }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number" name="year" class="form-control" required>
                            </td>
                            <td>
                                <input type="number" name="target" class="form-control" step="0.01" required>
                            </td>
                            <td>
                                <button type="submit" class="btn btn-primary">ثبت</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>

            <!-- Display Targets for Distributor -->
            <div class="col-md-12">
                <h4>تارگت‌های این نماینده:</h4>
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
                                <td>{{ ['فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'][$target->month - 1] }}</td>
                                <td>{{ $target->year }}</td>
                                <td>{{ $target->target }}</td>
                                <td>
                                    <form action="{{ route('distributor.deleteTarget', ['id' => $user->id, 'targetId' => $target->id]) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">حذف</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        @if($user->targets->isEmpty())
                            <tr>
                                <td colspan="4" class="text-center">هیچ تارگتی برای این نماینده ثبت نشده است.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

        </div>
        @endforeach
    </div>
</x-app-layout>
