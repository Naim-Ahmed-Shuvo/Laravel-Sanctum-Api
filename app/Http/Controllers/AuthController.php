<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //register
    public function register(Request $request)
    {
        $fields = $request->validate([
            'name'=> 'required|string|max:10|min:4',
            'email'=> 'required|email|max:100|string|unique:users,email',
            'password'=> 'required|confirmed|string'
        ]);

        $user = User::create([
            'name' =>  $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password'])
        ]);

        $token = $user->createToken('myapptoken')->plainTextToken;

        return response([
            "user" => $user,
            "token" => $token
        ],201);
    }

    //login
    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|email|string|',
            'password' => 'required|string'
        ]);

        //check user
        $user = User::where('email',$fields['email'])->first();

        //check password
        if(!$user || !Hash::check($fields['password'], $user->password)){
            return response([
                'message'=> 'Email or password does not exists'
            ],401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        return response([
            "user" => $user,
            "token" => $token
        ], 201);
    }
    //logout

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();

        return response([
            'message'=> 'Logged out'
        ],200);
    }
}
