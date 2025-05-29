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
                            <h3 class="lite-text ">صفحه لاگین </h3>
                            <span class="lite-text ">ویرایش اطلاعات </span>
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

<div class="row m-2 mb-1 bg-light p-5 rounded">
<!-- <a href="{{ route('products.create') }}">Add Product</a> -->



       
<div class="col-md-12">
@if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
</dov>
       

        <!-- Show current data at the top -->
        <div class="card mb-4 p-5 m-5">
            <div class="card-header">
                <strong>اطلاعات فعلی</strong>
            </div>
            <div class="card-body">
                <p><strong>متن:</strong> {{ $loginPage->text ?? 'متنی وجود ندارد' }}</p>

                @if ($loginPage && $loginPage->image)
                    <p><strong>تصویر:</strong></p>
                    <img src="{{ asset('storage/' . $loginPage->image) }}" alt="Login Image" class="img-thumbnail" width="200">

                @else
                    <p><strong>تصویر:</strong> عکسی وجود ندارد</p>
                @endif
            </div>
        </div>
        <div class="card-body">
        <!-- Update Form -->
        <form action="{{ route('admin.login_page.update') }}" method="POST" enctype="multipart/form-data" class="m-5">
            @csrf
            <div class="mb-3">
                <label for="text" class="form-label">متن</label>
                <input type="text" name="text" id="text" class="form-control" value="{{ $loginPage->text ?? '' }}">
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">تصویر</label>
                <input type="file" name="image" id="image" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">به روز رسانی</button>
        </form>
</div>
 

    
</div>








</div>



    <!-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div> -->
</x-app-layout>
