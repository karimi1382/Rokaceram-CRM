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
                                    <h3 class="lite-text ">ثبت درخواست محصول</h3>
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
    
        <!-- Success or Error Messages -->
        <div class="col-md-12">
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
            
            <form action="{{ route('dis_requests.store') }}" method="POST" class="col-md-6">
                @csrf
                <div class="row">
                    <div class="form-group mb-3 col">
                        <select name="product_id" id="product_id" class="form-control" required style="direction:ltr; display:none;">
                            <option value="{{ $product->id }}" selected>
                                {{ $product->id_number }} - {{ $product->name }} - {{ $product->degree }} - {{ $product->size }} - {{ $product->model }} - {{ $product->color }} - {{ $product->color_code }}
                            </option>
                        </select>
                        <p style="direction:ltr;font-weight: bold;color:green" class="text-left">
                            {{$product->id_number}} - {{$product->name}} - {{$product->degree}} - {{$product->size}} - {{$product->model}} - {{$product->color}} - {{$product->color_code}}
                        </p>
                    </div>
                </div>
    
                @if(auth()->user()->role == 'personnel')
                    <div class="form-group">
                        <label for="distributor">انتخاب نماینده</label>
                        <select name="distributor_id" id="distributor" class="form-control">
                            @foreach ($distributors as $distributor)
                                <option value="{{ $distributor->id }}">{{ $distributor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
    
                <input type="hidden" name="request_type" value="{{auth()->user()->role}}">
                <input type="hidden" name="status" value="Pending">
    
                <div class="row">
                    <div class="form-group mb-3 col">
                        <label for="request_size">متراژ درخواستی</label>
                        <input type="number" step="0.01" name="request_size" id="request_size" class="form-control" required>
                    </div>
    
                    <div class="form-group mb-3 col">
                        <label for="tel_number">شماره تماس هماهنگی</label>
                        <input type="text" name="tel_number" id="tel_number" class="form-control" required>
                    </div>
    
                    <div class="form-group mb-3 col">
                        <label for="request_owner">نام و نام خانوادگی درخواست کننده</label>
                        <input type="text" name="request_owner" id="request_owner" class="form-control" required>
                    </div>
                </div>
    
                <div class="form-group mb-3">
                    <label for="address">آدرس</label>
                    <textarea name="address" id="address" class="form-control" rows="3" required></textarea>
                </div>
    
                <button type="submit" class="btn btn-primary">ثبت درخواست</button>
            </form>
    
        </div>
    </div>
    
    </x-app-layout>
    