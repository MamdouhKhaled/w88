<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {

        if (Auth::attempt($request->validated())){
            $user = Auth::user()->createToken($request->validated('email'));

            return UserResource::make(Auth::user())->additional([
                'token' => $user->plainTextToken,
            ]);
        }

        return response()->json([
            'message' => 'Unauthorized'
        ], 401);

    }
}
