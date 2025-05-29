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
                            <h3 class="lite-text ">محصول</h3>
                            <span class="lite-text ">اضافه کردن محصول / به روز رسانی محصول</span>
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
<table class="table table-bordered">
    <thead>
        <tr>
            <th>کد محصول</th>
            <th>برند</th>
            <th>درجه</th>
            <th>سایز</th>
            <th>نام طرح</th>
            <th>رنگ</th>
            <th>کد رنگ</th>
            <th>موجودی</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($temps as $temp)
        <tr>
            <td>{{ $temp->id_number }}</td>
            <td>{{ $temp->name }}</td>
            <td>{{ $temp->degree }}</td>
            <td>{{ $temp->size }}</td>
            <td>{{ $temp->model }}</td>
            <td>{{ $temp->color }}</td>
            <td>{{ $temp->color_code }}</td>
            <td>{{ $temp->inventory }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<form action="{{ route('products.finalize') }}" method="POST">
    @csrf
    <button type="submit" class="btn btn-info">ثبت اطلاعات</button>
</form>


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
