<x-app-layout>
    <div class="container-fluid">
        <div class="row m-1 pb-4 mb-3">
            <div class="col-12 p-2">
                <div class="page-header breadcrumb-header">
                    <div class="row align-items-end">
                        <div class="col-lg-8">
                            <div class="page-header-title text-left-rtl">
                                <h3 class="lite-text">مدیریت شکایت‌ها</h3>
                                <p class="text-muted small mt-1">در این بخش می‌توانید شکایت‌های ثبت‌شده را مشاهده و مدیریت کنید.</p>
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

        @if(session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="بستن">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered align-middle text-center">
                        <thead class="thead-dark">
                            <tr>
                                <th>نماینده</th>
                                <th>نام مشتری</th>
                                <th>کد محصول</th>
                                <th>نام محصول</th>
                                <th>وضعیت</th>
                                <th>تاریخ ثبت</th>
                                <th>عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($complaints as $complaint)
                                <tr>
                                    <td>{{ $complaint->distributor->name ?? '-' }}</td>
                                    <td>{{ $complaint->customer_name }}</td>
                                    <td>{{ $complaint->product_code }}</td>
                                    <td>
                                        {{ implode(' - ', array_filter([$complaint->product_name])) }}
                                    </td>
                                    <td>
                                        <span class="badge badge-pill badge-{{ 
                                            $complaint->status == 'در انتظار' ? 'warning' : 
                                            ($complaint->status == 'تایید شده' ? 'success' : 
                                            ($complaint->status == 'رد شده' ? 'danger' : 'secondary')) }}">
                                            {{ $complaint->status }}
                                        </span>
                                    </td>
                                    <td>{{ $complaint->shamsi_created }}</td>
                                    <td>
                                        <div class="text-center">
                                            @if(auth()->user()->role == 'manager') 
                                                 <a href="{{ route('manager.complaints.show', $complaint->id) }}" class="btn btn-sm btn-outline-info d-inline-block me-1">جزئیات</a>
                                            @else
                                                <a href="{{ route('complaints.show', $complaint->id) }}" class="btn btn-sm btn-outline-info d-inline-block me-1">جزئیات</a>
                                            @endif

                                            @if (auth()->user()->role == 'personnel')
                                                <a href="{{ route('complaints.edit', $complaint->id) }}" class="btn btn-sm btn-outline-primary d-inline-block me-1">ویرایش</a>
                                            @endif
                                    
                                            @if (auth()->user()->role == 'distributor' && $complaint->status == 'در انتظار')
                                                <form action="{{ route('complaints.destroy', $complaint->id) }}" method="POST" class="d-inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('آیا مطمئنید؟')">حذف</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                    
                                    
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">هیچ شکایتی ثبت نشده است.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
