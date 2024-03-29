<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\User;
use Illuminate\Auth\Events\Logout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{   
    public function test()
    {
        return response()->json([
            "message" => "User found"
          ], 200);
    }
    public function index(Request $request)
    {
        // get all data from logged in user
        return response()->json([
            "message" => "User found",
            "data" => $request->user()
          ], 200);
    }

    public function register(Request $request)
    {
        // validate the request(user input) and save to validation variable
        $validation = $request->validate([
            'fullName' => 'required|max:60',
            'phoneNumber' => 'required|digits_between:11,13|unique:users',
            'birthdate' => 'required|before:today',
            'gender' => 'required',
            'username' => 'required|min:4|max:16|unique:users|alpha_dash',
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
            'password' => $validation['password']
        ]);

        // returns a response in JSON format containing user data
        return response()->json([
            "message" => "Registration succeeded",
            "data" => $data
        ], 201);
    }

    public function login(Request $request)
    {
        // validate the request
        $credentials = $request->validate([
            'username' => 'required|alpha_dash|min:4|max:16',
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

    public function updateUsername(Request $request)
    {
        $validation = $request->validate([
            'username' => 'required|min:4|max:16|unique:users|alpha_dash'
        ]);

        $user = User::findOrFail(Auth::user()->userId);
        $user->username = $validation['username'];
        $user->save();

        return response()->json([
            "message" => "Username updated successfully",
            "data" => $user
        ], 200);
    }

    public function changePassword(Request $request)
    {
        $validation = $request->validate([
            'currentPassword' => 'required',
            'newPassword' => 'required|min:6|max:32|confirmed'
        ]);

        $user = User::findOrFail(Auth::user()->userId);

        if(Hash::check($validation['currentPassword'], $user->password) ) {
            $user->update(['password' => bcrypt($validation['newPassword'])]);
            
            // clear api_token 
            $request->user()->forceFill([
                'api_token' => null
            ])->save();
            
            // return result
            return response()->json([
                "message" => "User Password has been changed",
                "token" => null 
            ]);
        } else {
            return response()->json([
                "message" => "Old password is incorrect"
            ], 401);
        }
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
        ], 200);
    }
    public function getAllUsers() {
        $users = User::all();

        return response()->json([
            "data" => $users
          ], 200);
    }

    public function setPrivacyCode(Request $request)
    {
        $validation = $request->validate([
            'privacyCode' => 'required|min:6|max:6|confirmed'
        ]);

        $user = User::findOrFail(Auth::user()->userId);

        if($user->privacyCode == null) {
            $user->privacyCode = $validation['privacyCode'];
            $user->save();
        } else {
            return response()->json([
                "message" => "Privacy Code already exists"
            ], 401);
        }

        return response()->json([
            "message" => "Successfully set Privacy Code",
            "data" => $user->privacyCode
        ], 200);
    }

    public function updatePrivacyCode(Request $request)
    {
        $validation = $request->validate([
            'currentPrivacyCode' => 'required|min:6|max:6',
            'newPrivacyCode' => 'required|min:6|max:6'
        ]);

        $user = User::findOrFail(Auth::user()->userId);

        if($validation['currentPrivacyCode'] == $user->privacyCode) {
            $user->update(['privacyCode' => $validation['newPrivacyCode']]);
            
            return response()->json([
                "message" => "Successfully Update Privacy Code",
                "data" => $user->privacyCode
            ], 200);
        } else {
            return response()->json([
                "message" => "Old Privacy Code is incorrect"
            ], 401);
        }
    }

    public function deletePrivacyCode(Request $request)
    {
        $validation = $request->validate([
            'currentPrivacyCode' => 'required'
        ]);

        $user = User::findOrFail(Auth::user()->userId);

        if($validation['currentPrivacyCode'] == $user->privacyCode) {
            $user->forceFill([
                'privacyCode' => null
            ])->save();
            
            return response()->json([
                "message" => "Successfully Delete Privacy Code",
                "data" => $user->privacyCode
            ], 200);
        } else {
            return response()->json([
                "message" => "Old Privacy Code is incorrect"
            ], 401);
        }
    }
}
