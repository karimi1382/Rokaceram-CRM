<div id="dw-s1" class="bmd-layout-drawer bg-faded ">

<div class="container-fluid side-bar-container ">
    <header class="pb-0 logo">
            <!-- <a class="navbar-brand ">
                <object class="side-logo" data="{{ asset('/svg/logo-8.svg') }}" type="image/svg+xml">
                </object>
            </a> -->
            <img  src="{{ asset('img/favicon/Logo.png') }}" alt="logo">
        </header>       
        <p class="side-comment  fnt-mxs"> {{ auth()->user()->name }} خوش آمدید</p>
        <p class="side-comment  fnt-mxs">ادمین سایت </p>

        <li class="side a-collapse short m-2 pr-1 pl-1">
            <a href="{{route('admin.index')}}" class="side-item selected c-dark ">
                <!-- <i class="fas fa-language  mr-1"></i> -->
                داشبورد 
                <!-- <span class="badge badge-pill badge-success">جدید</span> -->
            </a>
        </li>
        <!-- <ul class="side a-collapse short "> -->
            <!-- <a class="ul-text  fnt-mxs"><i class="fas fa-tachometer-alt mr-1"></i> محصول -->
                <!-- <span class="badge badge-info">4</span> -->
                <!-- <i class="fas fa-chevron-down arrow"></i></a>
            <div class="side-item-container hide animated">
                <li class="side-item "><a href="{{route('products.upload')}}" class="fnt-mxs"><i class="fas fa-angle-right mr-2"></i>اضافه کردن محصول</a>
                </li>
                <li class="side-item"><a href="{{route('products.index')}}" class="fnt-mxs"><i class="fas fa-angle-right mr-2"></i>مدیریت محصول</a>
                </li> -->
                <!-- <li class="side-item"><a href="./Login.html" class="fnt-mxs"><i class="fas fa-angle-right mr-2"></i>مدیریت موجودی محصول</a></li> -->
                <!-- <li class="side-item"><a href="./glogin.html"><i class="fas fa-angle-right mr-2"></i>صفحه ورود رنگی</a></li> -->

            <!-- </div> -->
        <!-- </ul> -->

        <ul class="side a-collapse short">
            <a class="ul-text  fnt-mxs"><i class="fas fa-cog mr-1"></i> اطلاعات پایه
                <!-- <span	class="badge badge-success">4</span> -->
                <i class="fas fas fa-chevron-down arrow"></i></a>
            <div class="side-item-container hide animated">
                <li class="side-item"><a href="{{route('cities.index')}}" class="fnt-mxs"><i class="fas fa-angle-right mr-2"></i>مدیریت شهر و استان</a></li>
                <!-- <li class="side-item"><a href="{{route('sizes.index')}}" class="fnt-mxs"><i class="fas fa-angle-right mr-2"></i>مدیریت سایز</a></li> -->
                <!-- <li class="side-item"><a href="{{route('tile_models.index')}}" class="fnt-mxs"><i class="fas fa-angle-right mr-2"></i>مدیریت طرح</a></li> -->
                <!-- <li class="side-item"><a href="{{route('admin.login_page.edit')}}" class="fnt-mxs"><i class="fas fa-angle-right mr-2"></i>مدیریت صفحه لاگین</a></li> -->
                <li class="side-item"><a href="{{route('users.index')}}" class="fnt-mxs"><i class="fas fa-angle-right mr-2"></i>مدیریت کاربران سایت</a></li>
            </div>
        </ul>
        <p class="side-comment  fnt-mxs">اطلاعات کاربری</p>
        <li class="side a-collapse short ">
            <a href="{{route('profile.show')}}" class="side-item "><i class="fas fa-fan fa-spin mr-1"></i>مدیریت پروفایل شخصی</a>
        </li>
        <li class="side a-collapse short ">
            <a href="{{route('profile.change-password-show')}}" class="side-item "><i class="fas fa-icons  mr-1"></i>تغییر رمز عبور</a>
        </li>

        <!-- <ul class="side a-collapse short ">
            <a class="ul-text  fnt-mxs"><i class="fas fa-cube mr-1"></i> کامپوننت های پایه <span
                    class="badge badge-danger">9</span><i class="fas fas fa-chevron-down arrow"></i></a>
            <div class="side-item-container hide animated">
                <li class="side-item"><a href="./alert.html" class="fnt-mxs"><i class="fas fa-angle-right mr-2"></i>alert</a>
                </li>
                <li class="side-item"><a href="./badge.html" class="fnt-mxs"><i class="fas fa-angle-right mr-2"></i>Badge</a></li>
                <li class="side-item"><a href="./breadcrumb.html" class="fnt-mxs"><i class="fas fa-angle-right mr-2"></i>Breadcrumb</a></li>
                <li class="side-item"><a href="./button.html" class="fnt-mxs"><i class="fas fa-angle-right mr-2"></i>Button</a></li>
                <li class="side-item"><a href="./card.html" class="fnt-mxs"><i class="fas fa-angle-right mr-2"></i>Card</a></li>
                <li class="side-item"><a href="./collapse.html" class="fnt-mxs"><i class="fas fa-angle-right mr-2"></i>Collapse</a></li>
                <li class="side-item"><a href="./Input.html" class="fnt-mxs"><i class="fas fa-angle-right mr-2"></i>Input</a></li>
                <li class="side-item"><a href="./jumborton.html" class="fnt-mxs"><i class="fas fa-angle-right mr-2"></i>Jumborton</a></li>
                <li class="side-item"><a href="./pagination.html" class="fnt-mxs"><i class="fas fa-angle-right mr-2"></i>Pagination</a></li>
                <li class="side-item"><a href="./progress.html" class="fnt-mxs"><i class="fas fa-angle-right mr-2"></i>Progress</a></li>
            </div>
        </ul> -->
        <!-- <ul class="side a-collapse short">
            <a class="ul-text  fnt-mxs"><i class="fas fa-layer-group mr-1"></i>کامپوننت های اضافی
                <span class="badge badge-warning">6</span>
                <i class="fas fas fa-chevron-down arrow"></i></a>
            <div class="side-item-container hide animated">
                <li class="side-item"><a href="./modal.html" class="fnt-mxs"><i class="fas fa-angle-right mr-2"></i>Modal</a></li>
                <li class="side-item"><a href="./toast.html" class="fnt-mxs"><i class="fas fa-angle-right mr-2"></i>Toast</a></li>
                <li class="side-item"><a href="./widget.html" class="fnt-mxs"><i class="fas fa-angle-right mr-2"></i>Widget</a></li>
                <li class="side-item"><a href="./Chart.html" class="fnt-mxs"><i class="fas fa-angle-right mr-2"></i>Chart</a></li>

            </div>
        </ul> -->

        <!-- <p class="side-comment  fnt-mxs">پشتیبانی</p>
        <li class="side a-collapse short ">
            <a href="https://github.com/MajidAlinejad/Nozha-rtl-Dashboard" class="side-item  fnt-mxs "><i class=" fab fa-github mr-1"></i>GitHub</a>
        </li>
        <li class="side a-collapse short ">
            <a href="https://github.com/MajidAlinejad/Nozha-rtl-Dashboard" class="side-item  fnt-mxs "><i class=" far fa-question-circle mr-1"></i>گزارش باگ</a>
        </li>
        <li class="side a-collapse short ">
            <a href="https://github.com/MajidAlinejad/Nozha-rtl-Dashboard" class="side-item  fnt-mxs "><i class=" far fa-life-ring mr-1"></i>حل مشکل</a>
        </li> -->

        <p class="side-comment  fnt-mxs">خروج</p>
        <li class="side a-collapse short pb-5">
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="side-item  fnt-mxs "><i class=" fas fa-coffee mr-1"></i>خروج از سیستم</a>





        </li>


    </div>

</div>