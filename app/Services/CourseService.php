<?php

namespace App\Services;

use App\Models\Course;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use \Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CourseService
{

    public function __construct()
    {
    }

    private function createCacheKey(int $ownerId): string
    {
        return 'owner_' . $ownerId . 'courses';
    }

    public function rememberCache(int $ownerId): Collection
    {
        try {
            $coursesCacheKey =  $this->createCacheKey($ownerId);

            $expirationInSeconds = 60 * 60 * 24; // 1日（秒数で指定）

            $courses = Cache::remember($coursesCacheKey, $expirationInSeconds, function () use ($ownerId) {
                return  Course::where('owner_id', $ownerId)->orderBy('course_name', 'asc')->get();
            });

            return $courses;
        } catch (\Exception $e) {
            abort(500, 'エラーが発生しました');
        }
    }

    public function forgetCache(int $ownerId): void
    {
        try {
            $coursesCacheKey = $this->createCacheKey($ownerId);

            Cache::forget($coursesCacheKey);
        } catch (\Exception $e) {
            abort(500, 'エラーが発生しました');
        }
    }

    private function courseStore(array $data, int $ownerId): Course
    {
        try {
            $course = new Course();
            $course->course_name = $data['course_name'];
            $course->price = $data['price'];
            $course->owner_id = $ownerId;
            $course->save();

            return $course;
        } catch (\Exception $e) {
            abort(500, 'エラーが発生しました');
        }
    }

    private function courseUpdate(array $data, int $courseId): Course
    {
        try {
            $course = Course::find($courseId);
            $course->course_name = $data['course_name'];
            $course->price = $data['price'];
            $course->save();

            return $course;
        } catch (\Exception $e) {
            abort(500, 'エラーが発生しました');
        }
    }

    public function courseValidateAndCreateOrUpdate(array $data, int $ownerIdOrCourseId, bool $createOrUpdate): Course|JsonResponse
    // request->all()を受け取り、バリデーションを行い、createOrUpdateがtrueの場合はowner_idを受け取り、新規作成、falseの場合はcourse_idを受け取り、更新を行う
    {
        try {
            $validator = Validator::make($data, [
                'course_name' => 'required|string|max:100',
                'price' => 'required|integer|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => '入力内容を確認してください！'], 400);
            }

            $validatedData = $validator->validate();

            if ($createOrUpdate) {
                $ownerId = $ownerIdOrCourseId;
                return $this->courseStore($validatedData, $ownerId);
            } else {
                $courseId = $ownerIdOrCourseId;
                return $this->courseUpdate($validatedData, $courseId);
            }
        } catch (\Exception $e) {
            abort(500, 'エラーが発生しました');
        }
    }

    public  function courseDelete(int $courseId): void
    {
        try {
            $course = Course::find($courseId);

            $course->delete();
        } catch (\Exception $e) {
            abort(500, 'エラーが発生しました');
        }
    }
}
