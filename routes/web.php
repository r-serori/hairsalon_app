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
use App\Http\Controllers\PageController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\UserPostController;
// imgタグのsrc属性に画像を表示するためのルーティング　startは出勤時の写真、endは退勤時の写真
// Route::get("/storage/attendance_times/images/startPhotos/{fileName}", [AttendanceTimesController::class, 'startPhotos'])->where('fileName', '.*');

// Route::get("/storage/attendance_times/images/endPhotos/{fileName}", [AttendanceTimesController::class, 'endPhotos'])->where('fileName', '.*');


Route::middleware('web')->group(function () {

    Route::middleware('auth:sanctum')->group(function () {

        //userの勤怠時間のコントローラー
        //勤怠時間の取得userのidと年月を受け取る。yearMonthが"無し"の場合は当月の勤怠時間を取得　Gate,OWNER
        Route::get('/attendance_times/selectedAttendanceTimes/{yearMonth}/{user_id}', [AttendanceTimesController::class, 'selectedAttendanceTime']);
        //クエリのuser:idを受け取り、そのユーザーの一番新しい勤怠時間を取得　Gate,ALL   
        Route::get('/firstAttendanceTimes/firstGet/{user_id}', [AttendanceTimesController::class, 'firstAttendanceTime']);
        //スタッフが前日に退勤を押さずに帰った時の編集依頼ボタンが押されたときの処理　Gate,ALL
        Route::post('/attendance_times/pleaseEditEndTime', [AttendanceTimesController::class, 'pleaseEditEndTime']);
        //勤怠時間削除　Gate,OWNER
        Route::post('/attendance_times/delete', [AttendanceTimesController::class, 'destroy']);
        //出勤ボタンが押された時の処理　Gate,ALL
        Route::post('/attendance_times/startTimeShot', [AttendanceTimesController::class, 'startTimeShot']);
        //退勤ボタンが押された時の処理　Gate,ALL
        Route::post('/attendance_times/endTimeShot', [AttendanceTimesController::class, 'endTimeShot']);
        //出勤時間の編集データの更新　Gate,OWNER
        Route::post('/attendance_times/updateStartTime', [AttendanceTimesController::class, 'updateStartTime']);
        //退勤時間の編集データの更新　Gate,OWNER
        Route::post('/attendance_times/updateEndTime', [AttendanceTimesController::class, 'updateEndTime']);


        //顧客情報のコントローラー post,update,destroy = Gate,OWNER,MANAGER  , index = Gate,ALL
        Route::get('/customers/all', [CustomersController::class, 'index']);
        Route::post('/customers/store', [CustomersController::class, 'store']);
        Route::post('/customers/update', [CustomersController::class, 'update']);
        Route::post('/customers/delete', [CustomersController::class, 'destroy']);

        //コース情報のコントローラー post,update,destroy = Gate,OWNER,MANAGER   , index = Gate,ALL
        Route::get('/courses/all', [CoursesController::class, 'index']);
        Route::post('/courses/store', [CoursesController::class, 'store']);
        Route::post('/courses/update', [CoursesController::class, 'update']);
        Route::post('/courses/delete', [CoursesController::class, 'destroy']);

        //コース情報のコントローラー post,update,destroy = Gate,OWNER,MANAGER   , index = Gate,ALL
        Route::get('/options/all', [OptionsController::class, 'index']);
        Route::post('/options/store', [OptionsController::class, 'store']);
        Route::post('/options/update', [OptionsController::class, 'update']);
        Route::post('/options/delete', [OptionsController::class, 'destroy']);

        //コース情報のコントローラー post,update,destroy = Gate,OWNER,MANAGER   , index = Gate,ALL
        Route::get('/merchandises/all', [MerchandisesController::class, 'index']);
        Route::post('/merchandises/store', [MerchandisesController::class, 'store']);
        Route::post('/merchandises/update', [MerchandisesController::class, 'update']);
        Route::post('/merchandises/delete', [MerchandisesController::class, 'destroy']);

        //コース情報のコントローラー post,update,destroy = Gate,OWNER,MANAGER   , index = Gate,ALL
        Route::get('/hairstyles/all', [HairstylesController::class, 'index']);
        Route::post('/hairstyles/store', [HairstylesController::class, 'store']);
        Route::post('/hairstyles/update', [HairstylesController::class, 'update']);
        Route::post('/hairstyles/delete', [HairstylesController::class, 'destroy']);

        //コース情報のコントローラー post,update,destroy = Gate,OWNER,MANAGER   , index = Gate,ALL
        Route::get('/schedules/customers/selectGetYear/{year}', [SchedulesController::class, 'selectGetYear']);
        Route::post('/schedules/customers/double', [SchedulesController::class, 'double']);
        Route::post('/schedules/customers/doubleUpdate', [SchedulesController::class, 'doubleUpdate']);
        Route::post('/schedules/customers/customerOnlyUpdate', [SchedulesController::class, 'customerOnlyUpdate']);
        Route::get('/schedules/all', [SchedulesController::class, 'index']);
        Route::post('/schedules/store', [SchedulesController::class, 'store']);

        Route::post('/schedules/update', [SchedulesController::class, 'update']);
        Route::post('/schedules/delete', [SchedulesController::class, 'destroy']);



        //コース情報のコントローラー post,update,destroy = Gate,OWNER,MANAGER   
        Route::get('/daily_sales/all', [DailySalesController::class, 'index']);
        Route::get('/daily_sales/selected/{year}', [DailySalesController::class, 'selectedDailySales']);
        Route::post('/daily_sales/store', [DailySalesController::class, 'store']);
        Route::post('/daily_sales/update', [DailySalesController::class, 'update']);
        Route::post('/daily_sales/delete', [DailySalesController::class, 'destroy']);

        //コース情報のコントローラー post,update,destroy = Gate,OWNER,MANAGER   
        Route::get('monthly_sales/all', [MonthlySalesController::class, 'index']);
        Route::get('monthly_sales/selected/{year}', [MonthlySalesController::class, 'selectedMonthlySales']);
        Route::post('/monthly_sales/store', [MonthlySalesController::class, 'store']);
        Route::post('/monthly_sales/update', [MonthlySalesController::class, 'update']);
        Route::post('/monthly_sales/delete', [MonthlySalesController::class, 'destroy']);

        //コース情報のコントローラー post,update,destroy = Gate,OWNER,MANAGER   
        Route::get('/yearly_sales/all', [YearlySalesController::class, 'index']);
        Route::post('/yearly_sales/store', [YearlySalesController::class, 'store']);
        Route::post('/yearly_sales/update', [YearlySalesController::class, 'update']);
        Route::post('/yearly_sales/delete', [YearlySalesController::class, 'destroy']);

        //コース情報のコントローラー post,update,destroy = Gate,OWNER,MANAGER   , index = Gate,ALL
        Route::get('/stocks/all', [StocksController::class, 'index']);
        Route::post('/stocks/store', [StocksController::class, 'store']);
        Route::post('/stocks/update', [StocksController::class, 'update']);
        Route::post('/stocks/delete', [StocksController::class, 'destroy']);

        //コース情報のコントローラー post,update,destroy = Gate,OWNER,MANAGER   , index = Gate,ALL;
        Route::get('/stock_categories/all', [StockCategoriesController::class, 'index']);
        Route::post('/stock_categories/store', [StockCategoriesController::class, 'store']);
        Route::post('/stock_categories/update', [StockCategoriesController::class, 'update']);
        Route::post('/stock_categories/delete', [StockCategoriesController::class, 'destroy']);
    });
});


// require __DIR__ . '/auth.php';
