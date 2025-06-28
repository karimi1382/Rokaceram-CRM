<x-app-layout>
    <div class="container-fluid">
        <div class="row m-1 pb-4 mb-3 ">
            <div class="col-12 p-2">
                <div class="page-header breadcrumb-header ">
                    <div class="row align-items-end ">
                        <div class="col-lg-8">
                            <div class="page-header-title text-left-rtl">
                                <h3 class="lite-text ">شکایت‌های تکمیل‌شده</h3>
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

        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped text-center align-middle shadow-sm">
                <thead class="thead-dark">
                    <tr>
                        <th>نماینده</th>
                        <th>نام مشتری</th>
                        <th>محصول</th>
                        <th>نوع شکایت</th>
                        <th>وضعیت</th>
                      
                      
                        <th>تاریخ</th>
                        <th>جزئیات</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($complaints as $complaint)
                        <tr>
                            <td>{{ $complaint->distributor->name ?? '-' }}</td>
                            <td>{{ $complaint->customer_name }}</td>
                            <td>{{ $complaint->product_name }}</td>
                            <td>{{ $complaint->complaint_type }}</td>
                            <td>
                                <span class="badge 
                                    @if($complaint->status == 'تایید شده') badge-success
                                    @elseif($complaint->status == 'رد شده') badge-danger
                                    @else badge-warning
                                    @endif">
                                    {{ $complaint->status }}
                                </span>
                            </td>
                           
                       
                            <td>{{ $complaint->shamsi_created }}</td>
                            <td>
                                <div class="d-flex justify-content-center gap-2 flex-wrap">
                                    @if(auth()->user()->role == 'manager') 
                                        <a href="{{ route('manager.complaints.show', $complaint->id) }}" class="btn btn-sm btn-outline-info">جزئیات</a>
                                    @else
                                        <a href="{{ route('complaints.show', $complaint->id) }}" class="btn btn-sm btn-outline-info">جزئیات</a>
                                    @endif                           
                                    
                                    @if(auth()->user()->role === 'personnel')
                                        <a href="{{ route('complaints.edit', $complaint->id) }}" class="btn btn-sm btn-warning">ویرایش</a>
                                    @endif
                                </div>
                            </td>
                            
                            
                            
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
