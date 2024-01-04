<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    
    // Register user
    public function register(Request $request){
        // validate fields
        $attrs = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed'
        ]);

        // create user
        $user = User::create([
            'name' => $attrs['name'],
            'email' => $attrs['email'],
            'password' => bcrypt($attrs['password'])
        ]);

        // Return user & token in response
        return response()->json([
            'user' => $user,
            'token' => $user->createToken('secret')->plainTextToken,
        ], 200);
    }
    
    // Login user
    public function login(Request $request){
        // validate fields
        $attrs = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        // attempt login
        if(!Auth::attempt($attrs)){
            return response()->json([
                'message' => 'Invalid credentials.'
            ], 403);
        }

        // Return user & token in response
        return response()->json([
            'user' => auth()->user(),
            'token' => auth()->user()->createToken('secret')->plainTextToken,
        ], 200);
    }

    // Update user
    public function update(Request $request){
        $attrs = $request->validate([
            'name' => 'required|string',
        ]);

        $image = $this->saveImage($request->image, 'profiles');

        auth()->user()->update([
            'name' => $attrs['name'],
            'image' => $image
        ]);

        return response()->json([
            'message' => 'User updated.',
            'user' => auth()->user()
        ], 200);
    }

    // logout user
    public function logout(){
        auth()->user()->tokens()->delete();
        return response()->json([
            'message' => 'Logout success'
        ]);
    }

    // Get user
    public function show(){
        return response()->json([
            'user' => auth()->user()
        ]);
    }
}
