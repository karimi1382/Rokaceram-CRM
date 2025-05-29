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
                            <h3 class="lite-text ">در انتظار تایید   </h3>
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
<table border="1" class="table table-bordered">
        <thead>
            <tr>
                <th>نام نماینده</th>
                <th>محصول درخواستی</th>
                <th>سرپرست مربوطه</th>
                <th>متراژ درخواستی</th>
                <th>وضعیت</th>
                <th style="width:70px"></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($disRequests as $request)
                <tr>
                    <td>{{ $request->user->name }}</td> <!-- Display the user's name -->
                    <td>{{ $request->product->name }} - {{$request->product->degree }} - {{$request->product->size }} - {{$request->product->model }} - {{$request->product->color }} - {{$request->product->color_code }}  </td>
                    <td>

                 


                    @if($request->user->userdata && $request->user->userdata->personel_id)
                        {{ $request->user->Userdata->parent->name }} <!-- Display the parent user's name -->
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ $request->request_size }}</td> <!-- Display the request size -->
                    <td>
                        <form action="{{ route('dis_request.update', $request->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <select name="status" class="form-control">
                                <option value="In Progress" {{ $request->status == 'In Progress' ? 'selected' : '' }}> تایید سرپرست</option>
                                <option value="Approved" {{ $request->status == 'Approved' ? 'selected' : '' }}>تایید مدیر فروش</option>
                                <option value="rejected" {{ $request->status == 'rejected' ? 'selected' : '' }}>رد شده</option>
                            </select>
                         
                    </td>
                    <td >
                    <button type="submit" class="btn-sm btn-warning mt-1">ویرایش</button>
                    
                    </form>
                    </td>
                  
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
</div>
</x-app-layout>
