<x-app-layout>
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row m-1 pb-4 mb-3">
            <div class="col-xs-12">
                <div class="page-header breadcrumb-header">
                    <div class="row align-items-end">
                        <div class="col-lg-8">
                            <h3 class="lite-text">حواله های تکمیل شده</h3>
                        </div>
                        <div class="col-lg-4">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i></a></li>
                                <li class="breadcrumb-item active">حواله های تکمیل شده</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            @if(session()->has('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
        
            @if(session()->has('error')) 
                <div class="alert alert-danger">
                    {{ session('error') }}
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
        <!-- Table -->
        <div class="row m-2 mb-1 bg-light p-5 rounded">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ردیف</th>
                            <th>نام نماینده</th>
                            <th>نام شهر</th>
                            <th>شماره حواله</th>
                            <th>وضعیت درخواست</th>
                            <th>مشاهده جزئیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $n=0 ?>
                        @foreach ($uniqueRequests as $requestGroup)
                            @php $request = $requestGroup->first(); @endphp
                            <tr>
                                <td>{{ ++$n }}</td>
                                <td>{{ $request->user->name }}</td>
                                <td>{{ $request->user->userdata->city->name }}</td>
                                <td>
                                    @foreach ($requestGroup as $req)
                                        @foreach ($req->disRequestHavales as $havale)
                                            <a href="{{ route('dis_requests.havale_data', $havale->havale_number) }}" class="btn btn-info">{{ $havale->havale_number }}</a>
                                        @endforeach
                                    @endforeach
                                </td>
                                <td>تکمیل شده</td>
                                <td>
                                    <a href="{{ route('dis_requests.show', $request->id) }}" class="btn btn-info">مشاهده جزئیات</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
