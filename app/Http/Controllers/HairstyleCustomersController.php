<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HairstyleCustomer;

class HairstyleCustomersController extends Controller
{
    public function index()
    {
        try {
            $hairstyle_customers = HairstyleCustomer::all();

            return response()->json([
                'hairstyle_customers' => $hairstyle_customers
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'hairstyle_customers not found'
            ], 500);
        }
    }
}
