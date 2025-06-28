<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Complaint;
use Illuminate\Support\Facades\DB;
use App\Models\UserData;
use Carbon\Carbon;
use Morilog\Jalali\Jalalian; // Import Jalalian class
use App\Models\ComplaintComment;


class ComplaintController extends Controller
{
    public function index()
    {
        $user = auth()->user();
    
        if (in_array($user->role, ['admin', 'manager'])) {
            $complaints = Complaint::whereNotIn('status', ['تایید شده', 'رد شده'])->get();
        } elseif ($user->role == 'personnel') {
            $complaints = Complaint::where('personnel_id', $user->id)
                ->whereNotIn('status', ['تایید شده', 'رد شده'])
                ->get();
        } else {
            $complaints = Complaint::where('distributor_id', $user->id)->get();
        }
    
        // تبدیل created_at به تاریخ شمسی برای هر شکایت
        $complaints->map(function ($complaint) {
            $complaint->shamsi_created = Jalalian::fromDateTime($complaint->created_at)->format('Y/m/d ');
            return $complaint;
        });
    
        return view('complaints.index', compact('complaints'));
    }
    
    

    public function create()
    {
        $user = auth()->user();
        $products = null;
        $error = null;
    
        $distributors = [];
        if ($user->role == 'personnel') {
            
            $distributors = User::where( 'role', 'distributor')
                ->whereHas('userData', function ($q) use ($user) {
                    $q->where('personel_id', $user->id);
                })->get();
        }
    
        try {
            \DB::connection('sqlsrv')->getPdo();
            $products = DB::connection('sqlsrv')->table('vw_CRMproducts')->get();
        } catch (\Exception $e) {
            $error = 'ارتباط با دیتابیس برقرار نیست. لطفاً دقایقی دیگر بررسی کنید.';
        }
    
        return view('complaints.create', compact('products', 'distributors', 'error'));
    }
    
    

    public function havaleSearch(Request $request)
    {
        $tracking = $request->query('tracking_number');
    
        try {
            DB::connection('sqlsrv')->getPdo();
    
            $records = DB::connection('sqlsrv')
                ->table('vw_HavaleData')
                ->where('Havale', $tracking)
                ->select('product_code', 'product_name')
                ->get();
    
            if ($records->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No data found']);
            }
    
            return response()->json([
                'success' => true,
                'products' => $records
            ]);
    
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'DB connection error']);
        }
    }
    



   

    public function store(Request $request)
    {
        $user = auth()->user();
 
        $data = $request->validate([
            'product_name' => 'required|string',
            'product_code' => 'required|string',
            'model' => 'nullable|string',
            'tracking_number' => 'required',
            'customer_name' => 'required|string',
            'tel_number' => 'required|string',
            'address' => 'required|string',
            'complaint_text' => 'required|string',
            'complaint_type' => 'required|string',
            'attachments.*' => 'nullable|image|max:10000',
        ]);
    
        // تجزیه رشته محصول
        $parts = explode(' - ', $request->product_name);
    
        $data['degree']      = $parts[1] ?? null;
        $data['size']        = $parts[2] ?? null;
        $data['model']       = $parts[3] ?? null;
        $data['color']       = $parts[4] ?? null;
        $data['color_code']  = $parts[7] ?? null; // چون CL02 هشتمین بخشه
    
        // اضافه‌کردن کد محصول
        $data['product_code'] = $request->product_code;
    
        // ثبت هویت ثبت‌کننده
        if ($user->role == 'personnel') {
            $data['distributor_id'] = $request->distributor_id;
            $data['personnel_id'] = $user->id;
        } else {
            $data['distributor_id'] = $user->id;
            $data['personnel_id'] = $user->userData->personel_id;
        }
    
        // ذخیره فایل‌ها
        if ($request->hasFile('attachments')) {
            $files = [];
            foreach ($request->file('attachments') as $file) {
                $files[] = $file->store('complaint_attachments', 'public');
            }
            $data['attachments'] = json_encode($files);
        }
    
        Complaint::create($data);
    
        return redirect()->route('complaints.index')->with('success', 'شکایت با موفقیت ثبت شد.');
    }
    
    


    public function edit(Complaint $complaint)
{
    $issues = \App\Models\Issue::all(); // اگه مدل Issue داری
    return view('complaints.edit', compact('complaint', 'issues'));
}


    public function update(Request $request, Complaint $complaint)
{
    // اعتبارسنجی ساده
    $request->validate([
        'status' => 'required',
        'supervisor_comment' => 'nullable|string',
        'issues' => 'array',
        'issues.*' => 'integer'
    ]);

    // بروزرسانی وضعیت و توضیحات
    $complaint->update([
        'status' => $request->status,
        'supervisor_comment' => $request->supervisor_comment,
    ]);

    // اگر چک‌باکس‌های مشکلات تیک خورده باشند، ذخیره در Pivot
    if ($request->has('issues')) {
        $complaint->issues()->sync($request->issues);
    } else {
        // اگر هیچ مشکلی انتخاب نشده باشد، اتصال‌های قبلی پاک می‌شود
        $complaint->issues()->detach();
    }

    return redirect()->route('complaints.index')->with('success', 'وضعیت شکایت با موفقیت بروزرسانی شد.');
}


    public function destroy(Complaint $complaint)
    {
        if ($complaint->status == 'در انتظار') {
            $complaint->delete();
            return back()->with('success', 'شکایت حذف شد.');
        }
        return back()->with('error', 'امکان حذف شکایت پردازش شده وجود ندارد.');
    }

    public function completed()
    {


        $user = auth()->user();
    
        if (in_array($user->role, ['admin', 'manager'])) {
            $complaints = Complaint::whereIn('status', ['تایید شده', 'رد شده'])->get();
        } elseif ($user->role == 'personnel') {
            $complaints = Complaint::where('personnel_id', $user->id)
                ->whereIn('status', ['تایید شده', 'رد شده'])
                ->get();
        } else {
            $complaints = Complaint::where('distributor_id', $user->id)->get();
        }
    
        // تبدیل created_at به تاریخ شمسی برای هر شکایت
        $complaints->map(function ($complaint) {
            $complaint->shamsi_created = Jalalian::fromDateTime($complaint->created_at)->format('Y/m/d ');
            return $complaint;
        });
    
        return view('complaints.completed', compact('complaints'));



    }

    public function show(Complaint $complaint)
    {
        $complaint->load('issues', 'comments.user', 'distributor');
    
        // تبدیل created_at همه کامنت‌ها به شمسی و اضافه به فیلد جدید
        foreach ($complaint->comments as $comment) {
            $comment->shamsi_created_at = Jalalian::fromDateTime($comment->created_at)->format('Y/m/d H:i');
        }
    
        return view('complaints.show', compact('complaint'));
    }

public function addComment(Request $request, Complaint $complaint)
{
    $request->validate([
        'comment_text' => 'required|string',
    ]);

    $complaint->comments()->create([
        'user_id' => auth()->id(),
        'comment_text' => $request->comment_text,
    ]);

    return back()->with('success', 'کامنت ثبت شد.');
}

public function deleteComment($id)
{
    $comment = ComplaintComment::findOrFail($id);

    // فقط سرپرست خودش بتونه حذف کنه
    if ($comment->user_id == auth()->id()) {
        $comment->delete();
        return back()->with('success', 'کامنت حذف شد.');
    }

    return back()->with('error', 'دسترسی ندارید.');
}


}
