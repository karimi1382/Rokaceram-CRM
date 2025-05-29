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
                            <h3 class="lite-text ">رمز عبور </h3>
                            <span class="lite-text ">ویرایش رمز عبور</span>
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

<div class="row m-2 mb-1 bg-light p-5 rounded row justify-content-md-center">
<!-- <a href="{{ route('products.create') }}">Add Product</a> -->

<div class="col-md-12">
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
</div>


 

    <!-- Display success message -->
   

  

    <!-- Last Login Date -->
     <!-- <div class="col-md-12">
    <h3 class="mt-5">Last Login</h3>
    <p>{{ $userData->last_login_at ? $userData->last_login_at->diffForHumans() : 'هیچ اطلاعاتی از آخرین لاگین وجود ندارد' }}</p>
</div> -->

    <div class="card-body col-3">
        <form action="{{ route('profile.updatePassword') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label for="password">رمز عبور جدید</label>
                <input type="password" name="password" id="password" class="form-control">
                <small class="form-text text-muted">رمز عبور باید حداقل 8 کاراکتر باشد</small>
            </div>
            
            <div class="form-group">
                <label for="password_confirmation">تکرار رمز عبور</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
            </div>
            
            <button type="submit" class="btn btn-primary">ویرایش رمز عبور</button>
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
