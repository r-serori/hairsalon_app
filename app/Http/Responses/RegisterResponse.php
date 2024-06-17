<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract, Responsable
{
  protected $responseData;

  public function __construct($responseData)
  {
    $this->responseData = $responseData;
  }

  public function toResponse($request)
  {
    return response()->json([
      'resStatus' => 'success',
      'responseUser' => $this->responseData,
      'message' => 'ユーザー登録に成功しました！オーナー登録をしてください！',
    ]);
  }
}
