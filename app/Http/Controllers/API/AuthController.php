<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => "required|email",
            'password' => "required|string|min:6",
        ]);

        if ($validator->fails()) {
            $errorMessages = $validator->errors()->first();
            return ResponseHelper::jsonResponse('error', $errorMessages, null, 400);
        }

        $credentials = request(['email', 'password']);

        if (!$token = Auth::attempt($credentials)) {
            return ResponseHelper::jsonResponse('error', 'Invalid Credentials', null, 401);
        }
        $data = [
            'user' => auth()->user(),
            'token' => $token,
        ];
        return ResponseHelper::jsonResponse('success', 'Successfully logged in', $data);
    }

    function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => "required|string",
            'email' => "required|email|unique:users",
            'password' => "required|string|confirmed|min:6",
            'address' => "required|string",
        ]);

        if ($validator->fails()) {
            $errorMessages = $validator->errors()->first();
            return ResponseHelper::jsonResponse('error', $errorMessages, null, 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'address' => $request->address,
        ]);

        return ResponseHelper::jsonResponse('success', 'Successfully Registered', $user);
    }

    function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return ResponseHelper::jsonResponse('success', 'Successfully logged out');
    }
}
