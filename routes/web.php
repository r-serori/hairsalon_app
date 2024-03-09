<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceTimesController;
use App\Http\Controllers\MerchandiseController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\HairstyleController;
use App\Http\Controllers\OptionController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\StocksController;





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

Route::resource('attendance', AttendanceController::class)->middleware('auth');

Route::resource('attendance_times', AttendanceTimesController::class)->middleware('auth');
Route::get('/attendance_times/{attendance_id}', [AttendanceTimesController::class, 'index'])->name('attendance_times.index');
Route::get('/attendance_times/create/{attendance_id}', [AttendanceTimesController::class, 'create'])->name('attendance_times.create');
Route::post('/attendance_times/{attendance_id}', [AttendanceTimesController::class, 'store'])->name('attendance_times.store');





Route::resource('merchandise', MerchandiseController::class)->middleware('auth');
Route::resource('course', CourseController::class)->middleware('auth');
Route::resource('customers', CustomersController::class)->middleware('auth');
Route::resource('expense', ExpenseController::class)->middleware('auth');
Route::resource('hairstyle', HairstyleController::class)->middleware('auth');
Route::resource('option', OptionController::class)->middleware('auth');
Route::resource('schedule', ScheduleController::class)->middleware('auth');
Route::resource('sales', SalesController::class)->middleware('auth');
Route::resource('stocks', StocksController::class)->middleware('auth');




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
