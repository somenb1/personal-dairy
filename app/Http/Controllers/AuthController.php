<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use \Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;

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

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'success' => false,
                'message' => $validator->errors()
            ], 400);
        }

        try {
            $user = User::where('email', $request['email'])->first();

            if (!empty($user) && Hash::check($request['password'], $user->password)) {
                $token = $user->createToken('login-token')->plainTextToken;
                return response([
                    'success' => true,
                    'user' => $user,
                    'token' => $token,
                    'message' => 'Login Successful'
                ], 201);
            } else {
                return response([
                    'success' => false,
                    'message' => "You have entered wrong username or password."
                ], 401);
            }
        } catch (QueryException $exception) {
            return response([
                'success' => false,
                'message' => $exception->errorInfo
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        //print_r($request->bearerToken());
        print_r(auth()->user()->tokens()->delete());
        return response([
            'success' => true,
            'message' => 'Successfully Logged Out.'
        ], 200);
    }
}
