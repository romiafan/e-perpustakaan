<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends Controller
{
    public function __construct(private readonly UserService $users) {}

    public function show(Request $request)
    {
        if (! $request->user()) {
            return response()->json(['message' => 'Unauthenticated'], Response::HTTP_UNAUTHORIZED);
        }
        $user = $request->user();
        $stats = [
            'active_reservations' => $user->activeReservations()->count(),
            'total_borrowed'      => $user->reservations()->where('status', 'collected')->count(),
            'account_status'      => 'active',
        ];
        $recent = []; // Placeholder for activity stream
        return response()->json([
            'user'            => $user->only(['id', 'name', 'email', 'role']),
            'stats'           => $stats,
            'recent_activity' => $recent,
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $data = $request->all();
        $validator = Validator::make($data, [
            'name'              => ['sometimes', 'string', 'max:255'],
            'email'             => ['sometimes', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'current_password'  => ['sometimes', 'required_with:password', 'string'],
            'password'          => ['sometimes', 'string', 'min:8', 'confirmed'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors'  => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (isset($data['password']) && ! password_verify($data['current_password'] ?? '', $user->password)) {
            return response()->json([
                'message' => 'Validation error',
                'errors'  => ['current_password' => ['Current password incorrect']],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $updated = $this->users->updateProfile($user, $data);

        return response()->json([
            'user'    => $updated->only(['name', 'email']),
            'message' => 'Profile updated',
        ]);
    }

    public function destroy(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $data = $request->all();
        $validator = Validator::make($data, [
            'password' => ['required', 'string'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (! password_verify($data['password'], $user->password)) {
            return response()->json([
                'message' => 'Validation error',
                'errors'  => ['password' => ['Current password incorrect']],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->users->deleteUser($user);
        Auth::logout();

        return redirect()->route('home');
    }
}
