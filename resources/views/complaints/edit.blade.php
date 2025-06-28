<x-app-layout>
    <div class="container-fluid">
        <div class="row m-1 pb-4 mb-3">
            <div class="col-12 p-2">
                <div class="page-header breadcrumb-header">
                    <div class="row align-items-end">
                        <div class="col-lg-8">
                            <div class="page-header-title text-left-rtl">
                                <h3 class="lite-text">ویرایش وضعیت شکایت</h3>
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

        @if(session()->has('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session()->has('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row m-2 mb-1 bg-light p-5 rounded row justify-content-md-center">

            <!-- فرم تغییر وضعیت و دلایل -->
            <form action="{{ route('complaints.update', $complaint->id) }}" method="POST" class="col-md-8 mb-5">
                @csrf
                @method('PUT')

                <div class="form-group mb-3">
                    <label>وضعیت فعلی:</label>
                    <p>{{ $complaint->status }}</p>
                </div>

                <div class="form-group mb-3">
                    <label for="status">تغییر وضعیت</label>
                    <select name="status" class="form-control" required>
                        <option value="در انتظار" {{ $complaint->status == 'در انتظار' ? 'selected' : '' }}>در انتظار</option>
                        <option value="در حال بررسی" {{ $complaint->status == 'در حال بررسی' ? 'selected' : '' }}>در حال بررسی</option>
                        <option value="تایید شده" {{ $complaint->status == 'تایید شده' ? 'selected' : '' }}>تایید شده</option>
                        <option value="رد شده" {{ $complaint->status == 'رد شده' ? 'selected' : '' }}>رد شده</option>
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label>نوع مشکلات (چک‌باکس):</label><br>
                    @foreach ($issues as $issue)
                        <label class="mr-3">
                            <input type="checkbox" name="issues[]" value="{{ $issue->id }}"
                                {{ $complaint->issues && in_array($issue->id, $complaint->issues->pluck('id')->toArray()) ? 'checked' : '' }}>
                            {{ $issue->name }}
                        </label>
                    @endforeach
                </div>

                <button type="submit" class="btn btn-primary">ذخیره تغییر وضعیت و دلایل</button>
            </form>

            <!-- بخش نمایش و مدیریت کامنت‌ها -->
            <div class="col-md-8">
                <h5>کامنت‌های ثبت‌شده</h5>

                @if ($complaint->comments && $complaint->comments->count())
                    <ul class="list-group mb-3">
                        @foreach ($complaint->comments as $comment)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    {{ $comment->comment_text }}
                                    <br>
                                    <small class="text-muted">تاریخ: {{ $comment->created_at }}</small>
                                </div>
                                <form method="POST" action="{{ route('complaints.deleteComment', $comment->id) }}" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">حذف</button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p>کامنتی ثبت نشده است.</p>
                @endif

                <h5 class="mt-4">افزودن کامنت جدید</h5>
                <form method="POST" action="{{ route('complaints.addComment', $complaint->id) }}">
                    @csrf
                    <div class="form-group mb-3">
                        <textarea name="comment_text" rows="3" class="form-control" placeholder="متن کامنت..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-secondary">ثبت کامنت جدید</button>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
