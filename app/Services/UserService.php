<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class UserService
{
    /**
     * Register a new user with validated data.
     *
     * @param array{name:string,email:string,password:string} $data
     */
    public function registerUser(array $data): User
    {
        return User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => 'member',
        ]);
    }

    /**
     * Update user profile (name/email/password as provided) and return fresh user.
     *
     * @param array{name?:string,email?:string,current_password?:string,password?:string,password_confirmation?:string} $data
     */
    public function updateProfile(User $user, array $data): User
    {
        $updates = Arr::only($data, ['name', 'email']);

        if (! empty($data['password'])) {
            $updates['password'] = Hash::make($data['password']);
        }

        if ($updates) {
            $user->update($updates);
        }

        return $user->refresh();
    }

    /**
     * Permanently delete a user account (hard delete).
     */
    public function deleteUser(User $user): void
    {
        // Store the ID before deletion for verification
        $userId = $user->id;

        // Use direct database delete to bypass soft deletes entirely
        DB::table('users')->where('id', $userId)->delete();

        // Verify the record is actually gone
        $check = DB::table('users')->where('id', $userId)->first();
        if ($check !== null) {
            throw new \RuntimeException('Failed to delete user from database');
        }
    }
}
