<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\merchandise_customers;

class MerchandiseCustomersController extends Controller
{
    public function index()
    {
        try {
            $merchandise_customers = merchandise_customers::all();

            return response()->json([
                'merchandise_customers' => $merchandise_customers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'merchandise_customers not found'
            ], 500);
        }
    }
}
