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




            <div class="container-fluid ">

            <!-- content -->
            <!-- breadcrumb -->
<div class="col-md-12 text-center">
     <img src="{{ asset('/img/favicon/RokaEN.png') }}" style="width:150px;margin:auto" />
</div>
<hr>
            <div class="row  m-1 pb-4 mb-3 ">
                <div class="col-xs-12  col-sm-12  col-md-12  col-lg-12 p-2">
                    <div class="page-header breadcrumb-header ">
                        <div class="row align-items-end ">
                            <div class="col-lg-8">
                                <div class="page-header-title text-left-rtl">
                                    <div class="d-inline">
                                        <h3 class="lite-text ">شرکت روکاسرام </h3>
        
                                        <span class="lite-text ">مشاهده موجودی محصولات</span>
                                    </div>
                                </div>
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
                      <div class="bg-light p-5 rounded mb-3">
            <form method="POST" action="{{ route(name: 'ShowProductsearch') }}" class="mb-3">
                @csrf
                <div class="row">
                    <div class="col-md-3">
                        <input type="text" name="id_number" class="form-control" placeholder="کد محصول">
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="name" class="form-control" placeholder="نام برند">
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="degree" class="form-control" placeholder="درجه">
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="size" class="form-control" placeholder="سایز">
                    </div>
                    <div class="col-md-3 mt-2">
                        <input type="text" name="model" class="form-control" placeholder="طرح">
                    </div>
                    <div class="col-md-3 mt-2">
                        <input type="text" name="color" class="form-control" placeholder="رنگ">
                    </div>
                    <div class="col-md-3 mt-2">
                        <input type="text" name="color_code" class="form-control" placeholder="کد رنگ">
                    </div>
                    <div class="col-md-3 mt-2">
                        <button type="submit" class="btn btn-primary btn-block">جستجو</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- New Search Bar and Action Buttons (Below the Original Search Form) -->
        <div class="row mt-3">
            <div class="col-md-4">
                <!-- Modern Search Bar with Rounded Design and Icon -->
                <div class="input-group">
                    <input type="text" id="searchInput" class="form-control rounded-pill" placeholder="جستجو کنید...">
                   
                </div>
            </div>
            <div class="col-md-4"></div>
            <div class="col-md-4 d-flex justify-content-end">
                <!-- Export Excel Button -->
                <a href="{{ route('products.export') }}" class="btn btn-success rounded-pill">
                    <i class="fas fa-file-excel pl-2 pt-1"></i> دریافت فایل اکسل
                </a>
            </div>
        </div>

        <!-- Sorting Buttons -->
        <div class="row mt-1 mb-1 rounded align-items-center">
            <div class="col-md-12 d-flex justify-content-end">
                <div class="">
                    <button id="sort-asc" class="btn btn-primary  " style="height: 40px;">
                        <i class="fas fa-arrow-up" style="font-size: 20px;"></i>
                    </button>
                    <button id="sort-desc" class="btn btn-primary " style="height: 40px;">
                        <i class="fas fa-arrow-down" style="font-size: 20px;"></i> 
                    </button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive mt-3">
            <table border="1" class="table table-bordered">
                <thead>
                    <tr>
                        <th>کد محصول</th>
                        <th>نام برند</th>
                        <th>درجه</th>
                        <th>سایز</th>
                        <th>طرح</th>
                        <th>رنگ</th>
                        <th>کد رنگ</th>
                        <th>موجودی</th>
                    </tr>
                </thead>
                <tbody id="product-list">
                    <?php $n=0 ?>
                    @foreach ($products as $product)
                    @if($product->color_code != 'CL000')
                    @if($product->inventory > 23)
                    <?php $n++ ?>
                        <tr>
                            <td>{{ $product->id_number }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->degree }}</td>
                            <td>{{ $product->size }}</td>
                            <td>{{ $product->model }}</td>
                            <td>{{ $product->color }}</td>
                            <td>{{ $product->color_code }}</td>
                            <td class="inventory">
                                    
                                        @if($product->inventory > 1000)
                                            @if($product->degree <> '4')
                                                < 1000
                                            @else
                                                {{ number_format($product->inventory, 2) }}
                                            @endif                                 
                                        @else
                                        {{ number_format($product->inventory, 2) }}
                                        @endif
                                    @endif
                            </td>
                        </tr>
                        @endif
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Include jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <script>
            $(document).ready(function() {
                function sortTable(order) {
                    var rows = $('#product-list tr').get();

                    rows.sort(function(a, b) {
                        var A = $(a).find('.inventory').text().trim();
                        var B = $(b).find('.inventory').text().trim();

                        // Convert text "< 1000" to a number
                        A = A.includes("<") ? 1000 : parseInt(A, 10);
                        B = B.includes("<") ? 1000 : parseInt(B, 10);

                        if (order === 'asc') {
                            return A - B;
                        } else {
                            return B - A;
                        }
                    });

                    $.each(rows, function(index, row) {
                        $('#product-list').append(row);
                    });
                }

                // Button Click Events
                $('#sort-asc').click(function() {
                    sortTable('asc'); // Sort Low to High
                });

                $('#sort-desc').click(function() {
                    sortTable('desc'); // Sort High to Low
                });

                // Search Functionality
                $("#searchInput").on("keyup", function() {
                    var value = $(this).val().toLowerCase(); // Get the value of the search input

                    // Normalize the value for number comparison
                    var normalizedValue = normalizeNumbers(value);

                    $("table tbody tr").filter(function() {
                        var modelColumn = $(this).find("td:nth-child(5)").text().toLowerCase();
                        var sizeColumn = $(this).find("td:nth-child(4)").text().toLowerCase();
                        var colorColumn = $(this).find("td:nth-child(6)").text().toLowerCase();

                        // Normalize the values in the columns as well
                        var normalizedModel = normalizeNumbers(modelColumn);
                        var normalizedSize = normalizeNumbers(sizeColumn);
                        var normalizedColor = normalizeNumbers(colorColumn);

                        // Check if the search value is present in Model, Size or Color columns
                        $(this).toggle(
                            normalizedModel.indexOf(normalizedValue) > -1 ||
                            normalizedSize.indexOf(normalizedValue) > -1 ||
                            normalizedColor.indexOf(normalizedValue) > -1
                        );
                    });
                });

                // Normalize numbers for comparison (convert Farsi to English)
                function normalizeNumbers(str) {
                    var farsiDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
                    var englishDigits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

                    for (var i = 0; i < farsiDigits.length; i++) {
                        str = str.replace(new RegExp(farsiDigits[i], 'g'), englishDigits[i]);
                    }
                    return str;
                }
            });
        </script>








            </div>



</body>

</html>
