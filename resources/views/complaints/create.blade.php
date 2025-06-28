<x-app-layout>
    <div class="container-fluid">
        <div class="row m-1 pb-4 mb-3">
            <div class="col-12 p-2">
                <div class="page-header breadcrumb-header">
                    <div class="row align-items-end">
                        <div class="col-lg-8">
                            <div class="page-header-title text-left-rtl">
                                <h3 class="lite-text">ثبت شکایت جدید</h3>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i></a></li>
                                <li class="breadcrumb-item active">داشبورد</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(isset($error))
            <div class="alert alert-danger">{{ $error }}</div>
        @else
            @if(session()->has('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
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

            <div class="row m-2 mb-1 bg-light p-5 rounded row justify-content-md-center">
                <form id="complaint-form" action="{{ route('complaints.store') }}" method="POST" enctype="multipart/form-data" class="col-md-8">
                    @csrf

                    <div class="form-group mb-3">
                        <label for="tracking_number">شماره حواله</label>
                        <input type="text" name="tracking_number" id="tracking_number" class="form-control" required>
                        <button type="button" class="btn btn-info mt-2" id="loadHavale">مشاهده محصول</button>
                    </div>

                    <div class="form-group mb-3" id="productInfoBox" style="display:none">
                        <label>محصول شناسایی شده:</label>
                        <p id="productInfo" class="text-success font-weight-bold"></p>
                    </div>
                    <input type="hidden" name="product_code" id="product_code">
                    <input type="hidden" name="product_name" id="product_name">
                    <input type="hidden" name="degree" id="degree">
                    <input type="hidden" name="size" id="size">
                    <input type="hidden" name="model" id="model">
                    <input type="hidden" name="color" id="color">
                    <input type="hidden" name="color_code" id="color_code">

                    @if(auth()->user()->role == 'personnel')
                        <div class="form-group mb-3">
                            <label for="distributor_id">انتخاب نماینده</label>
                            <select name="distributor_id" class="form-control" required>
                                <option value="">انتخاب کنید</option>
                                @foreach ($distributors as $distributor)
                                    <option value="{{ $distributor->id }}">{{ $distributor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="form-group mb-3">
                        <label for="customer_name">نام مشتری</label>
                        <input type="text" name="customer_name" class="form-control" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="tel_number">شماره تماس</label>
                        <input type="text" name="tel_number" class="form-control" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="address">آدرس</label>
                        <textarea name="address" rows="3" class="form-control" required></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label for="complaint_text">شرح شکایت</label>
                        <textarea name="complaint_text" rows="4" class="form-control" required></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label for="complaint_type">نوع شکایت</label>
                        <select name="complaint_type" class="form-control" required>
                            <option value="">انتخاب کنید</option>
                            <option value="عادی">عادی</option>
                            <option value="بحرانی">بحرانی</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label>آپلود تصاویر</label>
                        <div id="image-inputs">
                            <input type="file" name="attachments[]" class="form-control mb-2" accept="image/*">
                        </div>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="addImageInput()">افزودن تصویر دیگر</button>
                    </div>

                    <button type="submit" class="btn btn-success" id="submitButton" disabled>ثبت شکایت</button>
                </form>
            </div>

            <script>
                function addImageInput() {
                    let container = document.getElementById('image-inputs');
                    let input = document.createElement('input');
                    input.type = 'file';
                    input.name = 'attachments[]';
                    input.className = 'form-control mb-2';
                    input.accept = 'image/*';
                    container.appendChild(input);
                }
            
                document.getElementById('loadHavale').addEventListener('click', function () {
                    const trackingNumber = document.getElementById('tracking_number').value;
            
                    fetch(`/complaints/havale-search?tracking_number=${trackingNumber}`)
                        .then(res => res.json())
                        .then(data => {
                            const box = document.getElementById('productInfoBox');
                            const info = document.getElementById('productInfo');
            
                            if (data.success) {
                                box.style.display = 'block';
                                info.innerHTML = '';
            
                                data.products.forEach((product, index) => {
                                    const id = `radio_${index}`;
                                    info.innerHTML += `
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="selected_product" id="${id}" value="${product.product_code}||${product.product_name}">
                                            <label class="form-check-label" for="${id}">
                                                ${product.product_name} - ${product.product_code}
                                            </label>
                                        </div>
                                    `;
                                });
            
                                document.getElementById('submitButton').disabled = true;
            
                                // فعال‌سازی فرم فقط بعد انتخاب محصول
                                document.querySelectorAll('input[name="selected_product"]').forEach(el => {
                                    el.addEventListener('change', function () {
                                        const [code, name] = this.value.split('||');
                                        document.getElementById('product_code').value = code;
                                        document.getElementById('product_name').value = name;
                                        document.getElementById('model').value = code;
            
                                        document.getElementById('submitButton').disabled = false;
                                    });
                                });
            
                            } else {
                                alert('هیچ اطلاعاتی برای این شماره حواله یافت نشد.');
                                box.style.display = 'none';
                                document.getElementById('submitButton').disabled = true;
                            }
                        })
                        .catch(() => {
                            alert('خطا در برقراری ارتباط.');
                            document.getElementById('submitButton').disabled = true;
                        });
                });
            </script>
            
        @endif
    </div>
</x-app-layout>
