<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\CustomerUser;
use Illuminate\Support\Facades\DB;
use App\Services\HasRole;
use App\Services\GetImportantIdService;
use App\Services\MiddleTableService;
use App\Services\CustomerService;
use App\Services\CourseService;
use App\Services\OptionService;
use App\Services\MerchandiseService;
use App\Services\HairstyleService;
use App\Services\ScheduleService;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SchedulesController extends BaseController
{
    protected $getImportantIdService;
    protected $hasRole;
    protected $middleTableService;
    protected $scheduleService;
    protected $customerService;
    protected $courseService;
    protected $optionService;
    protected $merchandiseService;
    protected $hairstyleService;

    public function __construct(
        GetImportantIdService $getImportantIdService,
        HasRole $hasRole,
        MiddleTableService $middleTableService,
        ScheduleService $scheduleService,
        CustomerService $customerService,
        CourseService $courseService,
        OptionService $optionService,
        MerchandiseService $merchandiseService,
        HairstyleService $hairstyleService
    ) {
        $this->getImportantIdService = $getImportantIdService;
        $this->hasRole = $hasRole;
        $this->middleTableService = $middleTableService;
        $this->scheduleService = $scheduleService;
        $this->customerService = $customerService;
        $this->courseService = $courseService;
        $this->optionService = $optionService;
        $this->merchandiseService = $merchandiseService;
        $this->hairstyleService = $hairstyleService;
    }

    //owner_idを受け取り、スケジュールを取得
    public function index()

    {
        try {
            $user = $this->hasRole->allAllow();

            $ownerId = $this->getImportantIdService->getOwnerId($user->id);

            $customers = $this->customerService->rememberCache($ownerId);

            $schedules = $this->scheduleService->rememberCache($ownerId);

            $responseUsers = $this->getImportantIdService->getResponseUser($ownerId);
            $courses = $this->courseService->rememberCache($ownerId);

            $options = $this->optionService->rememberCache($ownerId);

            $merchandises = $this->merchandiseService->rememberCache($ownerId);

            $hairstyles = $this->hairstyleService->rememberCache($ownerId);

            $courseCustomer = $this->middleTableService->rememberCache($ownerId, 'course_customers');
            $optionCustomer = $this->middleTableService->rememberCache($ownerId, 'option_customers');
            $merchandiseCustomer = $this->middleTableService->rememberCache($ownerId, 'merchandise_customers');
            $hairstyleCustomer = $this->middleTableService->rememberCache($ownerId, 'hairstyle_customers');

            $userCustomer = CustomerUser::where('owner_id', $ownerId)->get();


            if ($customers->isEmpty() && $schedules->isEmpty()) {
                return $this->responseMan([
                    "message" => "初めまして！顧客画面の新規作成ボタンから顧客を作成しましょう！",
                    'schedules' => $schedules,
                    'customers' => $customers,
                    'courses' => $courses,
                    'options' => $options,
                    'merchandises' => $merchandises,
                    'hairstyles' => $hairstyles,
                    'responseUsers' => $responseUsers,
                    'course_customers' => $courseCustomer,
                    'option_customers' => $optionCustomer,
                    'merchandise_customers' => $merchandiseCustomer,
                    'hairstyle_customers' => $hairstyleCustomer,
                    'customer_users' => $userCustomer,
                ]);
            } else if ($customers->isEmpty() && $schedules->isNotEmpty()) {
                return $this->responseMan([
                    "message" => "顧客画面の新規作成ボタンから顧客を作成しましょう！",
                    'schedules' => $schedules,
                    'customers' => $customers,
                    'courses' => $courses,
                    'options' => $options,
                    'merchandises' => $merchandises,
                    'hairstyles' => $hairstyles,
                    'responseUsers' => $responseUsers,
                    'course_customers' => $courseCustomer,
                    'option_customers' => $optionCustomer,
                    'merchandise_customers' => $merchandiseCustomer,
                    'hairstyle_customers' => $hairstyleCustomer,
                    'customer_users' => $userCustomer,
                ]);
            }

            if ($schedules->isEmpty()) {
                return $this->responseMan([
                    'message' => '初めまして！新規作成ボタンからスケジュールを作成しましょう！',
                    'schedules' => $schedules,
                    'customers' => $customers,
                    'courses' => $courses,
                    'options' => $options,
                    'merchandises' => $merchandises,
                    'hairstyles' => $hairstyles,
                    'responseUsers' => $responseUsers,
                    'course_customers' => $courseCustomer,
                    'option_customers' => $optionCustomer,
                    'merchandise_customers' => $merchandiseCustomer,
                    'hairstyle_customers' => $hairstyleCustomer,
                    'customer_users' => $userCustomer,
                ]);
            } else {
                return $this->responseMan([
                    'schedules' => $schedules,
                    'customers' => $customers,
                    'courses' => $courses,
                    'options' => $options,
                    'merchandises' => $merchandises,
                    'hairstyles' => $hairstyles,
                    'responseUsers' => $responseUsers,
                    'course_customers' => $courseCustomer,
                    'option_customers' => $optionCustomer,
                    'merchandise_customers' => $merchandiseCustomer,
                    'hairstyle_customers' => $hairstyleCustomer,
                    'customer_users' => $userCustomer,
                ]);
            }
        } catch (HttpException $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->responseMan(['message' => $e->getMessage()], $e->getStatusCode());
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->serverErrorResponseWoman();
        }
    }

    public function selectGetYear($year)
    {
        try {
            $user = $this->hasRole->allAllow();
            $ownerId = $this->getImportantIdService->getOwnerId($user->id);

            $decodeYear = urldecode($year);

            $customers = $this->customerService->rememberCache($ownerId);

            $selectSchedules = Schedule::whereDate('start_time', $decodeYear)
                ->where('owner_id', $ownerId)
                ->get();

            $courses = $this->courseService->rememberCache($ownerId);

            $options = $this->optionService->rememberCache($ownerId);

            $merchandises = $this->merchandiseService->rememberCache($ownerId);

            $hairstyles = $this->hairstyleService->rememberCache($ownerId);

            $courseCustomer = $this->middleTableService->rememberCache($ownerId, 'course_customers');
            $optionCustomer = $this->middleTableService->rememberCache($ownerId, 'option_customers');
            $merchandiseCustomer = $this->middleTableService->rememberCache($ownerId, 'merchandise_customers');
            $hairstyleCustomer = $this->middleTableService->rememberCache($ownerId, 'hairstyle_customers');
            $userCustomer = CustomerUser::where('owner_id', $ownerId)->get();

            $responseUsers = $this->getImportantIdService->getResponseUser($ownerId);

            if ($customers->isEmpty() && $selectSchedules->isEmpty()) {
                return $this->responseMan([
                    "message" => "初めまして！顧客画面の新規作成ボタンから顧客を作成しましょう！",
                    'schedules' => $selectSchedules,
                    'customers' => $customers,
                    'courses' => $courses,
                    'options' => $options,
                    'merchandises' => $merchandises,
                    'hairstyles' => $hairstyles,
                    'responseUsers' => $responseUsers,
                    'course_customers' => $courseCustomer,
                    'option_customers' => $optionCustomer,
                    'merchandise_customers' => $merchandiseCustomer,
                    'hairstyle_customers' => $hairstyleCustomer,
                    'customer_users' => $userCustomer,
                ]);
            } else if ($customers->isEmpty() && $selectSchedules->isNotEmpty()) {
                return $this->responseMan([
                    "message" => "顧客画面の新規作成ボタンから顧客を作成しましょう！",
                    'schedules' => $selectSchedules,
                    'customers' => $customers,
                    'courses' => $courses,
                    'options' => $options,
                    'merchandises' => $merchandises,
                    'hairstyles' => $hairstyles,
                    'responseUsers' => $responseUsers,
                    'course_customers' => $courseCustomer,
                    'option_customers' => $optionCustomer,
                    'merchandise_customers' => $merchandiseCustomer,
                    'hairstyle_customers' => $hairstyleCustomer,
                    'customer_users' => $userCustomer,
                ]);
            }


            return $this->responseMan([
                'schedules' => $selectSchedules,
                'customers' => $customers,
                'courses' => $courses,
                'options' => $options,
                'merchandises' => $merchandises,
                'hairstyles' => $hairstyles,
                'responseUsers' => $responseUsers,
                'course_customers' => $courseCustomer,
                'option_customers' => $optionCustomer,
                'merchandise_customers' => $merchandiseCustomer,
                'hairstyle_customers' => $hairstyleCustomer,
                'customer_users' => $userCustomer,
            ]);
        } catch (HttpException $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->responseMan(['message' => $e->getMessage()], $e->getStatusCode());
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->serverErrorResponseWoman();
        }
    }
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->hasRole->managerAllow();

            $ownerId = $this->getImportantIdService->getOwnerId($user->id);

            $schedule = $this->scheduleService->scheduleValidateAndCreateOrUpdate($request->all(), $ownerId, null, true, false);

            $this->scheduleService->forgetCache($ownerId);

            DB::commit();

            return $this->responseMan([
                'schedule' => $schedule,
            ], 200);
        } catch (HttpException $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->responseMan(['message' => $e->getMessage()], $e->getStatusCode());
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->serverErrorResponseWoman();
        }
    }

    public function update(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->hasRole->managerAllow();

            $ownerId = $this->getImportantIdService->getOwnerId($user->id);

            $schedule = $this->scheduleService->scheduleValidateAndCreateOrUpdate($request->all(), $ownerId, $request->id, false, false);

            $this->scheduleService->forgetCache($ownerId);

            DB::commit();

            return $this->responseMan([
                'schedule' => $schedule,
            ], 200);
        } catch (HttpException $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->responseMan(['message' => $e->getMessage()], $e->getStatusCode());
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->serverErrorResponseWoman();
        }
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->hasRole->ownerAllow();

            $ownerId = $this->getImportantIdService->getOwnerId($user->id);

            $this->scheduleService->scheduleDelete(intval($request->id));

            $this->scheduleService->forgetCache($ownerId);

            DB::commit();

            return $this->responseMan([
                "deleteId" => $request->id
            ], 200);
        } catch (HttpException $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->responseMan(['message' => $e->getMessage()], $e->getStatusCode());
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->serverErrorResponseWoman();
        }
    }

    public function double(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->hasRole->managerAllow();

            $ownerId = $this->getImportantIdService->getOwnerId($user->id);

            $data = $request->all();

            $customerData = [
                'customer_name' => $data['customer_name'],
                'phone_number' => $data['phone_number'],
                'remarks' => $data['remarks'],
                'course_id' => $data['course_id'],
                'option_id' => $data['option_id'],
                'merchandise_id' => $data['merchandise_id'],
                'hairstyle_id' => $data['hairstyle_id'],
                'user_id' => $data['user_id'],
            ];

            $customer = $this->customerService->customerValidateAndCreateOrUpdate($customerData, $ownerId, null, true);

            $scheduleData = [
                'title' => $data['title'] ?? null,
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'allDay' => $data['allDay'],
                'customer_id' => $customer->id,
            ];

            $schedule = $this->scheduleService->scheduleValidateAndCreateOrUpdate($scheduleData, $ownerId, null, true, true);

            $this->customerService->forgetCache($ownerId);

            $this->middleTableService->flushCache($ownerId);

            $courseCustomer = $this->middleTableService->rememberCache($ownerId, 'course_customers');

            $optionCustomer = $this->middleTableService->rememberCache($ownerId, 'option_customers');

            $merchandiseCustomer = $this->middleTableService->rememberCache($ownerId, 'merchandise_customers');

            $hairstyleCustomer = $this->middleTableService->rememberCache($ownerId, 'hairstyle_customers');

            $userCustomer = CustomerUser::where('owner_id', $ownerId)->get();

            $this->scheduleService->forgetCache($ownerId);

            DB::commit();

            return $this->responseMan([
                "customer" => $customer,
                "schedule" => $schedule,
                "course_customers" => $courseCustomer,
                "option_customers" => $optionCustomer,
                "merchandise_customers" => $merchandiseCustomer,
                "hairstyle_customers" => $hairstyleCustomer,
                "customer_users" => $userCustomer,
            ]);
        } catch (HttpException $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->responseMan(['message' => $e->getMessage()], $e->getStatusCode());
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->serverErrorResponseWoman();
        }
    }


    public function doubleUpdate(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->hasRole->managerAllow();
            $ownerId = $this->getImportantIdService->getOwnerId($user->id);

            $data = $request->all();

            $customerData = [
                'customer_name' => $data['customer_name'],
                'phone_number' => $data['phone_number'],
                'remarks' => $data['remarks'],
                'course_id' => $data['course_id'],
                'option_id' => $data['option_id'],
                'merchandise_id' => $data['merchandise_id'],
                'hairstyle_id' => $data['hairstyle_id'],
                'user_id' => $data['user_id'],
            ];

            $customer = $this->customerService->customerValidateAndCreateOrUpdate($customerData, $ownerId, intval($data['customer_id']), false);

            $scheduleData = [
                'title' => $data['title'] ?? null,
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'allDay' => $data['allDay'],
                'customer_id' =>
                $data['customer_id'],
            ];

            $schedule = $this->scheduleService->scheduleValidateAndCreateOrUpdate($scheduleData, $ownerId, intval($data['id']), false, true);

            $this->customerService->forgetCache($ownerId);

            $this->middleTableService->flushCache($ownerId);

            $courseCustomer = $this->middleTableService->rememberCache($ownerId, 'course_customers');

            $optionCustomer = $this->middleTableService->rememberCache($ownerId, 'option_customers');

            $merchandiseCustomer = $this->middleTableService->rememberCache($ownerId, 'merchandise_customers');

            $hairstyleCustomer = $this->middleTableService->rememberCache($ownerId, 'hairstyle_customers');

            $userCustomer = CustomerUser::where('owner_id', $ownerId)->get();

            $this->scheduleService->forgetCache($ownerId);

            DB::commit();

            return $this->responseMan([
                "customer" => $customer,
                "schedule" => $schedule,
                "course_customers" => $courseCustomer,
                "option_customers" => $optionCustomer,
                "merchandise_customers" => $merchandiseCustomer,
                "hairstyle_customers" => $hairstyleCustomer,
                "customer_users" => $userCustomer,
            ]);
        } catch (HttpException $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->responseMan(['message' => $e->getMessage()], $e->getStatusCode());
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->serverErrorResponseWoman();
        }
    }



    public function customerOnlyUpdate(Request $request)
    {
        DB::beginTransaction();
        try {
            Log::info('customerOnlyUpdate', ['request' => $request->all()]);
            $user = $this->hasRole->managerAllow();
            $ownerId = $this->getImportantIdService->getOwnerId($user->id);

            $data = $request->all();

            $customerId = intval($data['customer_id']);

            $customerData = [
                'customer_name' => $data['customer_name'],
                'phone_number' => $data['phone_number'],
                'remarks' => $data['remarks'],
                'course_id' => $data['course_id'],
                'option_id' => $data['option_id'],
                'merchandise_id' => $data['merchandise_id'],
                'hairstyle_id' => $data['hairstyle_id'],
                'user_id' => $data['user_id'],
            ];

            $customer = $this->customerService->customerValidateAndCreateOrUpdate($customerData, $ownerId, $customerId, false);
            Log::info('customer成功', ['customer' => $customer]);

            $scheduleData = [
                'title' => $data['title'] ?? null,
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'allDay' => $data['allDay'],
                'customer_id' => $customerId
            ];

            $schedule = $this->scheduleService->scheduleValidateAndCreateOrUpdate($scheduleData, $ownerId, null, true, true);
            Log::info('schedule成功', ['schedule' => $schedule]);

            $this->scheduleService->forgetCache($ownerId);

            $this->customerService->forgetCache($ownerId);

            $this->middleTableService->flushCache($ownerId);

            $courseCustomer = $this->middleTableService->rememberCache($ownerId, 'course_customers');

            $optionCustomer = $this->middleTableService->rememberCache($ownerId, 'option_customers');

            $merchandiseCustomer = $this->middleTableService->rememberCache($ownerId, 'merchandise_customers');

            $hairstyleCustomer = $this->middleTableService->rememberCache($ownerId, 'hairstyle_customers');

            $userCustomer = CustomerUser::where('owner_id', $ownerId)->get();

            DB::commit();

            return $this->responseMan([
                "customer" => $customer,
                "schedule" => $schedule,
                "course_customers" => $courseCustomer,
                "option_customers" => $optionCustomer,
                "merchandise_customers" => $merchandiseCustomer,
                "hairstyle_customers" => $hairstyleCustomer,
                "customer_users" => $userCustomer,
            ]);
        } catch (HttpException $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->responseMan(['message' => $e->getMessage()], $e->getStatusCode());
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->serverErrorResponseWoman();
        }
    }


    public function  customerCreateAndScheduleUpdate(Request $request)
    {
        DB::beginTransaction();
        try {
            Log::info('customerOnlyUpdate', ['request' => $request->all()]);
            $user = $this->hasRole->managerAllow();
            $ownerId = $this->getImportantIdService->getOwnerId($user->id);

            $data = $request->all();

            $customerData = [
                'customer_name' => $data['customer_name'],
                'phone_number' => $data['phone_number'],
                'remarks' => $data['remarks'],
                'course_id' => $data['course_id'],
                'option_id' => $data['option_id'],
                'merchandise_id' => $data['merchandise_id'],
                'hairstyle_id' => $data['hairstyle_id'],
                'user_id' => $data['user_id'],
            ];

            $customer = $this->customerService->customerValidateAndCreateOrUpdate($customerData, $ownerId, null, true);
            Log::info('customer成功', ['customer' => $customer]);

            $scheduleData = [
                'title' => $data['title'] ?? null,
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'allDay' => $data['allDay'],
                'customer_id' => $customer->id
            ];

            $schedule = $this->scheduleService->scheduleValidateAndCreateOrUpdate($scheduleData, $ownerId, intval($data['id']), false, true);
            Log::info('schedule成功', ['schedule' => $schedule]);
            $this->scheduleService->forgetCache($ownerId);

            $this->customerService->forgetCache($ownerId);

            $this->middleTableService->flushCache($ownerId);

            $courseCustomer = $this->middleTableService->rememberCache($ownerId, 'course_customers');

            $optionCustomer = $this->middleTableService->rememberCache($ownerId, 'option_customers');

            $merchandiseCustomer = $this->middleTableService->rememberCache($ownerId, 'merchandise_customers');

            $hairstyleCustomer = $this->middleTableService->rememberCache($ownerId, 'hairstyle_customers');

            $userCustomer = CustomerUser::where('owner_id', $ownerId)->get();

            DB::commit();

            return $this->responseMan([
                "customer" => $customer,
                "schedule" => $schedule,
                "course_customers" => $courseCustomer,
                "option_customers" => $optionCustomer,
                "merchandise_customers" => $merchandiseCustomer,
                "hairstyle_customers" => $hairstyleCustomer,
                "customer_users" => $userCustomer,
            ]);
        } catch (HttpException $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->responseMan(['message' => $e->getMessage()], $e->getStatusCode());
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->serverErrorResponseWoman();
        }
    }
}
