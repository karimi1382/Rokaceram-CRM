<x-app-layout>
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row m-1 pb-4 mb-3">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p-2">
                <div class="page-header breadcrumb-header">
                    <div class="row align-items-end">
                        <div class="col-lg-8">
                            <div class="page-header-title text-left-rtl">
                                <div class="d-inline">
                                    <h3 class="lite-text">مدیریت کاربران سایت</h3>
                                    <span class="lite-text">ایجاد کاربر جدید</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i></a></li>
                                <li class="breadcrumb-item active">داشبورد</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Display Success/Error Message -->
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

        <!-- Form to Create User -->
        <div class="row m-2 mb-1 bg-light p-5 rounded row justify-content-md-center">
            <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data" class="col-md-8">
                @csrf

                <!-- Name Field -->
                <div class="form-group">
                    <label for="name">نام و نام خانوادگی</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>

                <!-- Email Field -->
                <div class="form-group">
                    <label for="email">ایمیل ( نام کاربری )</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>

                <!-- Role Field (Dropdown) -->
                <div class="form-group">
                    <label for="role">نقش کاربر</label>
                    <select name="role" class="form-control" required>
                        <option value="personnel">سرپرست فروش</option>
                        <option value="manager">مدیریت</option>
                        <option value="distributor">نماینده فروش</option>
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
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                </div>

                <!-- City Field (Dropdown) -->
                <div class="form-group">
                    <label for="city">شهر</label>
                    <select name="city_id" class="form-control" required>
                        <option value="">انتخاب شهر</option>
                        @foreach ($cities as $city)
                            <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>
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
                                <option value="{{ $personel->id }}" {{ old('personel_id') == $personel->id ? 'selected' : '' }}>
                                    {{ $personel->name }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

  <!-- Product Names Selection (Checkboxes in Horizontal Layout) -->



                <!-- Profile Picture -->
                <div class="form-group">
                    <label for="profile_picture">تصویر پروفایل</label>
                    <input type="file" name="profile_picture" class="form-control" accept="image/*">
                </div>

                <button type="submit" class="btn btn-primary">ایجاد کاربر</button>
            </form>
        </div>
    </div>
</x-app-layout>
