<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JWTAuth;
use App\User;
use Tymon\JWTAuth\Exceptions\JWTExceptions;
use Illuminate\Support\MessageBag;


class ApiController extends Controller
{
    //
    public $loginAfterSignUp = true;


    public function register(Request $request){

        $validate_data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|max:10'
        ]);

        $validate_data['password'] = bcrypt($validate_data['password']);
        $user = User::create($validate_data);
        return response()->json([
            'success' => true,
            'id' => $user->id
        ], 200);
    }

    public function login(Request $request){
        $input = $request->only('name','password');
        $token = NULL;
        $myTTL = 1440; // minute of token expiration -- 1 day
        JWTAuth::factory()->setTTL($myTTL); // set token expiration
        if(!$token = JWTAuth::attempt($input)){
            return response()->json([
                'success' => false,
                'message' => "Invalid username or password"
            ],401);
        }
        return response()->json([
            'success' => true,
            'token' => $token
        ],200);
    }

    public function logout(Request $request){

        $this->validate($request, [
            'token' => 'required'
        ]);

        try{
            JWTAuth::invalidate($request->token);
            return response()->json([
                'success' => true,
                'message' => 'User logged out successfully'
            ]);

        }catch(JWTException $exception){
            return response()->json([
                'success' => false,
                'message' => 'Sorry, the user cannot be logged out'
            ],500);
        }
    }

  
}
