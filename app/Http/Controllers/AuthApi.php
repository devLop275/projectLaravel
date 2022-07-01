<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;

class AuthApi extends Controller
{
    public function response($user)
    {
      $token=$user->CreateToken( str()->random(40))->plainTextToken;
      
      return response()->json([
        'user'=>$user,
        'token'=>$token,
        'token_user'=>'Bearer'
      ]);
    }

    public function register(Request $request)
    {
      $request->validate([
        'name'=>'required|min:3',
        'email'=>'required|email|unique:users',
        'password'=>'required|min:4|confirmed'
      ]);

      $user=User::create([
        'name'=> ucwords($request->name),
        'email'=>$request->email,
        'password'=>bcrypt($request->password),
      ]);

      return $this->response($user);
    }

    public function login(Request $request)
    {
        $cred = $request->validate([
            'email'=>'required|email|exists:users',
            'password'=>'required|min:4',
        ]);

        if(!Auth::attempt($cred)){
            return response()->json([
                'message'=>'unauthorized'
            ], 401);
        }
        return $this-> response(Auth :: user());
    }

    public function logout()
    {
        Auth::user()->tokens()->delete();

        return response()->json([
            'message'=>'You have successfully logged out token was successfull deleted'
        ]);
    }
}
