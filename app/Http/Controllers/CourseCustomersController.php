<?php

namespace App\Http\Controllers;


use App\Models\CourseCustomer;

class CourseCustomersController extends Controller
{
    public function index()
    {
        try {

            $course_customers = CourseCustomer::all();

            return response()->json([
                'course_customers' => $course_customers
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'コースカスタマーが見つかりません！'
            ], 500);
        }
    }
}
