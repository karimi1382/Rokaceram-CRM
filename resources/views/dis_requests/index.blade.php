<x-app-layout>
    <div class="container-fluid ">

        <!-- content -->
        <!-- breadcrumb -->

        <div class="row  m-1 pb-4 mb-3 ">
            <div class="col-xs-12  col-sm-12  col-md-12  col-lg-12 p-2">
                <div class="page-header breadcrumb-header ">
                    <div class="row align-items-end ">
                        <div class="col-lg-8">
                            <div class="page-header-title text-left-rtl">
                                <div class="d-inline">
                                    <h3 class="lite-text ">مشاهده درخواست محصول</h3>
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
            <!-- Display Success or Error Message -->
            @if (session()->has('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
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
                <!-- <div class="row mb-3">
                    <div class="col-md-3">
                        <input type="text" id="searchName" class="form-control" placeholder="جستجو بر اساس نام نماینده">
                    </div>
                    <div class="col-md-3">
                        <input type="text" id="searchStatus" class="form-control" placeholder="جستجو بر اساس وضعیت درخواست">
                    </div>
                </div> -->

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ردیف</th>
                            <th>نام نماینده</th>
                            <th>نام شهر</th>
                            <th>نام محصول</th>
                            <th>متراژ</th>
                            <th>شماره حواله</th>
                            <th>وضعیت درخواست</th>
                            <th>تاریخ ایجاد</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $n = 0; @endphp
                        @foreach ($requests as $request)
                        
                            @if($request->product_details) <!-- اضافه کردن شرط بررسی موجودیت محصول -->
                                <tr>
                                    <td>{{ ++$n }}</td>
                                    <td>{{ $request->user->name }}</td>
                                    <td>{{ $request->user->userdata->city->name }}</td>
                                    <td>
                                        {{ $request->product_details->name }} - {{$request->product_details->degree }} - 
                                        {{$request->product_details->size }} - {{$request->product_details->model }} - 
                                        {{$request->product_details->color }} - {{$request->product_details->color_code }}
                                    </td>
                                    <td>{{ $request->request_size }}</td>

                                    <td>
                                        @if(!empty($request->havale_numbers))
                                            @foreach($request->havale_numbers as $havale_number)
                                                <a href="{{ route('dis_requests.havale_data', $havale_number) }}" class="badge bg-primary">
                                                    {{ $havale_number }}
                                                </a>
                                            @endforeach
                                        @else
                                            <span class="text-muted"> - </span>
                                        @endif
                                    </td>

                                    <td>
                                        @if($request->status == 'Pending')
                                            در انتظار بررسی
                                        @elseif($request->status == 'In Progress')
                                            <span class="badge bg-warning">حواله برای این درخواست ثبت شد</span>
                                        @endif
                                    </td>

                                    <td>{{ $request->jalali_created_at }}</td>

                                    <td>
                                        <div style="display: flex; gap: 5px;">
                                           @if(auth()->user()->role == 'personnel')
                                            <form action="{{ route('dis_requests.delete', $request->id) }}" method="POST" onsubmit="return confirmDelete(this);">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-warning">حذف</button>
                                            </form>
                                            @endif
                                            <form action="{{ route('dis_requests.show', $request->id) }}" method="GET">
                                                <button type="submit" class="btn btn-info">مشاهده جزئیات</button>
                                            </form>
                                        </div>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            <script>

                function confirmDelete(form) {
                        return confirm('آیا از حذف این مورد مطمئن هستید؟');
                    }



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
    </div>
</x-app-layout>
