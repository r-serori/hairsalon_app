<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\customer_attendances;

class CustomerAttendancesController extends Controller
{
  public function index()
  {
    try {
      $customer_attendances = customer_attendances::all();
      return response()->json([
        "resStatus" => "success",
        'customer_attendances' => $customer_attendances
      ]);
    } catch (\Exception $e) {
      return response()->json([
        "resStatus" => "error",
        "message" => "顧客の出席情報取得時にエラーが発生しました。"
      ], 500);
    }
  }
}
