<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\HairstylesController;
use App\Http\Controllers\OptionsController;
use App\Http\Controllers\SchedulesController;
use App\Http\Controllers\DailySalesController;
use App\Http\Controllers\CoursesController;
use App\Http\Controllers\CourseCustomersController;
use App\Http\Controllers\AttendancesController;
use App\Http\Controllers\AttendanceTimesController;
use App\Http\Controllers\MerchandiseCustomersController;
use App\Http\Controllers\MerchandisesController;
use App\Http\Controllers\MonthlySalesController;
use App\Http\Controllers\StockCategoriesController;
use App\Http\Controllers\StocksController;
use App\Http\Controllers\YearlySalesController;
use App\Http\Controllers\AttendanceAttendanceTimesController;
use App\Http\Controllers\CustomerSchedulesController;
use App\Http\Controllers\CustomerAttendancesController;
use App\Http\Controllers\HairstyleCustomersController;
use App\Http\Controllers\OptionCustomersController;





Route::resource('attendances', AttendancesController::class);
Route::post('attendances/{id}/update', [AttendancesController::class, 'update']);
Route::post('attendances/{id}/delete', [AttendancesController::class, 'destroy']);


Route::resource('attendance_times', AttendanceTimesController::class)->parameters(['attendance_times' => 'id']);
Route::post('attendance_times/{id}/update', [AttendanceTimesController::class, 'update']);
Route::post('attendance_times/{id}/delete', [AttendanceTimesController::class, 'destroy']);




Route::resource('customers', CustomersController::class);
Route::post('customers/{id}/update', [CustomersController::class, 'update']);
Route::post('customers/{id}/delete', [CustomersController::class, 'destroy']);



Route::resource('courses', CoursesController::class);
Route::post('courses/{id}/update', [CoursesController::class, 'update']);
Route::post('courses/{id}/delete', [CoursesController::class, 'destroy']);



Route::resource('options', OptionsController::class);
Route::post('options/{id}/update', [OptionsController::class, 'update']);
Route::post('options/{id}/delete', [OptionsController::class, 'destroy']);




Route::resource('merchandises', MerchandisesController::class);
Route::post('merchandises/{id}/update', [MerchandisesController::class, 'update']);
Route::post('merchandises/{id}/delete', [MerchandisesController::class, 'destroy']);



Route::resource('hairstyles', HairstylesController::class);
Route::post('hairstyles/{id}/update', [HairstylesController::class, 'update']);
Route::post('hairstyles/{id}/delete', [HairstylesController::class, 'destroy']);



Route::resource('schedules', SchedulesController::class);
Route::post('schedules/{id}/update', [SchedulesController::class, 'update']);
Route::post('schedules/{id}/delete', [SchedulesController::class, 'destroy']);



Route::resource('daily_sales', DailySalesController::class);
Route::post('daily_sales/{id}/update', [DailySalesController::class, 'update']);
Route::post('daily_sales/{id}/delete', [DailySalesController::class, 'destroy']);



Route::resource('monthly_sales', MonthlySalesController::class);
Route::post('monthly_sales/{id}/update', [MonthlySalesController::class, 'update']);
Route::post('monthly_sales/{id}/delete', [MonthlySalesController::class, 'destroy']);



Route::resource('yearly_sales', YearlySalesController::class);
Route::post('yearly_sales/{id}/update', [YearlySalesController::class, 'update']);
Route::post('yearly_sales/{id}/delete', [YearlySalesController::class, 'destroy']);



Route::resource('stocks', StocksController::class);
Route::post('stocks/{id}/update', [StocksController::class, 'update']);
Route::post('stocks/{id}/delete', [StocksController::class, 'destroy']);



Route::resource('stock_categories', StockCategoriesController::class);
Route::post('stock_categories/{id}/update', [StockCategoriesController::class, 'update']);
Route::post('stock_categories/{id}/delete', [StockCategoriesController::class, 'destroy']);



//中間テーブルのルーティング
Route::resource('customer_schedules', CustomerSchedulesController::class);
Route::resource('course_customers', CourseCustomersController::class);
Route::resource('customer_attendances', CustomerAttendancesController::class);
Route::resource('hairstyle_customers', HairstyleCustomersController::class);
Route::resource('merchandise_customers', MerchandiseCustomersController::class);
Route::resource('option_customers', OptionCustomersController::class);
Route::resource('attendance_attendanceTimes', AttendanceAttendanceTimesController::class);




Route::get('/', function () {
    return view('welcome');
});



Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



Route::get('/csrf-token', function () {
    return response()->json(['csrfToken' => csrf_token()]);
});




require __DIR__ . '/auth.php';
