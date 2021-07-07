<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use \Illuminate\Database\QueryException;
use App\Models\User;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:5',
        ]);
        if ($validator->fails()) {
            return response([
                'success' => false,
                'message' => $validator->errors()
            ], 400);
        }

        try {
            $user = User::create([
                'name' => $request['name'],
                'email' => $request['email'],
                'password' => bcrypt($request['password'])
            ]);
            $token = $user->createToken('login-token')->plainTextToken;
            return response([
                'success' => true,
                'token' => $token,
                'user' => $user,
                'message' => 'Registration Successful'
            ], 201);
        } catch (QueryException $exception) {
            return response([
                'success' => false,
                'message' => $exception->errorInfo
            ], 500);
        }
    }
}
