<?php

namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Enums\Roles;







class getKeyController extends Controller
{

  public function getKey()
  {
    try {
      $user = User::find(Auth::id());
      if ($user && $user->hasRole(Roles::$OWNER) || $user->hasRole(Roles::$MANAGER) || $user->hasRole(Roles::$STAFF)) {

        Log::info('REACT_APP_ENCRYPTION_KEY: ', [
          env('REACT_APP_ENCRYPTION_KEY'),
          env('REACT_APP_OWNER_ROLE'),
          env('REACT_APP_MANAGER_ROLE'),
        ]);
        return response()->json(['roleKey' => env('REACT_APP_ENCRYPTION_KEY')], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
      } else {
        return response()->json([
          'message' => '権限がありません。',
        ], 403, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
      }
    } catch (\Exception $e) {
      Log::error($e->getMessage());
      return response()->json([
        'message' => 'エラーが発生しました。もう一度やり直してください！',
      ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
    }
  }
}
