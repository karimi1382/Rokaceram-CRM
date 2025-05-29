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
                            <span class="lite-text ">مدیریت محصولات</span>
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
<a href="{{ route('cities.create') }}" class="btn btn-info">اضافه کردن شهر جدید</a>
    <table  class="table table-bordered">
        <thead>
            <tr>
                <th>ردیف</th>
                <th>استان</th>
                <th>شهرستان</th>
                <th>کشور</th>
                <!--<th style="width:70px"></th>-->
            </tr>
        </thead>
        <tbody>
            <?php $n=0 ?>
            @foreach ($cities as $city)
            <?php $n++ ?>
                <tr>
                    <td>{{ $n }}</td>
                    <td>{{ $city->name }}</td>
                    <td>{{ $city->state }}</td>
                    <td>{{ $city->country }}</td>
                    <!--<td>-->
                        
                    <!--    <form action="{{ route('cities.destroy', $city) }}" method="POST" class="m-0">-->
                    <!--        @csrf-->
                    <!--        @method('DELETE')-->
                    <!--        <button type="submit" class="btn-sm btn-danger">حذف</button>-->
                    <!--    </form>-->
                    <!--</td>-->
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
