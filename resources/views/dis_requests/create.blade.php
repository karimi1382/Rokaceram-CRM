<x-app-layout>
    <style>
        body {
            background: linear-gradient(to right, #f3f4f6, #e2e8f0);
            min-height: 100vh;
        }

        .request-wrapper {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding-top: 10px;
            padding-bottom: 60px;
        }

        .request-container {
            background-color: #ffffff;
            border-radius: 10px;
            border: 1px solid #dee2e6;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
            padding: 30px;
            width: 100%;
            max-width: 1000px;
        }

        .request-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 30px;
            text-align: center;
            color: #444;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.15rem rgba(13, 110, 253, 0.25);
        }

        table th, table td {
            vertical-align: middle !important;
        }
    </style>

    <div class="container-fluid request-wrapper">
        <div class="request-container">

            <div class="request-title">
                ثبت درخواست 
            </div>

            <form action="{{ route('dis_requests.multiStore') }}" method="POST">
                @csrf

                <div class="table-responsive mb-4">
                    <table class="table table-striped table-hover table-bordered text-center align-middle">
                        <thead class="thead-light bg-light">
                            <tr>
                                <th>محصول</th>
                                <th>متراژ درخواستی</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td class="text-right">
                                        <strong>{{ $product->id_number }}</strong> - {{ $product->name }} - {{ $product->degree }} - {{ $product->size }} - {{ $product->model }} - {{ $product->color }} - {{ $product->color_code }}
                                        <input type="hidden" name="products[{{ $loop->index }}][id_number]" value="{{ $product->id }}">
                                    </td>
                                    <td style="max-width: 150px;">
                                        <input type="number" step="0.01" name="products[{{ $loop->index }}][request_size]" class="form-control text-center" required>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if(auth()->user()->role == 'personnel')
                    <div class="form-group mb-3">
                        <label for="distributor">انتخاب نماینده</label>
                        <select name="distributor_id" id="distributor" class="form-control">
                            @foreach ($distributors as $distributor)
                                <option value="{{ $distributor->id }}">{{ $distributor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <input type="hidden" name="request_type" value="{{ auth()->user()->role }}">
                <input type="hidden" name="status" value="Pending">

                <div class="row">
                    <div class="form-group mb-3 col-md-6">
                        <label for="tel_number">شماره تماس هماهنگی</label>
                        <input type="text" name="tel_number" id="tel_number" class="form-control" required>
                    </div>
                    <div class="form-group mb-3 col-md-6">
                        <label for="request_owner">نام و نام خانوادگی درخواست‌کننده</label>
                        <input type="text" name="request_owner" id="request_owner" class="form-control" required>
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label for="address">آدرس</label>
                    <textarea name="address" id="address" class="form-control" rows="3" required></textarea>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary px-5">ثبت همه درخواست‌ها</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
