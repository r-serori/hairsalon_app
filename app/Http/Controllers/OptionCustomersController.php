<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\option_customers;

class OptionCustomersController extends Controller
{

    public function index()
    {
        try {
            $option_customers = option_customers::all();

            return response()->json([
                'option_customers' => $option_customers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'option_customers not found'
            ], 500);
        }
    }
}
