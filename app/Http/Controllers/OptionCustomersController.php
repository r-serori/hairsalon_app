<?php

namespace App\Http\Controllers;

use App\Models\OptionCustomer;

class OptionCustomersController extends Controller
{

    public function index()
    {
        try {
            $option_customers = OptionCustomer::all();

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
