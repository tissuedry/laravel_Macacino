<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{
  public function login(Request $request)
  {
    $request->validate([
      'username' => 'required|string',
      'password' => 'required',
    ]);

    $user = User::where('username', $request->username)
      ->orWhere('email', $request->username)
      ->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
      throw ValidationException::withMessages([
        'username' => ['Kredensial yang diberikan salah.'],
      ]);
    }

    return response()->json([
      'token' => $user->createToken('mobile-app')->plainTextToken,
      'user' => new UserResource($user)
    ]);
  }

  public function register(Request $request)
  {
    $request->validate([
      'username' => 'required|string|max:255|unique:users,username',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string|min:8|confirmed',
    ]);

    $user = User::create([
      'name' => $request->username,
      'username' => $request->username,
      'email' => $request->email,
      'password' => Hash::make($request->password),
    ]);

    return response()->json([
      'token' => $user->createToken('mobile-app')->plainTextToken,
      'user' => new UserResource($user)
    ], 201);
  }

  public function logout(Request $request)
  {
    $request->user()->currentAccessToken()->delete();

    return response()->json(['message' => 'Berhasil logout']);
  }

  public function user(Request $request)
  {
    return new UserResource($request->user());
  }
}
