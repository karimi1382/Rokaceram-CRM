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
                            <h3 class="lite-text ">درخواست های تکمیل شده</h3>
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

<div class="row m-2 mb-1 bg-light p-5 rounded row justify-content-md-center">
<!-- <a href="{{ route('products.create') }}">Add Product</a> -->



<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ردیف</th>
                <th>نام نماینده</th>
                <th>نام شهر</th>
                <th>شماره حواله</th>
                <th>وضعیت درخواست</th>
                <th>تاریخ ایجاد</th>
                <th style="width:70px"></th>
            </tr>
        </thead>
        <tbody id="tableBody">
            <?php $n=0 ?>
            <!-- نمایش درخواست‌های inprogress -->
            @foreach ($inProgressRequests as $request)
                <?php $n++ ?>
                <tr>
                    <td>{{ $n }}</td>
                    <td class="agent-name">{{ $request->user->name }}</td>
                    <td>{{ $request->user->userdata->city->name }}</td>
                    <td>
                        @foreach ($request->disRequestHavales as $havale)
                            <a href="{{ route('dis_requests.havale_data', $havale->havale_number) }}" class="btn btn-info">{{ $havale->havale_number }}</a>
                        @endforeach
                    </td>
                    <td>
                        حواله برای این درخواست ثبت شده
                    </td>
                    <td>{{ $request->shamsi_created_at  }}</td>
                    <td>
                        <a href="{{ route('dis_requests.show', $request->id) }}" class="btn btn-info">مشاهده جزئیات</a>
                    </td>
                </tr>
            @endforeach
            
            <!-- نمایش درخواست‌های rejected -->
            @foreach ($rejectedRequests as $request)
                <?php $n++ ?>
                <tr>
                    <td>{{ $n }}</td>
                    <td class="agent-name">{{ $request->user->name }}</td>
                    <td>{{ $request->user->userdata->city->name }}</td>
                    <td>
                        @foreach ($request->disRequestHavales as $havale)
                            <a href="{{ route('dis_requests.havale_data', $havale->havale_number) }}" class="btn btn-info">{{ $havale->havale_number }}</a>
                        @endforeach
                    </td>
                    <td>
                        رد شده
                    </td>
                    <td>{{ $request->jalali_created_at }}</td>
                    <td>
                        <a href="{{ route('dis_requests.show', $request->id) }}" class="btn btn-info">مشاهده جزئیات</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    

</div>





</div>
{{-- 
<div class="row m-2 mb-1 bg-light p-5 rounded row justify-content-md-center">

<div class="table-responsive">
    <table class="table table-striped table-hover ">
        <thead>
            <tr class="table-danger">
                <th>ردیف</th>
                <th>نام محصول</th>
                <th>متراژ</th>
                <th>وضعیت درخواست</th>
                <th style="width:70px"></th>
            </tr>
        </thead>
        <tbody>
            <?php $n=0 ?>
            @foreach ($Reject_requests as $reject_request)
            <?php $n++ ?>
                <tr>
                    <td>{{ $n }}</td>
                    <td>{{ $reject_request->product->name }} - {{$reject_request->product->degree }} - {{$reject_request->product->size }} - {{$reject_request->product->model }} - {{$reject_request->product->color }} - {{$reject_request->product->color_code }}  </td>
                    <td>{{ $reject_request->request_size }}</td>
                    <td>رد شده</td>
                    <td>
                    <a href="{{ route('dis_requests.completed.show', $reject_request->id) }}" class="btn btn-sm btn-info">مشاهده جزئیات</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>

</div> --}}



</div>




</x-app-layout>
