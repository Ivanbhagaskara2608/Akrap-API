<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // get all data from logged in user
        return response()->json([
            "message" => "User found",
            "data" => $request->user()
          ]);
    }

    public function register(Request $request)
    {
        // validate the request(user input) and save to validation variable
        $validation = $request->validate([
            'fullName' => 'required|max:60',
            'phoneNumber' => 'required|digits_between:11,13|unique:users',
            'birthdate' => 'required|before:today',
            'gender' => 'required',
            'username' => 'required|min:4|max:16|unique:users',
            'job' => 'required',
            'password' => 'required|min:8|max:32|confirmed'
        ]);

        // password encryption 
        $validation['password'] = bcrypt($validation['password']);

        // store user data to database
        $data = User::create([
            'fullName' => $validation['fullName'],
            'phoneNumber' => $validation['phoneNumber'],
            'birthdate' => $validation['birthdate'],
            'gender' => $validation['gender'],
            'username' => $validation['username'],
            'job' => $validation['job'],
            'password' => $validation['password']
        ]);

        // returns a response in JSON format containing user data
        return response()->json([
            "message" => "Registration succeeded",
            "data" => $data
        ]);
    }

    public function login(Request $request)
    {
        // validate the request
        $credentials = $request->validate([
            'phoneNumber' => 'required|digits_between:11,13',
            'password' => 'required|min:8|max:32',
        ]);

        // authenticate user login
        if(Auth::attempt($credentials)) {
            $user = Auth::user();
            // token generated from md5 time + email user
            $token = md5(time()) . '.' . md5($request->email);
            // fill up the field of api_token from generated token md5
            $user->forceFill([
                'api_token' => $token,
            ])->save();
            // returns a response in jSON containing token
            return response()->json([
                "message" => "Login Succeeded",
                "token" => $token
            ]);
        };

         // return for unathenticated user
         return response()->json([
            "message" => "The provided credentials doesn't match our record",
            "data" => []
        ], 401);
    }

    public function logout(Request $request)
    {
        // clear api_token 
        $request->user()->forceFill([
            'api_token' => null
        ])->save();
        
        // return result
        return response()->json([
            'message'=>'Logout successful',
            "token" => null 
        ]);
    }

}
