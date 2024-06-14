<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\customer_users;

class CustomerAttendancesController extends Controller
{
  public function index()
  {
    try {
      $customer_users = customer_users::all();
      return response()->json([
        "resStatus" => "success",
        'customer_users' => $customer_users
      ]);
    } catch (\Exception $e) {
      return response()->json([
        "resStatus" => "error",
        "message" => "顧客の出席情報取得時にエラーが発生しました。"
      ], 500);
    }
  }
}
