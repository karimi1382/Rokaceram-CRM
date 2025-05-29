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
                            <span class="lite-text ">ویرایش کاربر  </span>
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

<form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data" class="col-md-6">
        @csrf
        @method('PUT') <!-- PUT method for updating the user -->

        <!-- Name Field -->
        <div class="form-group">
            <label for="name">نام و نام خانوادگی</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
        </div>

        <!-- Email Field -->
        <div class="form-group">
            <label for="email">ایمیل ( نام کاربری )</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required >
        </div>

        <!-- Role Field (Dropdown) -->
        <div class="form-group">
            <label for="role">نقش کاربر</label>
            <select name="role" class="form-control" required>
                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>ادمین</option>
                <option value="personnel" {{ $user->role == 'personnel' ? 'selected' : '' }}>سرپرست فروش</option>
                <option value="manager" {{ $user->role == 'manager' ? 'selected' : '' }}>مدیریت</option>
                <option value="distributor" {{ $user->role == 'distributor' ? 'selected' : '' }}>نماینده فروش</option>
            </select>
        </div>

       <!-- Password Field (Optional) -->
       <div class="form-group">
            <label for="password">رمز عبور ( درصورتی که تمایل به ویرایش ندارید آن را خالی بگذارید )</label>
            <input type="password" name="password" class="form-control">
        </div>

        <!-- Password Confirmation Field -->
        <div class="form-group">
            <label for="password_confirmation">تکرار رمز عبور</label>
            <input type="password" name="password_confirmation" class="form-control">
        </div>

        <!-- Phone Field -->
        <div class="form-group">
            <label for="phone">تلفن</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->userData->phone) }}">
        </div>

        <!-- City Field (Dropdown) -->
        <div class="form-group">
            <label for="city">شهر</label>
            <select name="city_id" class="form-control" required>
                <option value="">انتخاب شهر</option>
                @foreach ($cities as $city)
                    <option value="{{ $city->id }}" {{ old('city_id', $user->userData->city_id) == $city->id ? 'selected' : '' }}>
                          {{ $city->name }} | {{ $city->state }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Personnel Field (Dropdown) -->
        <div class="form-group">
            <label for="personel_id">زیر مجموعه</label>
            <select name="personel_id" class="form-control">
                <option value="">انتخاب پرسنل</option>
                @foreach ($users as $personel)
                @if($personel->role != "distributor")

                    <option value="{{ $personel->id }}" 
                        {{ old('personel_id', $user->userData->personel_id) == $personel->id ? 'selected' : '' }}>
                        {{ $personel->name }}
                    </option>
                    @endif
                @endforeach
            </select>
        </div>

        <!-- Customer Type Field -->
        <!-- <div class="form-group">
            <label for="customer_type">زیر مجموعه</label>
            <input type="text" name="customer_type" class="form-control" value="{{ old('customer_type', $user->userData->customer_type) }}">
        </div> -->
<!-- Product Names Selection (Checkboxes in Horizontal Layout) -->
<div class="form-group">
    <label class="form-label">انتخاب نام محصولات:</label>
    <div class="row">
        @foreach($productNames as $product)
            <div class="col-md-3">
                <div class="form-check">
                    <input type="checkbox" name="visible_products_names[]" value="{{ $product->name }}" class="form-check-input"
                        {{ in_array($product->name, old('visible_products_names', $user->productVisibilities->pluck('product_name')->toArray() ?: [])) ? 'checked' : '' }}>
                    <label class="form-check-label">{{ $product->name }}</label>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Product Degrees Selection (Checkboxes in Horizontal Layout) -->
<div class="form-group">
    <label class="form-label">انتخاب درجه محصولات:</label>
    <div class="row">
        @foreach($productNames as $product)
            <div class="col-md-3">
                <div class="form-check">
                    <label>Degree for {{ $product->name }}:</label><br>
                    @foreach($productDegrees[$product->name] as $degree)
                        <input type="checkbox" name="visible_products[{{ $product->name }}][degree][]" value="{{ $degree->degree }}" class="form-check-input"
                            {{ in_array($degree->degree, old('visible_products.' . $product->name . '.degree', $user->productVisibilities->where('product_name', $product->name)->pluck('product_degree')->toArray())) ? 'checked' : '' }}>
                        <label class="form-check-label">Degree: {{ $degree->degree }}</label><br>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>






        <!-- Profile Picture -->
        <div class="form-group">
            <label for="profile_picture">تصویر پروفایل</label>
            @if ($user->userData->profile_picture)
                <div>
                    <img src="{{ asset('storage/' . $user->userData->profile_picture) }}" alt="Profile Picture" width="100" class="mb-2">
                    <p>Current Picture</p>
                </div>
            @endif
            <input type="file" name="profile_picture" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">ویرایش کاربر</button>
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
