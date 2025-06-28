<x-app-layout>
    <div class="container-fluid">

        <!-- Header -->
        <div class="row m-1 pb-4 mb-3">
            <div class="col-12 p-2">
                <div class="page-header breadcrumb-header">
                    <div class="row align-items-end">
                        <div class="col-lg-8">
                            <div class="page-header-title text-left-rtl">
                                <h3 class="lite-text">🔍 جزئیات شکایت #{{ $complaint->id }}</h3>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i></a></li>
                                <li class="breadcrumb-item active">جزئیات شکایت</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Content -->
        <div class="card shadow-sm p-4 border rounded">

            <!-- Info Section -->
            <h5 class="text-primary mb-3"><i class="fas fa-info-circle"></i> اطلاعات کلی</h5>
            <div class="row mb-4">
                <div class="col-md-6 mb-2">
                    <strong>نماینده:</strong> {{ $complaint->distributor->name ?? '-' }}
                </div>
                <div class="col-md-6 mb-2">
                    <strong>نام مشتری:</strong> {{ $complaint->customer_name }}
                </div>
                <div class="col-md-6 mb-2">
                    <strong>شماره تماس:</strong> {{ $complaint->tel_number }}
                </div>
                <div class="col-md-6 mb-2">
                    <strong>آدرس:</strong> {{ $complaint->address }}
                </div>
                <div class="col-md-6 mb-2">
                    <strong>کد محصول:</strong> {{ $complaint->product_code ?? '-' }}
                </div>
                <div class="col-md-12 mb-2">
                    <strong>محصول:</strong>
                    <span class="badge badge-info p-2">
                        {{ implode(' - ', array_filter([
                            $complaint->product_name,
                            $complaint->degree,
                            $complaint->size,
                            $complaint->model,
                            $complaint->color,
                            $complaint->color_code
                        ])) }}
                    </span>
                </div>
                <div class="col-md-6 mb-2">
                    <strong>شماره حواله:</strong> {{ $complaint->tracking_number ?? '-' }}
                </div>
                <div class="col-md-6 mb-2">
                    <strong>نوع شکایت:</strong> {{ $complaint->complaint_type }}
                </div>
                <div class="col-md-12 mb-2">
                    <strong>شرح شکایت:</strong> {{ $complaint->complaint_text }}
                </div>
                <div class="col-md-6 mb-2">
                    <strong>وضعیت:</strong>
                    <span class="badge badge-pill 
                        @if($complaint->status == 'تایید شده') badge-success 
                        @elseif($complaint->status == 'رد شده') badge-danger 
                        @elseif($complaint->status == 'در حال بررسی') badge-warning 
                        @else badge-secondary 
                        @endif">
                        {{ $complaint->status }}
                    </span>
                </div>
            </div>

            <!-- Issues -->
            <h5 class="text-primary mb-3"><i class="fas fa-list"></i> نوع مشکلات</h5>
            @if ($complaint->issues && $complaint->issues->count())
                <div class="form-group mb-4">
                    @foreach ($complaint->issues as $issue)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" checked disabled>
                            <label class="form-check-label">{{ $issue->name }}</label>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted">هیچ موردی ثبت نشده است.</p>
            @endif
            

            <!-- Attachments -->
            <h5 class="text-primary mb-3"><i class="fas fa-images"></i> تصاویر پیوست</h5>
            @if ($complaint->attachments)
                <div class="row">
                    @foreach (json_decode($complaint->attachments) as $file)
                        <div class="col-md-3 mb-3">
                            <div class="card">
                                <a href="{{ asset('storage/' . $file) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $file) }}" class="card-img-top img-fluid rounded" alt="Attachment">
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted">تصویری ثبت نشده است.</p>
            @endif

            <!-- Comments -->
            <h5 class="text-primary mb-3"><i class="fas fa-comments"></i> کامنت‌ها</h5>
            @if ($complaint->comments && $complaint->comments->count())
                <ul class="list-group">
                    @foreach ($complaint->comments as $comment)
                        <li class="list-group-item">
                            <br>
                            <small class="text-muted">تاریخ: {{ $comment->shamsi_created_at }}</small> <br><br>
                            <strong>{{ $comment->user->name ?? 'کاربر ناشناس' }}</strong> :
                           
                         {{ $comment->comment_text }} 
                            <br><br>
                           
                        </li>
                        <hr>
                        
                    @endforeach
                </ul>
            @else
                <p class="text-muted">هیچ کامنتی ثبت نشده است.</p>
            @endif

          
            <div class="col-2 mt-2 text-center">
                <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-arrow-left"></i> بازگشت
                </a>
            </div>
            

        </div>
        
    </div>
</x-app-layout>
