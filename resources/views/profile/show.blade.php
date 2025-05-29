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
                            <h3 class="lite-text ">پروفایل </h3>
                            <span class="lite-text ">مشاهده / ویرایش اطلاعات پروفایل</span>
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
   

    <!-- Profile Information -->
    <div class="row">
        <div class="col-md-3">
            <!-- Show profile picture -->
            @if ($userData->profile_picture)
                <img src="{{ asset('storage/' . $userData->profile_picture) }}" alt="Profile Picture"  style="width:100%;">
            @else
                <p>No profile picture uploaded.</p>
            @endif
        </div>

        <div class="col-md-9">
            <!-- Profile Update Form -->
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="phone">تلفن</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $userData->phone) }}">
                </div>

                <div class="form-group">
                    <label for="city">شهر</label>
                    <input type="text" name="city" class="form-control" value="{{ old('city',  $userData->city->name) }}" disabled>
                </div>

                <div class="form-group">
                    <label for="profile_picture">تصویر پروفایل</label>
                    <input type="file" name="profile_picture" class="form-control">
                </div>

                

                <button type="submit" class="btn btn-primary mt-3">به روز رسانی</button>
            </form>
        </div>
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
