<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HairstyleCustomersController;
use App\Http\Controllers\OptionCustomersController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\HairstylesController;
use App\Http\Controllers\OptionsController;
use App\Http\Controllers\SchedulesController;
use App\Http\Controllers\DailySalesController;
use App\Http\Controllers\CoursesController;
use App\Http\Controllers\CourseCustomersController;
use App\Http\Controllers\AttendancesController;
use App\Http\Controllers\AttendanceTimesController;
use App\Http\Controllers\CustomerPricesController;
use App\Http\Controllers\ExpenseCategoriesController;
use App\Http\Controllers\ExpensesController;
use App\Http\Controllers\MerchandiseCustomersController;
use App\Http\Controllers\MerchandisesController;
use App\Http\Controllers\MonthlySalesController;
use App\Http\Controllers\StockCategoriesController;
use App\Http\Controllers\StocksController;
use App\Http\Controllers\YearlySalesController;









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

Route::resource('hairstyle_customers', HairstyleCustomersController::class);
Route::resource('option_customers', OptionCustomersController::class);
Route::resource('customers', CustomersController::class);
Route::resource('hairstyles', HairstylesController::class);
Route::resource('options', OptionsController::class);
Route::resource('schedules', SchedulesController::class);
Route::resource('daily_sales', DailySalesController::class);
Route::resource('courses', CoursesController::class);
Route::resource('course_customers', CourseCustomersController::class);
Route::resource('attendances', AttendancesController::class);
Route::resource('attendance_times', AttendanceTimesController::class)->parameters(['attendance_times' => 'id']);
//attendance_times/id　に置き換わる。　attendance_times/{attendance_time}　になると、idがattendance_timeになる。
Route::get('attendance_times/{attendance_id}/search', [AttendanceTimesController::class, 'search'])->name('attendance_times.search');











Route::resource('customer_prices', CustomerPricesController::class);
Route::resource('expense_categories', ExpenseCategoriesController::class);
Route::resource('expenses', ExpensesController::class);
Route::resource('merchandise_customers', MerchandiseCustomersController::class);
Route::resource('merchandises', MerchandisesController::class);
Route::resource('monthly_sales', MonthlySalesController::class);
Route::resource('stock_categories', StockCategoriesController::class);
Route::resource('stocks', StocksController::class);
Route::resource('yearly_sales', YearlySalesController::class);







Route::get('/', function () {
    return view('welcome');
});



Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
