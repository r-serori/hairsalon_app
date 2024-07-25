<?php

namespace App\Http\Controllers;

use App\Models\CustomerUser;

class CustomerUsersController extends Controller
{
  public function index()
  {
    try {
      $customer_users = CustomerUser::all();
      return response()->json([
        'customer_users' => $customer_users
      ]);
    } catch (\Exception $e) {
      return response()->json([
        "message" => "顧客の出席情報取得時にエラーが発生しました！"
      ], 500);
    }
  }
}
