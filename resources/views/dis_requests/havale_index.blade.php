<x-app-layout>

    <div class="container-fluid">

        <!-- breadcrumb -->
        <div class="row m-1 pb-4 mb-3">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p-2">
                <div class="page-header breadcrumb-header">
                    <div class="row align-items-end">
                        <div class="col-lg-8">
                            <div class="page-header-title text-left-rtl">
                                <div class="d-inline">
                                    <h3 class="lite-text">مشاهده حواله های باز</h3>
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

        <!-- Success or Error Message -->
        <div class="col-md-12">
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

        <div class="row m-2 mb-1 bg-light p-5 rounded row justify-content-md-center">

            <div class="table-responsive">
                <!-- Search Inputs -->
                {{-- <div class="row mb-3">
                    <div class="col-md-3">
                        <input type="text" id="searchName" class="form-control" placeholder="جستجو بر اساس نام نماینده">
                    </div>
                    <div class="col-md-3">
                        <input type="text" id="searchStatus" class="form-control" placeholder="جستجو بر اساس وضعیت درخواست">
                    </div>
                </div> --}}

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ردیف</th>
                            <th>نام نماینده</th>
                            <th>نام شهر</th>
                            <th>شماره حواله</th>
                            <th>وضعیت حواله</th>
                            <th>مهلت تایید مالی</th>
                            <th>تاریخ ثبت حواله</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $n = 0; @endphp
                        @foreach ($havaleRecords as $havale)
                            <tr>
                                <td>{{ ++$n }}</td>
                                <td>{{ $havale->user->name }}</td>
                                <td>{{ $havale->user->userdata->city->name }}</td>
                                <td>
                                        {{ $havale->havale_number }}
                                </td>
                                <td>
                                    @if($havale->status == 'In Progress')
                                        <span class="badge bg-warning">در انتظار تایید مالی</span>
                                    @elseif($havale->status == 'Approved')
                                        <span class="badge bg-info">در انتظار بارگیری</span>
                                    @elseif($havale->status == 'Completed')
                                        <span class="badge bg-success">تکمیل شده</span>
                                    @elseif($havale->status == 'Rejected')
                                        <span class="badge bg-danger">رد شده - مهلت تمام شد</span>
                                    @endif
                                </td>
                                <td>
                                    @if($havale->status == 'In Progress')
                                        @if($havale->remaining_days > 0)
                                            @if($havale->remaining_days < 5)
                                                <span class="blinking-red">{{ $havale->remaining_days }} روز باقی مانده</span>
                                            @else
                                                {{ $havale->remaining_days }} روز باقی مانده
                                            @endif
                                        @else
                                            <span class="text-danger">مهلت به پایان رسیده!</span>
                                        @endif
                                    @else
                                        تایید شده
                                    @endif
                                </td>
                                
                                <td>{{ $havale->jalali_created_at }}</td>
                                <td>
                                    <a href="{{ route('dis_requests.havale_data', $havale->havale_number) }}" class="btn btn-info">
                                        مشاهده جزئیات
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                
                
                
            </div>
        </div>

        <script>
            document.getElementById('searchName').addEventListener('keyup', filterTable);
            document.getElementById('searchStatus').addEventListener('keyup', filterTable);

            function filterTable() {
                let nameInput = document.getElementById('searchName').value.toLowerCase();
                let statusInput = document.getElementById('searchStatus').value.toLowerCase();
                let rows = document.querySelectorAll('#tableBody tr');

                rows.forEach(row => {
                    let name = row.querySelector('.agent-name').textContent.toLowerCase();
                    let status = row.querySelector('.request-status').textContent.toLowerCase();

                    if (name.includes(nameInput) && status.includes(statusInput)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }
        </script>

    </div>
</x-app-layout>
