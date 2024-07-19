<?php

namespace App\Services;

use App\Models\Course;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use \Illuminate\Database\Eloquent\Collection;

class CourseService
{

    public function __construct()
    {
    }
    public static function rememberCache(int $ownerId): Collection
    {
        $coursesCacheKey = 'owner_' . $ownerId . 'courses';

        $expirationInSeconds = 60 * 60 * 24; // 1日（秒数で指定）

        $courses = Cache::remember($coursesCacheKey, $expirationInSeconds, function () use ($ownerId) {
            return  Course::where('owner_id', $ownerId)->get();
        });

        return $courses;
    }

    public  function forgetCache(int $ownerId): void
    {
        $coursesCacheKey = 'owner_' . $ownerId . 'courses';

        Cache::forget($coursesCacheKey);
    }

    public  function CourseValidate(array $data): array
    {
        $validator = Validator::make($data, [
            'course_name' => 'required|string|max:100',
            'price' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            abort(400, '入力内容を確認してください！');
        }
        return $validator->validate();
    }

    public  function CourseDelete(int $courseId): void
    {
        $course = Course::find($courseId);

        if (empty($course)) {
            abort(404, 'コースが見つかりません');
        }

        $course->delete();
    }
}
