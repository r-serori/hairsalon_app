<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\course_customers;

class CourseCustomersController extends Controller
{
    public function index()
    {
        $course_customers = course_customers::all();

        return response()->json(['course_customers' => $course_customers]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'courses_id' => 'required',
            'customers_id' => 'required',
        ]);

        $course_customers = course_customers::create([
            'courses_id' => $validatedData['courses_id'],
            'customers_id' => $validatedData['customers_id'],
        ]);

        return
            response()->json(
                [],
                204
            );
    }

    public function destroy($id)
    {
        $course_customers = course_customers::find($id);
        if (!$course_customers) {
            return response()->json(['message' =>
            'course_customers not found'], 404);
        }

        try {
            $course_customers->delete();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete course_customers ', 'error' => $e->getMessage()], 500);
        }

        return response()->json(
            [],
            204
        );
    }
}
