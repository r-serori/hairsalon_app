<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\hairstyle_customers;

class HairstyleCustomersController extends Controller
{
    public function index()
    {
        try {
            $hairstyle_customers = hairstyle_customers::all();

            return response()->json([
                "resStatus" => "success",
                'hairstyle_customers' => $hairstyle_customers
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' => 'hairstyle_customers not found'
            ], 500);
        }
    }
}
