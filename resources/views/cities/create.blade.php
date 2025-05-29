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
                            <h3 class="lite-text ">شهر و استان</h3>
                            <span class="lite-text "> اضافه کردن شهر و استان</span>
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

       

          

                  
                    <div class="card-body col-md-6">
                        <!-- Back Button -->


                        <!-- Error Messages -->
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- City Form -->
                        <form action="{{ route('cities.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">نام استان<span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control" 
                                       value="{{ old('name') }}" placeholder="نام استان را وارد نمایید" required>
                            </div>

                            <div class="mb-3">
                                <label for="state" class="form-label">شهرستان</label>
                                <input type="text" name="state" id="state" class="form-control" 
                                       value="{{ old('state') }}" placeholder="شهرستان را وارد نمایید">
                            </div>

                            <div class="mb-3">
                                <label for="country" class="form-label">کشور</label>
                                <input type="text" name="country" id="country" class="form-control" 
                                       value="{{ old('country') }}" placeholder="کشور را وارد نمایید">
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i>اضافه کردن</button>
                            </div>
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
