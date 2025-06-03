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
                                    <h3 class="lite-text ">جزئیات درخواست محصول</h3>
                                    <!-- <span class="lite-text ">ایجاد کاربر جدید</span> -->
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
        @if(!$connectionIsOk)
        <div class="alert alert-danger">
            ارتباط با سرور قطع می‌باشد.
        </div>
    @else
        <!-- widget -->
        <div class="col-md-12">
            <!-- Display Success or Error Message -->
            @if (session()->has('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
        
            @if (session()->has('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
        
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
        


        <div class="container">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card shadow mb-4">
                        <div class="card-header bg-dark text-white">
                            <h4 class="mb-0">جزئیات سفارش شما</h4>
                        </div>
                        <div class="card-body">
                            <p><strong>نام محصول:</strong>
                                {{ $request->product_details->name }}  
                                {{ $request->product_details->degree }}  
                                {{ $request->product_details->size }}  
                                {{ $request->product_details->model }}  
                                {{ $request->product_details->color }}  
                                {{ $request->product_details->color_code }}
                            </p>
                            <p><strong>تاریخ ایجاد:</strong> {{ $request->created_at_jalali }}</p>


                            <!-- If user is personnel and request status is 'Pending', show editable field -->

                            <!-- If request status is NOT Pending, show plain text -->
                            <p><strong>متراژ درخواست شده :</strong> {{ $request->request_size }}</p>


                            <p><strong>درخواست کننده:</strong> {{ $request->request_owner }}</p>
                            <p><strong>شماره تماس هماهنگی :</strong> {{ $request->tel_number }}</p>
                            <p><strong>آدرس :</strong> {{ $request->address }}</p>

                            <p><strong>وضعیت درخواست :</strong>
                                <span
                                    class="badge p-2 
                            @if ($request->status === 'Pending') bg-warning 
                            @elseif($request->status === 'Approved') bg-success 
                            @elseif($request->status === 'Rejected') bg-danger 
                            @elseif($request->status === 'Completed') bg-info 
                            @elseif($request->status === 'In Progress') bg-success  @endif">
                                    @if ($request->status == 'Pending')
                                        در انتظار بررسی
                                    @elseif($request->status == 'Approved')
                                    تایید مالی - در انتظار بارگیری
                                    @elseif($request->status == 'Rejected')
                                        رد شده
                                    @elseif($request->status == 'In Progress')
                                        حواله برای این درخواست ثبت شده است 
                                    @endif
                                </span>
                            </p>
                            @if(auth()->user()->role == 'personnel')
                            <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-warning" onclick="toggleEditForm()">ویرایش متراژ</button>
                            </div>
                            @endif

                        </div>
                    </div>
                    
                    <div class="row">
                        <div id="editForm" class="col-md-12 mb-4" style="display: none;">
                            <div class="card shadow ">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">ویرایش متراژ درخواست</h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('requests.updateSize', $request->id) }}" method="POST">
                                        @csrf
                                        @method('PUT') <!-- Laravel requires PUT/PATCH for updates -->
                                        
                                        <div class="form-group mb-3">
                                            <label for="request_size"><strong>متراژ درخواست شده :</strong></label>
                                            <input type="text" name="request_size" id="request_size" class="form-control"
                                                   value="{{ $request->request_size }}" required oninput="validateInput(this)">
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <button type="submit" class="btn btn-success">ویرایش متراژ</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 mb-4">
                            @if(auth()->user()->role == 'personnel')
                            <div class="card shadow">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">درج شماره حواله</h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('dis_requests.store_havale', $request->id) }}" method="POST">
                                        @csrf
                                        <div class="form-group mb-3">
                                            <label for="havale_number">شماره حواله</label>
                                            <input type="text" name="havale_number" class="form-control" required>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <button type="submit" class="btn btn-success">اضافه کردن حواله</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            @endif
                        </div>
                       
                        <!-- Display All Havale Numbers for this Request -->
                        <div class="col-md-12 mb-4">
                            <div class="card shadow">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">لیست شماره حواله‌ها</h5>
                                </div>
                                <div class="card-body">
                                    @if($request->disRequestHavales->isEmpty())
                                        <p class="text-muted">هیچ حواله‌ای برای این درخواست ثبت نشده است.</p>
                                    @else
                                        <table class="table table-bordered table-striped">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>شماره حواله</th>
                                                    <th>عملیات</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($request->disRequestHavales as $havale)
                                                    <tr>
                                                        <td>{{ $havale->havale_number }}</td>
                                                        <td class="text-center">
                                                            <!-- Delete Button with Confirmation -->
                                                            <form action="{{ route('dis_requests.delete_havale', $havale->id) }}" method="POST" style="display:inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('آیا مطمئن هستید که می خواهید این حواله را حذف کنید؟')">
                                                                    <i class="fas fa-trash"></i> حذف
                                                                </button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        
                        <!-- Custom Modal for Confirmation -->
                        <div id="confirmationModal" class="modal" style="display: none;">
                            <div class="modal-content">
                                <h5>آیا مطمئن هستید که می خواهید درخواست را لغو کنید؟</h5>
                                <div class="d-flex justify-content-end">
                                    <button class="btn btn-success m-1" onclick="confirmCancelRequest()">تایید</button>
                                    <button class="btn btn-danger m-1" onclick="closeModal()">لغو</button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Styles -->
                        <style>
                            /* The Modal */
                            .modal {
                                display: none; /* Hidden by default */
                                position: fixed; /* Stay in place */
                                z-index: 9999; /* Make sure it's above everything else */
                                left: 0;
                                top: 0;
                                width: 100%;
                                height: 100%;
                                background-color: rgba(0, 0, 0, 0.5); /* Black background with opacity */
                                display: flex;
                                align-items: center;
                                justify-content: center;
                            }
                        
                            /* Modal Content */
                            .modal-content {
                                background-color: #fefefe;
                                padding: 20px;
                                border-radius: 5px;
                                text-align: center;
                                width: 50%;
                            }
                        
                            /* Styling for buttons */
                            .btn {
                                padding: 10px;
                                margin: 5px;
                            }
                        </style>
                        
                        <!-- JavaScript to Handle Modal -->
                        <script>
                            // Show the modal
                            function showConfirmationModal() {
                                document.getElementById('confirmationModal').style.display = "flex";
                            }
                        
                            // Close the modal
                            function closeModal() {
                                document.getElementById('confirmationModal').style.display = "none";
                            }
                        
                            // Confirm and cancel the request
                            function confirmCancelRequest() {
                                window.location.href = "{{ route('dis_requests.cancel', $request->id) }}";
                            }
                        </script>
                        
                        
                        
                        
                        
                    </div>
                


                </div>


                <div class="col-md-6">
                    <div class="card shadow mb-4">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0">توضیحات اضافه شده</h5>
                        </div>
                        <div class="card-body">
                            @if ($request->requestDetails->isEmpty())
                                <p class="text-muted">هیچ توضیحاتی اضافه نشده است</p>
                            @else
                                @foreach ($request->requestDetails as $detail)
                                    <div class="d-flex mb-3">
                                        <div class="m-2">
                                            @if ($detail->user->userData && $detail->user->userData->profile_picture)
                                                <img src="{{ asset('storage/' . $detail->user->userData->profile_picture) }}"
                                                    alt="{{ $detail->user->name }}" class="rounded-circle"
                                                    width="50" height="50">
                                            @else
                                            @endif
                                            <span class="badge bg-light">{{ $detail->user->name }}</span>
                                        </div>
                                        <div class="m-2">
                                            <p class="mt-4">{{ $detail->description }}</p>
                                            @if ($detail->file_path)
                                                <a href="{{ asset('storage/' . $detail->file_path) }}" target="_blank"
                                                    class="btn-sm btn-warning" style="text-decoration:none">مشاهده
                                                    فایل
                                                    پیوست شده</a> <!-- Display the file link -->
                                            @endif
                                            <small class="text-muted">
                                                {{ \Morilog\Jalali\Jalalian::fromCarbon($detail->created_at)->format('Y/m/d - ساعت H:i') }}
                                            </small>
                                        </div>
                                    </div>
                                    <hr>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="card shadow">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">درج توضیحات جدید</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('dis_requests.add_comment', $request->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="form-group mb-3">
                                    <textarea name="description" class="form-control" rows="4"
                                        placeholder="در صورت نیاز میتواین از این قسمت توضیحات به سفارش اضافه کنید" required></textarea>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="file">اضافه کردن فاِیل</label>
                                    <input type="file" name="file" class="form-control"
                                        accept="image/*, .pdf, .docx, .xlsx">
                                </div>


                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">اضافه کردن توضیحات</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>


            </div>






        </div>

        @endif
    </div>




    <script>
        // این تابع نمایش یا پنهان کردن فرم ویرایش متراژ را کنترل می‌کند
        function toggleEditForm() {
            var form = document.getElementById("editForm");
            // اگر فرم نمایش داده شده باشد، آن را پنهان می‌کنیم و برعکس
            if (form.style.display === "none" || form.style.display === "") {
                form.style.display = "block";
            } else {
                form.style.display = "none";
            }
        }
        
        // تابع validateInput برای اعتبارسنجی ورودی
        function validateInput(input) {
            input.value = input.value.replace(/[^0-9.]/g, ''); // فقط اعداد و نقطه مجاز هستند
            if ((input.value.match(/\./g) || []).length > 1) { // جلوگیری از داشتن بیشتر از یک نقطه
                input.value = input.value.substring(0, input.value.lastIndexOf('.')) + input.value.slice(input.value.lastIndexOf('.')).replace(/\./g, '');
            }
            if (input.value.startsWith('.')) { // جلوگیری از شروع ورودی با نقطه
                input.value = '0' + input.value;
            }
            if (parseFloat(input.value) < 0) { // جلوگیری از وارد کردن عدد منفی
                input.value = '0';
            }
        }
    </script>


    </div>
</x-app-layout>
