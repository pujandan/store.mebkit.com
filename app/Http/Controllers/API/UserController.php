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
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            $credentials = request(['email', 'password']);
            if (!Auth::attempt($credentials)) {
                throw new \Exception(trans('message.login_failed'), 401);
            }

            $user = User::where('email', $request->email)->first();
            if (!Hash::check($request->password, $user->password, [])) {
                throw new \Exception(trans('message.login_failed'), 401);
            }

            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return ResponseFormatter::success(
                [
                    'access_token' => $tokenResult,
                    'token_type' => 'Bearer',
                    'user' => $user,
                ],
                trans('message.login_success')
            );
        } catch (\Exception $e) {
            return ResponseFormatter::exception($e);
        }
    }

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
                trans('message.register_success')
            );
        } catch (\Exception $e) {
            return ResponseFormatter::exception($e);
        }
    }


    public function show(Request $request)
    {
        try {
            return ResponseFormatter::success($request->user(), trans('message.show_success'));
        } catch (\Exception $e) {
            return ResponseFormatter::exception($e);
        }
    }

    public function update(Request $request)
    {
        try {

            $rules = [
                'name' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:255',
                'username' => 'nullable|string|max:255',
            ];

            // for changed email
            if ($request->email != Auth::user()->email) {
                $rules['email'] = 'nullable|string|max:255|email|unique:users,email';
            } else {
                $rules['email'] = 'nullable|string|max:255|email';
            }

            // for changed username
            if ($request->username != Auth::user()->username) {
                $rules['username'] = 'nullable|string|max:255|unique:users,username';
            } else {
                $rules['username'] = 'nullable|string|max:255';
            }

            // for changed password
            if ($request->password) {
                $rules['password'] = ['required', 'string', new Password];
            }

            $request->validate($rules);
            $user = User::find(Auth::user()->id);

            $user->update([
                "name" => $request->name ?? $user->name,
                "email" => $request->email ?? $user->email,
                "phone" => $request->phone ?? $user->phone,
                "username" => $request->username ?? $user->username,
                "password" => ($request->password) ? Hash::make($request->password) : Auth::user()->password,
            ]);

            return ResponseFormatter::success($user, trans('message.updated'));
        } catch (\Exception $e) {
            return ResponseFormatter::exception($e);
        }
    }


    public function logout(Request $request)
    {
        try {
            $token = $request->user()->currentAccessToken()->delete();
            return ResponseFormatter::success($token, trans("message.logout_success"));
        } catch (\Exception $e) {
            return ResponseFormatter::exception($e);
        }
    }
}
