<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <!-- <meta charset="utf-8"> -->
        <!-- <meta name="viewport" content="width=device-width, initial-scale=1"> -->
        <!-- <meta name="csrf-token" content="{{ csrf_token() }}"> -->

        <!-- <title>{{ config('app.name', 'Laravel') }}</title> -->

        <!-- Fonts -->
        <!-- <link rel="preconnect" href="https://fonts.bunny.net"> -->
        <!-- <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" /> -->

        <!-- Scripts -->

        <meta charset="utf-8">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<title>CRM - Rokaceram</title>
	<meta name="description" content="nozha admin panel fully support rtl with complete dark mode css to use. ">
	<meta name=”robots” content="index, follow">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('/img/favicon/apple-touch-icon.png') }}">
	<link rel="icon" type="image/png" sizes="32x32"  href="{{ asset('/img/favicon/favicon-32x32.png') }}">
	<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('/img/favicon/favicon-16x16.png') }}" >
	<link rel="manifest" href="{{ asset('/img/favicon/site.webmanifest') }}">
	<link rel="mask-icon" href="{{ asset('/img/favicon/safari-pinned-tab.svg') }}"  color="#5bbad5">
	<meta name="msapplication-TileColor" content="#2b5797">
	<meta name="theme-color" content="#ffffff">
    <!-- Place favicon.ico in the root directory -->
    <link rel="stylesheet" href="{{ asset('css/normalize.css') }}" >
    <link href="{{ asset('/css/fontawsome/all.min.css') }}"  rel="stylesheet">
    <link rel="stylesheet"
        href="https://unpkg.com/bootstrap-material-design@4.1.1/dist/css/bootstrap-material-design.min.css"
        integrity="sha384-wXznGJNEXNG1NFsbm0ugrLFMQPWswR3lds2VeinahP8N0zJw9VWSopbjv2x7WCvX" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css"
        integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    
@vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="rtl persianumber">

        <div class="bmd-layout-container bmd-drawer-f-l avam-container animated bmd-drawer-in">
        
            @include('layouts.Top_menu')


            @auth
            
                @if(auth()->user()->role === 'admin')
                    @include('layouts.Right_menu_admin') 
                @elseif(auth()->user()->role === 'personnel')
                    @include('layouts.Right_menu_personel')
                @elseif(auth()->user()->role === 'distributor')
                    @include('layouts.Right_menu_distributor')
                @elseif(auth()->user()->role === 'manager')
                    @include('layouts.Right_menu_manager')
    
                @endif
            
        =   @endauth







            
        

                <main class="bmd-layout-content">
                    {{ $slot }}
                </main>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
        </div>
      



<script src="{{ asset('js/vendor/modernizr.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.2.1.min.js"
    integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
<script>
    window.jQuery || document.write('<script src="js/vendor/jquery-3.2.1.min.js"><\/script>')
</script>
<script src="https://unpkg.com/popper.js@1.12.6/dist/umd/popper.js"
    integrity="sha384-fA23ZRQ3G/J53mElWqVJEGJzU0sTs+SvzG8fXVWP+kJQ1lwFAOkcUOysnlKJC33U" crossorigin="anonymous">
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://unpkg.com/bootstrap-material-design@4.1.1/dist/js/bootstrap-material-design.js"
    integrity="sha384-CauSuKpEqAFajSpkdjv3z9t8E7RlpJ1UP0lKM/+NdtSarroVKu069AlsRPKkFBz9" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>

<script src="./js/persianumber.min.js"></script>
<script>
    $(document).ready(function () {
        $('body').bootstrapMaterialDesign();
        $('.persianumber').persiaNumber();

    });
</script>
<script>
    ! function (d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (!d.getElementById(id)) {
            js = d.createElement(s);
            js.id = id;
            js.src = 'https://weatherwidget.io/js/widget.min.js';
            fjs.parentNode.insertBefore(js, fjs);
        }
    }(document, 'script', 'weatherwidget-io-js');
</script>
{{$role = auth()->user()->role}}
@if ($role == 'personnel')
<script src="{{ asset('js/personel.js') }}"></script>
@elseif ($role == 'distributor')
<script src="{{ asset('js/distributor.js') }}"></script>
@else
<script src="{{ asset('js/main.js') }}"></script>
@endif


</body>

</html>