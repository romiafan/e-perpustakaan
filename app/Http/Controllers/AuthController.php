<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function __construct(private readonly UserService $users) {}

    public function login(Request $request)
    {
        $data = $request->only(['email', 'password', 'remember']);
        $validator = Validator::make($data, [
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (! Auth::attempt(['email' => $data['email'], 'password' => $data['password']], (bool) ($data['remember'] ?? false))) {
            return response()->json([
                'message' => 'Invalid credentials',
                'errors'  => ['email' => ['These credentials do not match our records.']],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = $request->user()->only(['id', 'name', 'email', 'role']);

        return response()->json([
            'user'     => $user,
            'redirect' => '/dashboard',
        ]);
    }

    public function register(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = $this->users->registerUser($data);

        // Remove ID from response for API contract compliance
        $userData = $user->only(['name', 'email', 'role']);

        return response()->json([
            'user'    => $userData,
            'message' => 'User registered successfully',
        ], 201);
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return response()->json([
            'message'  => 'Logged out',
            'redirect' => '/login',
        ]);
    }
}
