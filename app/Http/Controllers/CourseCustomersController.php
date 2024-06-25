<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\course_customers;

class CourseCustomersController extends Controller
{
    public function index()
    {
        try {

            $course_customers = course_customers::all();

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
