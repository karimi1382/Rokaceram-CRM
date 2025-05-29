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
                                <h3 class="lite-text ">گزارش</h3>
                                <span class="lite-text ">گزارش محصولات رزرو شده بر اساس متراژ</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item "><a href="#"><i class="fas fa-home"></i></a></li>
                            <li class="breadcrumb-item active">گزارشات</li>
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
    
    @if(isset($error))
    <div class="alert alert-danger">
        {{ $error }}
    </div>
    @else
    <div class="row m-2 mb-1 bg-light p-5 rounded row justify-content-md-center">


       
            <h1 class="mb-4">گزارش متراژ محصولات رزرو شده بدون تایید مالی</h1>
       
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>کد محصول</th>
                        <th>نام محصول</th>
                        <th>متراژ رزرو شده بدون تایید مالی</th>
                        <th>شماره حواله‌ها</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $productCode => $item)
                        <tr>
                            <td>{{ $productCode }}</td>
                            <td>{{ $item['product_name'] }}</td>
                            <td>{{ $item['total_product_mr'] }}</td>
                            <td>
                                @foreach ($item['havales'] as $havale)
        
                                    <a href="{{ route('dis_requests.havale_data', $havale) }}" class="btn btn-info">  {{ $havale }}</a>

                                @endforeach
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">داده‌ای یافت نشد.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            
       
      


    </div>
    
 @endif
    </x-app-layout>
    