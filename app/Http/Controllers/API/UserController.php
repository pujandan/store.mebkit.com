<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Rules\Password;

class UserController extends Controller
{

    public function register(Request $request)
    {
        try {

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|max:255|email|unique:users,email',
                'phone' => 'nullable|string|max:255',
                'username' => 'required|string|max:255|unique:users,username',
                'password' => ['required', 'string', new Password],
            ]);

            $user = User::create([
                "name" => $request->name,
                "email" => $request->email,
                "phone" => $request->phone,
                "username" => $request->username,
                "password" => Hash::make($request->password),
            ]);

            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return ResponseFormatter::success(
                [
                    'access_token' => $tokenResult,
                    'token_type' => 'Bearer',
                    'user' => $user,
                ],
                'User registered'
            );
        } catch (\Exception $error) {

            return ResponseFormatter::error([
                'message' => 'something went wrong',
                'error' => $error->getMessage(),
            ], 'Authentication failed', 422);
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            $credentials = request(['email', 'password']);
            if (!Auth::attempt($credentials)) {
                throw new \Exception('Authentication Failed');
            }

            $user = User::where('email', $request->email)->first();

            if (!Hash::check($request->password, $user->password, [])) {
                throw new \Exception('Invalid Credentials');
            }

            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return ResponseFormatter::success(
                [
                    'access_token' => $tokenResult,
                    'token_type' => 'Bearer',
                    'user' => $user,
                ],
                'Authenticated'
            );
        } catch (\Exception $error) {
            return ResponseFormatter::error([
                'message' => 'something went wrong',
                'error' => $error->getMessage(),
            ], 'Authentication failed', 422);
        }
    }

    public function fetch(Request $request)
    {
        return ResponseFormatter::success($request->user(), "data profile berhasil di ambil");
    }

    public function update(Request $request)
    {
        try {

            $request->validate([
                'name' => 'nullable|string|max:255',
                'email' => 'nullable|string|max:255|email', // unique:users,email
                'phone' => 'nullable|string|max:255',
                'username' => 'nullable|string|max:255', // unique:users,username
                'password' => ['nullable', 'string', new Password],
            ]);

            $user = User::find(Auth::user()->id);
            $user->update([
                "name" => $request->name ?? $user->name,
                "email" => $request->email ?? $user->email,
                "phone" => $request->phone ?? $user->phone,
                "username" => $request->username ?? $user->username,
                // "password" => $request->password ?? $user->password,
            ]);

            return ResponseFormatter::success($user, "user berhasil di update");
        } catch (\Exception $error) {
            return ResponseFormatter::error([
                'message' => 'something went wrong',
                'error' => $error->getMessage(),
            ], 'Update failed', 422);
        }


        return ResponseFormatter::success($request->user(), "data profile berhasil di ambil");
    }


    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken()->delete();
        return ResponseFormatter::success($token, "Token Revoked");
    }
}
