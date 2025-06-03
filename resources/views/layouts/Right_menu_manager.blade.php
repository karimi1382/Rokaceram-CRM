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
        <p class="side-comment  fnt-mxs">داشتبرد مدیریت </p>
        <li class="side a-collapse short m-2 pr-1 pl-1">
            <a href="/manager" class="side-item selected c-dark ">
                <!-- <i class="fas fa-language  mr-1"></i> -->
                داشبورد 
                <!-- <span class="badge badge-pill badge-success">جدید</span> -->
            </a>
        </li>

        <li class="side a-collapse short ">
            <a href="{{route('products.index')}}" class="side-item "><i class="fas fa-fan fa-spin mr-1"></i>مشاهده محصول </a>
        </li>
        <li class="side a-collapse short ">
            <a href="{{ route('users.with.parents') }}" class="side-item "><i class="fas fa-icons  mr-1"></i>اطلاعات نماینده ها</a>
        </li>
        <li class="side a-collapse short ">
            <a href="{{route('ManagerController.personneltargetshow')}}" class="side-item "><i class="fas fa-icons  mr-1"></i>اطلاعات سرپرست ها</a>
        </li>

     @if( auth()->user()->target == 1)
        
        <ul class="side a-collapse short">
            <a class="ul-text  fnt-mxs"><i class="fas fa-cog mr-1"></i> تارگت های فروش
                <!-- <span	class="badge badge-success">4</span> -->
                <i class="fas fas fa-chevron-down arrow"></i></a>
            <div class="side-item-container hide animated">
                <li class="side-item"><a href="{{ route('admin.personnel.index') }}" class="fnt-mxs"><i class="fas fa-angle-right mr-2"></i>   پرسنل فروش</a></li>
                <li class="side-item"><a href="{{ route('admin.distributor.index') }}" class="fnt-mxs"><i class="fas fa-angle-right mr-2"></i>   نماینده های فروش</a></li>
            </div>
        </ul>

        {{-- <ul class="side a-collapse short">
            <a class="ul-text  fnt-mxs"><i class="fas fa-cog mr-1"></i> درخواست ها
                <i class="fas fas fa-chevron-down arrow"></i></a>
            <div class="side-item-container hide animated">
                <li class="side-item"><a href="{{ route('approved.requests') }}" class="fnt-mxs"><i class="fas fa-angle-right mr-2"></i>  در انتظار تایید   </a></li>
                <li class="side-item"><a href="{{ route('show.approved.requests') }}" class="fnt-mxs"><i class="fas fa-angle-right mr-2"></i>  تایید شده ها    </a></li>
            </div>
        </ul> --}}
        @endif
        <ul class="side a-collapse short">
            <a class="ul-text  fnt-mxs"><i class="fas fa-cog mr-1"></i> حواله ها
                <!-- <span	class="badge badge-success">4</span> -->
                <i class="fas fas fa-chevron-down arrow"></i></a>
            <div class="side-item-container hide animated">
                <li class="side-item"><a href="{{ route('admin.havale_all') }}" class="fnt-mxs"><i class="fas fa-angle-right mr-2"></i>  حواله های رزرو شده  </a></li>
                <li class="side-item"><a href="{{ route('admin.havale_completed') }}" class="fnt-mxs"><i class="fas fa-angle-right mr-2"></i>  حواله های ارسال شده   </a></li>
            </div>
        </ul>

        <ul class="side a-collapse short">
            <a class="ul-text  fnt-mxs"><i class="fas fa-cog mr-1"></i> گزارشات
                <!-- <span	class="badge badge-success">4</span> -->
                <i class="fas fas fa-chevron-down arrow"></i></a>
            <div class="side-item-container hide animated">
                <li class="side-item"><a href="{{ route('report.reserved-products') }}" class="fnt-mxs"><i class="fas fa-angle-right mr-2"></i>   رزروی های موقت </a></li>
            </div>
            <div class="side-item-container hide animated">
                <li class="side-item"><a href="{{ route('report.agents.performance') }}" class="fnt-mxs"><i class="fas fa-angle-right mr-2"></i>  عملکرد نمایندگان </a></li>
            </div>
        </ul>





        <p class="side-comment  fnt-mxs">اطلاعات کاربری</p>
        <li class="side a-collapse short ">
            <a href="{{route('profile.show')}}" class="side-item "><i class="fas fa-fan fa-spin mr-1"></i>مدیریت پروفایل شخصی</a>
        </li>
        <li class="side a-collapse short ">
            <a href="{{route('profile.change-password-show')}}" class="side-item "><i class="fas fa-icons  mr-1"></i>تغییر رمز عبور</a>
        </li>


        <p class="side-comment  fnt-mxs">خروج</p>
        <li class="side a-collapse short pb-5">
        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="side-item  fnt-mxs "><i class=" fas fa-coffee mr-1"></i>خروج از سیستم</a>
        </li>


    </div>

</div>