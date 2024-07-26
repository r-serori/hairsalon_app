<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Log;
use App\Http\Controllers\BaseController;
use App\Services\HasRole;

class GetKeyController extends BaseController
{
  protected $hasRole;

  public function __construct(HasRole $hasRole)
  {
    $this->hasRole = $hasRole;
  }

  public function getKey()
  {
    try {
      return $this->responseMan([
        'roleKey' => env('REACT_APP_ENCRYPTION_KEY'),
      ]);
    } catch (\Exception $e) {
      Log::error($e->getMessage());
      return $this->responseMan([
        'message' => 'エラーが発生しました。もう一度やり直してください！',
      ], 500);
    }
  }
}
