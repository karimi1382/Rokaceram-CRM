
<!doctype html>
<html lang="en">
  <head>
  	<title>CRM - Rokaceram</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">

<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('/img/favicon/apple-touch-icon.png') }}">
	<link rel="icon" type="image/png" sizes="32x32"  href="{{ asset('/img/favicon/favicon-32x32.png') }}">
	<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('/img/favicon/favicon-16x16.png') }}" >
	
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	
	<link rel="stylesheet" href="{{ asset('my_login/css/style.css') }}">
	</head>
	<body class="img js-fullheight" style="direction:rtl;background-image: url({{ asset('my_login/images/bg.jpg') }});">

    {{ $slot }}

    <script src="{{ asset('my_login/js/jquery.min.js') }}"></script>
  <script src="{{ asset('my_login/js/popper.js') }}"></script>
  <script src="{{ asset('my_login/js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('my_login/js/main.js') }}"></script>

	</body>
</html>






