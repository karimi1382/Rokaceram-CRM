<x-app-layout>
    <div class="container-fluid">

        <!-- Breadcrumb -->
        <div class="row  m-1 pb-4 mb-3 ">
            <div class="col-12 p-2">
                <div class="page-header breadcrumb-header">
                    <div class="row align-items-end">
                        <div class="col-lg-8">
                            <div class="page-header-title text-left-rtl">
                                <h3 class="lite-text">مشاهده درخواست محصول</h3>
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

        <!-- Table and messages -->
        <div class="row m-2 mb-1 bg-light p-5 rounded row justify-content-md-center">
            <div class="table-responsive">

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
                    <tbody id="tableBody">
                        @php $n = 0; @endphp
                        @foreach ($requests as $request)
                            @if($request->product_details)
                                <tr>
                                    <td>{{ ++$n }}</td>
                                    <td class="agent-name">{{ $request->user->name }}</td>
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
                                    <td class="request-status">
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
                                            <form action="{{ route('dis_requests.delete', $request->id) }}" method="POST" class="delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-warning">حذف</button>
                                            </form>
                                            @endif
                                            <form action="{{ route('dis_requests.show', $request->id) }}" method="GET">
                                                <button type="submit" class="btn btn-info">مشاهده جزئیات</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- SweetAlert + Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
        
                // ✅ حذف با SweetAlert
                document.querySelectorAll('.delete-form').forEach(form => {
                    form.addEventListener('submit', function (e) {
                        e.preventDefault();
        
                        Swal.fire({
                            title: 'آیا مطمئن هستید؟',
                            text: 'این عملیات قابل بازگشت نیست!',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: '✔ بله، حذف شود',
                            cancelButtonText: '✖ لغو',
                            customClass: {
                                popup: 'text-end rtl',   // راست‌چین کردن
                                title: 'w-100 text-center', // عنوان وسط‌چین
                                htmlContainer: 'text-center',
                                confirmButton: 'btn btn-danger mx-2 shadow-sm rounded-pill',
                                cancelButton: 'btn btn-secondary shadow-sm rounded-pill'
                            },
                            buttonsStyling: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    });
                });
        
                // ✅ پیام موفقیت بعد از حذف
                @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'موفقیت',
                    text: '{{ session('success') }}',
                    confirmButtonText: 'باشه',
                    customClass: {
                        popup: 'text-end rtl',
                        title: 'w-100 text-center',
                        htmlContainer: 'text-center',
                        confirmButton: 'btn btn-success shadow-sm rounded-pill px-4'
                    },
                    buttonsStyling: false
                });
                @endif
        
                // ✅ فیلتر جدول (در صورت نیاز)
                document.getElementById('searchName')?.addEventListener('keyup', filterTable);
                document.getElementById('searchStatus')?.addEventListener('keyup', filterTable);
        
                function filterTable() {
                    let nameInput = document.getElementById('searchName').value.toLowerCase();
                    let statusInput = document.getElementById('searchStatus').value.toLowerCase();
                    let rows = document.querySelectorAll('#tableBody tr');
        
                    rows.forEach(row => {
                        let name = row.querySelector('.agent-name')?.textContent.toLowerCase() || '';
                        let status = row.querySelector('.request-status')?.textContent.toLowerCase() || '';
        
                        row.style.display = name.includes(nameInput) && status.includes(statusInput) ? '' : 'none';
                    });
                }
            });
        </script>
        
    </div>
</x-app-layout>
