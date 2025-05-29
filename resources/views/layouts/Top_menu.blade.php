<header class="bmd-layout-header ">
        <div class="navbar navbar-light bg-faded animate__animated animate__fadeInDown">
            <button class="navbar-toggler animate__animated animate__wobble animate__delay-2s" type="button"
                data-toggle="drawer" data-target="#dw-s1">
                <span class="navbar-toggler-icon"></span>
                <!-- <i class="material-Animation">menu</i> -->
            </button>
            <ul class="nav navbar-nav p-0">
                <!-- <li class="nav-item">
                    <div class="dropdown">
                        <button class="btn  dropdown-toggle  m-0" type="button" id="dropdownMenu2"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="far fa-envelope fa-lg"></i><span
                                class="badge badge-pill badge-danger animate__animated animate__flash animate__repeat-3 animate__slower animate__delay-2s">5</span>
                        </button>
                        <div aria-labelledby="dropdownMenu2"
                            class="dropdown-menu dropdown-menu-right dropdown-menu dropdown-menu-right-lg">
                            <span class="dropdown-item dropdown-header">15 اطلاعیه</span>
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item">
                                <i class="far fa-envelope c-main mr-2"></i> 4 پیام جدید
                                <span class="float-right-rtl text-muted text-sm">3 دقیقه پیش</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item">
                                <i class="far fa-user c-main mr-2"></i> 8 درخواست دوستی
                                <span class="float-right-rtl text-muted text-sm">12 ساعت پیش</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item">
                                <i class="far fa-file c-main mr-2"></i> 3 گزارش جدید
                                <span class="float-right-rtl text-muted text-sm">2 روز پیش</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item dropdown-footer">دیدن همه</a>
                        </div>
                    </div>
                </li> -->
                <!-- <li class="nav-item">
                    <div class="dropdown">
                        <button class="btn  dropdown-toggle m-0" type="button" id="dropdownMenu3"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="far fa-bell  fa-lg "></i><span
                                class="badge badge-pill badge-warning animate__animated animate__flash animate__repeat-3 animate__slower animate__delay-2s">5</span>
                        </button>
                        <div aria-labelledby="dropdownMenu2"
                            class="dropdown-menu dropdown-menu-right dropdown-menu dropdown-menu-right-lg">
                            <span class="dropdown-item dropdown-header persianumber">15 اطلاعیه</span>
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item">
                                <i class="far fa-envelope c-main mr-2"></i> 4 پیام جدید
                                <span class="float-right-rtl text-muted text-sm">3 دقیقه پیش</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item">
                                <i class="far fa-user c-main mr-2"></i> 8 درخواست دوستی
                                <span class="float-right-rtl text-muted text-sm">12 ساعت پیش</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item">
                                <i class="far fa-file c-main mr-2"></i> 3 گزارش جدید
                                <span class="float-right-rtl text-muted text-sm">2 روز پیش</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item dropdown-footer">دیدن همه</a>
                        </div>
                    </div>
                </li> -->
                <li class="nav-item">
                
                @if (auth()->user()->userData->profile_picture)
                    <img src="{{ asset('storage/' . auth()->user()->userData->profile_picture) }}" alt="Profile Picture" class="rounded-circle screen-user-profile" width="150">
                @else
                    <img src="{{ asset('/img/user-profile.jpg')  }}" alt="Default Profile Picture" class="rounded-circle screen-user-profile" width="150">
                @endif

                
                <!-- <img src="{{ asset('/img/user-profile.jpg') }}" alt="..." class="rounded-circle screen-user-profile"> -->
                </li>
                <li class="nav-item">
                    <div class="dropdown">
                        <button class="btn  dropdown-toggle m-0" type="button" id="dropdownMenu4"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{auth()->user()->name}}
                        </button>
                        <div aria-labelledby="dropdownMenu4"
                            class="dropdown-menu  pl-3 dropdown-menu-right dropdown-menu dropdown-menu-right"
                            aria-labelledby="dropdownMenu2">
                            <a href="{{route('profile.show')}}" class="dropdown-item" type="button"><i
                                    class="far fa-user c-main fa-sm mr-2"></i>پروفایل</a>
                            <!-- <button onclick="dark()" class="dropdown-item" type="button"><i
                                    class="fas fa-moon fa-sm c-main mr-2"></i>حالت شب</button>
                            <button class="dropdown-item" type="button"><i
                                    class="fas fa-cog c-main fa-sm mr-2"></i>تنظیمات</button> -->
                            <button  onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="dropdown-item" type="button"><i
                                    class="fas fa-sign-out-alt c-main fa-sm mr-2"></i>خروج</button>
                        </div>
                    </div>
                </li>




            </ul>
        </div>
    </header>