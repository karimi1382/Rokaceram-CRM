<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\PersonnelController;
use App\Http\Controllers\DistributorController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\TileModelController;
use App\Http\Controllers\LoginPageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DisRequestController;
use App\Http\Controllers\ProductUploadController;

use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;


require_once base_path('vendor/autoload.php');
use Morilog\Jalali\Jalalian;
use Carbon\Carbon;
use App\Models\HavaleData;



use Illuminate\Support\Facades\DB;


Route::get('/run-job', function () {
    dispatch(new App\Jobs\SyncHavaleDataJob());
    return 'Job dispatched successfully!';
});




Route::get('/test-Connection', function () {
    try {
        // داده‌ها را از دیتابیس سرور اول می‌خوانیم
        $results = DB::connection('sqlsrv')->select("SELECT TOP 5 * FROM  vw_HavaleData");

        

        return response()->json(['status' => 'Data Select successfully']);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});

Route::get('/test-Connection2', function () {
    dd(config('database.connections.sqlsrv'));
});


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|


*/




use Illuminate\Support\Facades\Artisan;

Route::get('/clear-cache', function () {
    Artisan::call('optimize:clear');
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    return "Cache cleared!";
});


Route::get('/test-jalali', function () {
    $jalaliDate = Jalalian::fromCarbon(Carbon::now())->format('Y/m/d');
});



Route::get('/export-products', function () {
    return Excel::download(new ProductsExport, 'products.xlsx');
})->name('products.export');



Route::get('/dis_requests/create', function () {
    return 'salam';
});



Route::get('/products/upload', function () {
    return 'salam';
});

Route::get('/products/temp', function () {
    return 'salam';
});



Route::get('/', function () {
    if (Auth::check()) {
        // User is authenticated, redirect based on role
        $user = Auth::user();

        switch ($user->role) {
            case 'admin':
                return redirect('/users');
            case 'personnel':
                return redirect('/personnel');
            case 'distributor':
                return redirect('/distributor');
            case 'manager':
                return redirect('/manager');
           
        }
    }

    // User is not authenticated, show login page
      return view('welcome');
});


Route::get('/ShowProduct', [ProductController::class, 'ShowProduct'])->name('showproduct');
Route::post('/ShowProduct/search', [ProductController::class, 'ShowProductsearch'])->name('ShowProductsearch');





Route::get('/not_Access', function () {
    return view('not_Access');
});


Route::middleware('auth')->group(function () {
    // Display profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    // Update profile (phone, city, and profile picture)
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Change password
    Route::get('/profile/password', [ProfileController::class, 'changePasswordshow'])->name('profile.change-password-show');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
    Route::resource('products', ProductController::class);
    Route::post('/products/search', [ProductController::class, 'search'])->name('products.search');
});

Route::middleware(['auth', 'role:admin'])->group(function () {

 

    Route::get('users', [UserController::class, 'index'])->name('users.index'); // List users
    Route::get('users/create', [UserController::class, 'create'])->name('users.create'); // Create user form
    Route::post('users', [UserController::class, 'store'])->name('users.store'); // Store new user
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit'); // Edit user form
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update'); // Update user
    Route::get('/admin/login-page/edit', [LoginPageController::class, 'edit'])->name('admin.login_page.edit');
    Route::post('/admin/login-page/update', [LoginPageController::class, 'update'])->name('admin.login_page.update');
    Route::resource('tile_models', TileModelController::class);
    Route::resource('sizes', SizeController::class);
    Route::resource('cities', CityController::class);
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');




Route::get('/products/upload', [ProductUploadController::class, 'uploadForm'])->name('products.upload');
Route::post('/products/upload', [ProductUploadController::class, 'processUpload']);
Route::get('/products/temp', [ProductUploadController::class, 'tempIndex'])->name('products.temp.index');
Route::post('/products/finalize', [ProductUploadController::class, 'finalize'])->name('products.finalize');



});

Route::middleware(['auth', 'role:personnel'])->group(function () {

    
Route::put('/dis-requests/{id}/update-status', [DisRequestController::class, 'updateStatus'])->name('dis_requests.update_status');
Route::post('/dis-requests/{id}/store-havale', [DisRequestController::class, 'storeHavaleNumber'])->name('dis_requests.store_havale');

Route::delete('/dis_requests/havale/{id}', [DisRequestController::class, 'deleteHavale'])->name('dis_requests.delete_havale');


    Route::get('/dis_requests/havale', [DisRequestController::class, 'havaleIndex'])->name('dis_requests.havale_index');
    Route::get('/dis_requests/havale_completed', [DisRequestController::class, 'completedHavale'])->name('dis_requests.havale_completed');
    
    Route::get('/dis_requests/havale_data/{id}', [DisRequestController::class, 'showHavaleData'])->name('dis_requests.havale_data');

    Route::get('/havale-index', [DisRequestController::class, 'havaleIndex'])->name('dis_requests.havale_index');

// Route to handle updating the status (as per your original form)
Route::put('dis_requests/{id}/update_status', [DisRequestController::class, 'updateStatus'])->name('dis_requests.update_status');

// Route to handle cancel request (will trigger cancelRequest method in controller)
Route::get('dis_requests/{id}/cancel', [DisRequestController::class, 'cancelRequest'])->name('dis_requests.cancel');

// Route for the index page (list of requests)
Route::get('dis_requests', [DisRequestController::class, 'index'])->name('dis_requests.index');
        Route::put('/requests/{id}/update-size', [DisRequestController::class, 'updateSize'])->name('requests.updateSize');

    // Route::put('/dis_requests/{id}/update_status', [DisRequestController::class, 'updateStatus'])->name('dis_requests.update_status');
    Route::get('/dis_requests/{id}', [DisRequestController::class, 'personnelshow'])->name('dis_requests.show');
    Route::get('/requests', [DisRequestController::class, 'personnelindex'])->name('requests.personeelindex');
    
    Route::get('/requests/{id}', [DisRequestController::class, 'show'])->name('requests.show');
    Route::get('/user/{id}/children', [UserController::class, 'showChildren'])->name('user.children');
    Route::get('/user/{userId}/havales', [UserController::class, 'showUserHavales'])->name('user.havales');


    Route::get('/personnel', [PersonnelController::class, 'index'])->name('personnel.index');
    Route::get('/completed-requests', [DisRequestController::class, 'personelcompletedRequests'])->name('dis_requests.personelcompleted');

});

Route::middleware(['auth', 'role:distributor'])->group(function () {
    Route::get('/completed-requests', [DisRequestController::class, 'completedRequests'])->name('dis_requests.completed');
    Route::get('/completed-requests/{id}', [DisRequestController::class, 'showCompletedRequest'])->name('dis_requests.completed.show');
    Route::get('/dis_requests', [DisRequestController::class, 'index'])->name('dis_requests.index');
    Route::get('/dis_requests/create', [DisRequestController::class, 'create'])->name('dis_requests.create');
    Route::delete('/dis_requests/{id}', [DisRequestController::class, 'destroy'])->name('dis_requests.delete');


    Route::post('/dis_requests', [DisRequestController::class, 'store'])->name('dis_requests.store');
    Route::get('/dis_requests/{id}', [DisRequestController::class, 'show'])->name('dis_requests.show');
    Route::post('/dis_requests/{id}/comment', [DisRequestController::class, 'addComment'])->name('dis_requests.add_comment');
    Route::get('/distributor', [DistributorController::class, 'index'])->name('distributor.index');
});

Route::middleware(['auth', 'role:manager'])->group(function () {

    Route::get('/admin/havale/all', [AdminController::class, 'allHavale'])->name('admin.havale_all');
    Route::get('/user/{userId}/havales', [UserController::class, 'showUserHavales'])->name('user.havales');

    // نمایش حواله‌های تکمیل‌شده برای مدیر
    Route::get('/admin/havale/completed', [AdminController::class, 'completedHavale'])->name('admin.havale_completed');
    Route::get('/dis_requests/havale_data/{id}', [DisRequestController::class, 'showHavaleData'])->name('dis_requests.havale_data');

    // نمایش جزئیات یک حواله خاص برای مدیر
    // Route::get('/admin/havale/{id}/details', action: [DisRequestController::class, 'showHavaleData'])->name('admin.havale_data');

    // Route for Personnel Page (View)
    Route::get('/admin/personnel', [UserController::class, 'showPersonnelPage'])->name('admin.personnel.index');
    // Route for updating target for Personnel user
    Route::post('/admin/personnel/{id}/update-target', [UserController::class, 'updateTargetPersonnel'])->name('personnel.updateTarget');
    // Route for Distributor Page (View)
    Route::get('/admin/distributor', [UserController::class, 'showDistributorPage'])->name('admin.distributor.index');
    // Route for updating target for Distributor user
    Route::post('/admin/distributor/{id}/update-target', [UserController::class, 'updateTargetDistributor'])->name('distributor.updateTarget');
    // Route for updating the target
    Route::get('/users-with-parents', [UserController::class, 'users_with_parents'])->name('users.with.parents');
    Route::get('/manager', [ManagerController::class, 'index'])->name('manager.index');

    Route::get('/approved-requests', [DisRequestController::class, 'showApprovedRequests'])->name('approved.requests');
    Route::put('/dis-request/{id}/update', [DisRequestController::class, 'updateApprovedRequests'])->name('dis_request.update');
    Route::get('/approved-requests-show', [DisRequestController::class, 'showApprovedRequestspage'])->name('show.approved.requests');

    Route::get('/personneltargetshow', [ManagerController::class, 'personneltargetshow'])->name('ManagerController.personneltargetshow');
    Route::get('/personneltargetshowdetile/{id}', [ManagerController::class, 'personneltargetshowdetile'])->name('ManagerController.personneltargetshowdetile');

    Route::delete('/personnel/{id}/delete-target/{targetId}', [UserController::class, 'deleteTarget'])->name('personnel.deleteTarget');
    Route::delete('/distributor/{id}/target/{targetId}', [UserController::class, 'deleteTargetDistributor'])
    ->name('distributor.deleteTarget');

    
////////////////////// گزارشات

Route::get('/report/reserved-products', [ManagerController::class, 'reservedProductsReport'])->name('report.reserved-products');
Route::get('/manager/agents-performance', [App\Http\Controllers\ManagerController::class, 'agentsPerformance'])->name('report.agents.performance');


});


Route::middleware(['auth', 'role:personnel|distributor'])->group(function () {
    // Your routes here

    Route::get('/dis_requests/{id}', [DisRequestController::class, 'show'])->name('dis_requests.show');
    Route::get('/dis_requests/create', [DisRequestController::class, 'create'])->name('dis_requests.create');
    Route::post('/dis_requests', [DisRequestController::class, 'store'])->name('dis_requests.store');
    Route::get('/personelcompleted-requests', [DisRequestController::class, 'personelcompletedRequests'])->name('dis_requests.personelcompleted');
    Route::get('/completed-requests', [DisRequestController::class, 'completedRequests'])->name('dis_requests.completed');



});






require __DIR__.'/auth.php';
