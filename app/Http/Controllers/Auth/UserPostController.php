<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Models\User;

class UserPostController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }
}
