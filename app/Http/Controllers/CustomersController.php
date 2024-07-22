<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\CustomerUser;
use App\Services\CustomerService;
use App\Services\CourseService;
use App\Services\GetImportantIdService;
use App\Services\HasRole;
use App\Services\HairstyleService;
use App\Services\MerchandiseService;
use App\Services\MiddleTableService;
use App\Services\OptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;


class CustomersController extends BaseController
{
    protected $getImportantIdService;
    protected $hasRole;
    protected $middleTableService;
    protected $customerService;
    protected $courseService;
    protected $optionService;
    protected $merchandiseService;
    protected $hairstyleService;

    public function __construct(
        GetImportantIdService $getImportantIdService,
        HasRole $hasRole,
        MiddleTableService $middleTableService,
        CustomerService $customerService,
        CourseService $courseService,
        OptionService $optionService,
        MerchandiseService $merchandiseService,
        HairstyleService $hairstyleService
    ) {
        $this->getImportantIdService = $getImportantIdService;
        $this->hasRole = $hasRole;
        $this->middleTableService = $middleTableService;
        $this->customerService = $customerService;
        $this->courseService = $courseService;
        $this->optionService = $optionService;
        $this->merchandiseService = $merchandiseService;
        $this->hairstyleService = $hairstyleService;
    }

    public function index()
    {
        try {
            $user = $this->hasRole->allAllow();
            // 顧客データを取得

            $ownerId  = $this->getImportantIdService->getOwnerId($user->id);

            $customers = $this->customerService->rememberCache($ownerId);

            $courses = $this->courseService->rememberCache($ownerId);

            $options = $this->optionService->rememberCache($ownerId);

            $merchandises = $this->merchandiseService->rememberCache($ownerId);

            $hairstyles = $this->hairstyleService->rememberCache($ownerId);

            $responseUsers = $this->getImportantIdService->getResponseUser($ownerId);

            $courseCustomer = $this->middleTableService->rememberCache($ownerId, 'course_customers');

            $optionCustomer = $this->middleTableService->rememberCache($ownerId, 'option_customers');

            $merchandiseCustomer = $this->middleTableService->rememberCache($ownerId, 'merchandise_customers');

            $hairstyleCustomer = $this->middleTableService->rememberCache($ownerId, 'hairstyle_customers');

            $userCustomer =
                CustomerUser::where('owner_id', $ownerId)->get();

            if ($customers->isEmpty()) {
                return $this->responseMan([
                    "message" => "初めまして！新規作成ボタンから顧客を作成しましょう！",
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
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->hasRole->managerAllow();

            $ownerId = $this->getImportantIdService->getOwnerId($user->id);

            $customer = $this->customerService->customerValidateAndCreateOrUpdate($request->all(), $ownerId, null, true);

            $this->customerService->forgetCache($ownerId);

            $this->middleTableService->flushCache($ownerId);

            $courseCustomer = $this->middleTableService->rememberCache($ownerId, 'course_customers');

            $optionCustomer = $this->middleTableService->rememberCache($ownerId, 'option_customers');

            $merchandiseCustomer = $this->middleTableService->rememberCache($ownerId, 'merchandise_customers');

            $hairstyleCustomer = $this->middleTableService->rememberCache($ownerId, 'hairstyle_customers');

            $userCustomer = CustomerUser::where('owner_id', $ownerId)->get();

            DB::commit();

            return
                $this->responseMan([
                    "customer" => $customer,
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

    public function update(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->hasRole->managerAllow();

            $ownerId = $this->getImportantIdService->getOwnerId($user->id);

            $customer = $this->customerService->customerValidateAndCreateOrUpdate($request->all(), $ownerId, $request->id, false);

            $this->customerService->forgetCache($ownerId);

            $this->middleTableService->flushCache($ownerId);

            $courseCustomer = $this->middleTableService->rememberCache($ownerId, 'course_customers');

            $optionCustomer = $this->middleTableService->rememberCache($ownerId, 'option_customers');

            $merchandiseCustomer = $this->middleTableService->rememberCache($ownerId, 'merchandise_customers');

            $hairstyleCustomer = $this->middleTableService->rememberCache($ownerId, 'hairstyle_customers');

            $userCustomer = CustomerUser::where('owner_id', $ownerId)->get();

            DB::commit();

            return
                $this->responseMan([
                    "customer" =>  $customer,
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

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->hasRole->ownerAllow();

            $ownerId = $this->getImportantIdService->getOwnerId($user->id);

            $this->customerService->forgetCache($ownerId);
            $this->courseService->forgetCache($ownerId);
            $this->optionService->forgetCache($ownerId);
            $this->merchandiseService->forgetCache($ownerId);
            $this->hairstyleService->forgetCache($ownerId);
            $this->middleTableService->flushCache($ownerId);

            $this->customerService->customerDelete($request->id);

            DB::commit();

            return $this->responseMan([
                "deleteId"  => $request->id, "message" => "顧客を削除しました！ "
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
