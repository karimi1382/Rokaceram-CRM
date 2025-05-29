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
                            <h3 class="lite-text ">مدیریت کاربران سایت</h3>
                            <span class="lite-text ">اضافه / ویرایش / حذف   </span>
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
  <!-- Display Success or Error Message -->
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

<div class="row m-2 mb-1 bg-light p-5 rounded">
<!-- <a href="{{ route('products.create') }}">Add Product</a> -->

<a href="{{ route('users.create') }}" class="btn btn-primary">اضافه کردن کاربر جدید</a>

    <table class="table mt-3">
        <thead>
            <tr>
                <th style="width:150px">تصویر پروفایل </th>
                <th>نام کاربر</th>
                <th>ایمیل</th>
                <th>نقش کاربر</th>
                <th style="width:70px"></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>
                    @if($user->userData && $user->userData->profile_picture)
                            <img src="{{ asset('storage/' . $user->userData->profile_picture) }}" alt="Profile Picture" class="rounded-circle screen-user-profile" style="width: 50px; height: 50px; margin-right: 10px;">
                        @else
                        <img src="{{ asset('/img/user-profile.jpg')  }}" alt="Default Profile Picture" class="rounded-circle screen-user-profile" width="150">
                        @endif  
</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role }}</td>
                    <td>
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-sm">ویرایش</a>
                        <!-- Add delete button if needed -->
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>



    
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
