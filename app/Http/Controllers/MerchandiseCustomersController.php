<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MerchandiseCustomer;

class MerchandiseCustomersController extends Controller
{
    public function index()
    {
        try {
            $merchandise_customers = MerchandiseCustomer::all();

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
