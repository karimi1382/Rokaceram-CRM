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
                            <h3 class="lite-text ">جزئیات درخواست تکمیل شده</h3>
                            <!-- <span class="lite-text ">ایجاد کاربر جدید</span> -->
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
<!-- alert -->
<!-- <div class="row m-1 pb-3 ">

    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p-2">
        <div class="alert alert-danger alert-shade alert-dismissible fade show" role="alert">
            <strong>Danger!</strong> Your Disk is Low.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
    </div>

</div> -->
<!-- widget -->
<div class="col-md-12">

  <!-- Display Success or Error Message -->
  @if(session()->has('success'))
        <div class="alert alert-success">
            {{ session('success') }}
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

<div class="container py-5">
    <div class="card shadow mb-4">
        <div class="card-header bg-dark text-white">
            <h4 class="mb-0">جزئیات سفارش شما</h4>
        </div>
        <div class="card-body">
            <p><strong>نام محصول:</strong> {{ $request->product->name }}</p>
            <p><strong>متراژ درخواست شده :</strong> {{ $request->request_size }}</p>
           

            <p><strong>درخواست کننده:</strong> {{ $request->request_owner }}</p>
            <p><strong>شماره تماس هماهنگی :</strong> {{ $request->tel_number }}</p>
            <p><strong>آدرس :</strong> {{ $request->address }}</p>
            <p><strong>وضعیت درخواست:</strong> 
                <span class="badge 
                    @if($request->status === 'Pending') bg-warning 
                    @elseif($request->status === 'Approved') bg-success 
                    @elseif($request->status === 'Rejected') bg-danger 
                    @elseif($request->status === 'Completed') bg-info 
                    @endif">
                    {{ $request->status }}
                </span>
            </p>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">توضیحات اضافه شده</h5>
        </div>
        <div class="card-body">
            @if ($request->requestDetails->isEmpty())
                <p class="text-muted">هیچ توضیحاتی اضافه نشده است</p>
            @else
                @foreach ($request->requestDetails as $detail)
                    <div class="d-flex mb-3">
                        <div class="m-2">
                        @if($detail->user->userData && $detail->user->userData->profile_picture)
                        <img src="{{ asset('storage/' . $detail->user->userData->profile_picture) }}" 
                            alt="{{ $detail->user->name }}" 
                            class="rounded-circle" 
                            width="50" 
                            height="50">
                         @else
                            
                        @endif
                            <span class="badge bg-light">{{ $detail->user->name }}</span>
                        </div>
                        <div class="m-2">
                            <p class="mt-4">{{ $detail->description }}</p>
                            <small class="text-muted">{{ $detail->created_at->format('F j, Y h:i A') }}</small>
                        </div>
                    </div>
                    <hr>
                @endforeach
            @endif
        </div>
    </div>

</div>









</div>
</x-app-layout>
