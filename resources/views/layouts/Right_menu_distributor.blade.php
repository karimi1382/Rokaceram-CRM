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
        <p class="side-comment  fnt-mxs">نماینده فروش</p>
        <li class="side a-collapse short m-2 pr-1 pl-1">
            <a href="/distributor" class="side-item selected c-dark ">
                <!-- <i class="fas fa-language  mr-1"></i> -->
                داشبورد 
                <!-- <span class="badge badge-pill badge-success">جدید</span> -->
            </a>
        </li>
        <li class="side a-collapse short m-2 pr-1 pl-1">
            <a href="{{route('products.index')}}" class="side-item   ">
                <!-- <i class="fas fa-language  mr-1"></i> -->
               مشاهده محصولات 
                <!-- <span class="badge badge-pill badge-success">جدید</span> -->
            </a>
        </li>
   

        <ul class="side a-collapse short">
            <a class="ul-text  fnt-mxs"><i class="fas fa-cog mr-1"></i> درخواست ها
                <!-- <span	class="badge badge-success">4</span> -->
                <i class="fas fas fa-chevron-down arrow"></i></a>
            <div class="side-item-container hide animated">
                <li class="side-item"><a href="{{route('dis_requests.index')}}" class="fnt-mxs"><i class="fas fa-angle-right mr-2"></i>مدیریت درخواست ها</a></li>
                <li class="side-item"><a href="{{ route('dis_requests.havale_index') }}" class="fnt-mxs"><i class="fas fa-angle-right mr-2"></i>حواله های ثبت شده</a></li>

                <!--<li class="side-item"><a href="{{ route('dis_requests.create') }}" class="fnt-mxs"><i class="fas fa-angle-right mr-2"></i>ثبت درخواست جدید</a></li>-->
                <li class="side-item"><a href="{{ route('dis_requests.completed') }}" class="fnt-mxs"><i class="fas fa-angle-right mr-2"></i> درخواست های حواله دار</a></li>
                <li class="side-item"><a href="{{ route('dis_requests.havale_completed') }}" class="fnt-mxs"><i class="fas fa-angle-right mr-2"></i> حواله های تکمیل شده </a></li>

                <!-- <li class="side-item"><a href="./rtl.html" class="fnt-mxs"><i class="fas fa-angle-right mr-2"></i>درخواست های تایید شده</a></li> -->
                <!-- <li class="side-item"><a href="./sidebar.html" class="fnt-mxs"><i class="fas fa-angle-right mr-2"></i>ساید بار</a></li> -->
            </div>
        </ul>

        <ul class="side a-collapse short">
            <a class="ul-text  fnt-mxs"><i class="fas fa-cog mr-1"></i> شکایت ها
                <!-- <span	class="badge badge-success">4</span> -->
                <i class="fas fas fa-chevron-down arrow"></i></a>
            <div class="side-item-container hide animated">

               <li class="side-item">
                    <a href="{{ route('complaints.create') }}" class="fnt-mxs">
                        <i class="fas fa-angle-right mr-2"></i> ثبت شکایت جدید
                    </a>
                </li>
                <li class="side-item">
                    <a href="{{ route('complaints.index') }}" class="fnt-mxs">
                        <i class="fas fa-angle-right mr-2"></i> مدیریت شکایت ها
                    </a>
                </li>
                <li class="side-item">
                    <a href="{{ route('complaints.completed') }}" class="fnt-mxs">
                        <i class="fas fa-angle-right mr-2"></i> شکایت های تکمیل شده
                    </a>
                </li>

                <!-- <li class="side-item"><a href="./rtl.html" class="fnt-mxs"><i class="fas fa-angle-right mr-2"></i>درخواست های تایید شده</a></li> -->
                <!-- <li class="side-item"><a href="./sidebar.html" class="fnt-mxs"><i class="fas fa-angle-right mr-2"></i>ساید بار</a></li> -->
            </div>
        </ul>







        <!-- <ul class="side a-collapse short">
            <a class="ul-text  fnt-mxs"><i class="fas fa-cog mr-1"></i> شکایت ها
                <i class="fas fas fa-chevron-down arrow"></i></a>
            <div class="side-item-container hide animated">
                <li class="side-item"><a href="./color.html" class="fnt-mxs"><i class="fas fa-angle-right mr-2"></i>مدیریت شکایت ها</a></li>
                <li class="side-item"><a href="./typo.html" class="fnt-mxs"><i class="fas fa-angle-right mr-2"></i>ثبت شکایت جدید</a></li>
                <li class="side-item"><a href="./dark-mode.html" class="fnt-mxs"><i class="fas fa-angle-right mr-2"></i>آرشیو شکایت ها</a></li>
             
            </div>
        </ul> -->

  


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