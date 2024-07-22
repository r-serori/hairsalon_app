<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Owner;
use Illuminate\Support\Facades\DB;
use App\Services\HasRole;
use App\Services\GetImportantIdService;
use App\Services\CourseService;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CoursesController extends BaseController
{
    protected $getImportantIdService;
    protected $courseService;
    protected $hasRole;

    public function __construct(GetImportantIdService $getImportantIdService, CourseService $courseService, HasRole $hasRole)
    {
        $this->getImportantIdService = $getImportantIdService;
        $this->courseService = $courseService;
        $this->hasRole = $hasRole;
    }

    public function index()
    {
        try {

            $user =  $this->hasRole->allAllow();

            $ownerId = $this->getImportantIdService->getOwnerId($user->id);

            $courses = $this->courseService->rememberCache($ownerId);

            if ($courses->isEmpty()) {
                return $this->responseMan([
                    "message" => "初めまして！新規作成ボタンからコースを作成しましょう！",
                    'courses' => []
                ]);
            } else {
                return $this->responseMan([
                    'courses' => $courses
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

            $course = $this->courseService->courseValidateAndCreateOrUpdate($request->all(), $ownerId, true);

            $this->courseService->forgetCache($ownerId);

            DB::commit();

            return $this->responseMan([
                'course' => $course
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

            $course = $this->courseService->courseValidateAndCreateOrUpdate($request->all(), $request->id, false);

            $ownerId = $this->getImportantIdService->getOwnerId($user->id);

            $this->courseService->forgetCache($ownerId);

            DB::commit();

            return $this->responseMan([
                'course' => $course,
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

            $this->courseService->courseDelete($request->id);

            $ownerId = Owner::where('user_id', $user->id)->value('id');

            $this->courseService->forgetCache($ownerId);

            DB::commit();

            return $this->responseMan([
                'deleteId' => $request->id,
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
