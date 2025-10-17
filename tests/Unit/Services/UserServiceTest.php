<?php

use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->service = new UserService();
});

describe('UserService', function () {
    describe('registerUser', function () {
        test('it creates a new user with valid data', function () {
            $userData = [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password123',
            ];

            $user = $this->service->registerUser($userData);

            expect($user)->toBeInstanceOf(User::class)
                ->and($user->name)->toBe('Test User')
                ->and($user->email)->toBe('test@example.com')
                ->and($user->role)->toBe('member')
                ->and($user->exists)->toBeTrue();

            $this->assertDatabaseHas('users', [
                'email' => 'test@example.com',
                'name' => 'Test User',
                'role' => 'member',
            ]);
        });

        test('it hashes password when creating user', function () {
            $userData = [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'plainpassword',
            ];

            $user = $this->service->registerUser($userData);

            expect($user->password)->not->toBe('plainpassword')
                ->and(Hash::check('plainpassword', $user->password))->toBeTrue();
        });

        test('it sets default role to member', function () {
            $userData = [
                'name' => 'Jane Doe',
                'email' => 'jane@example.com',
                'password' => 'password123',
            ];

            $user = $this->service->registerUser($userData);

            expect($user->role)->toBe('member');
        });

        test('it throws exception when creating user with duplicate email', function () {
            User::factory()->create(['email' => 'duplicate@example.com']);

            expect(fn() => $this->service->registerUser([
                'name' => 'Another User',
                'email' => 'duplicate@example.com',
                'password' => 'password123',
            ]))->toThrow(\Illuminate\Database\QueryException::class);
        });
    });

    describe('updateProfile', function () {
        test('it updates user profile information', function () {
            $user = User::factory()->create([
                'name' => 'Original Name',
                'email' => 'original@example.com',
            ]);

            $updatedUser = $this->service->updateProfile($user, [
                'name' => 'Updated Name',
            ]);

            expect($updatedUser->name)->toBe('Updated Name')
                ->and($updatedUser->email)->toBe('original@example.com');

            $this->assertDatabaseHas('users', [
                'id' => $user->id,
                'name' => 'Updated Name',
            ]);
        });

        test('it updates user password', function () {
            $user = User::factory()->create();
            $oldPassword = $user->password;

            $updatedUser = $this->service->updateProfile($user, [
                'password' => 'newpassword123',
            ]);

            expect($updatedUser->password)->not->toBe($oldPassword)
                ->and(Hash::check('newpassword123', $updatedUser->password))->toBeTrue();
        });

        test('it updates multiple fields at once', function () {
            $user = User::factory()->create([
                'name' => 'Old Name',
                'email' => 'old@example.com',
            ]);

            $updated = $this->service->updateProfile($user, [
                'name' => 'New Name',
                'email' => 'new@example.com',
            ]);

            expect($updated->name)->toBe('New Name')
                ->and($updated->email)->toBe('new@example.com');
        });

        test('it does not update password when not provided', function () {
            $user = User::factory()->create();
            $originalPassword = $user->password;

            $updated = $this->service->updateProfile($user, ['name' => 'New Name']);

            expect($updated->password)->toBe($originalPassword);
        });

        test('it returns fresh user instance', function () {
            $user = User::factory()->create();
            $originalName = $user->name;

            $updated = $this->service->updateProfile($user, ['name' => 'New Name']);

            expect($updated)->toBeInstanceOf(User::class)
                ->and($updated->name)->toBe('New Name')
                ->and($updated->name)->not->toBe($originalName);
        });

        test('it does not update email to existing email', function () {
            $user1 = User::factory()->create(['email' => 'user1@example.com']);
            $user2 = User::factory()->create(['email' => 'user2@example.com']);

            expect(fn() => $this->service->updateProfile($user1, [
                'email' => 'user2@example.com',
            ]))->toThrow(\Illuminate\Database\QueryException::class);
        });
    });

    describe('deleteUser', function () {
        test('it permanently deletes user from database', function () {
            $user = User::factory()->create();
            $userId = $user->id;

            $this->service->deleteUser($user);

            $this->assertDatabaseMissing('users', ['id' => $userId]);
        });

        test('it verifies user is actually deleted', function () {
            $user = User::factory()->create();
            $userId = $user->id;

            $this->service->deleteUser($user);

            $check = DB::table('users')->where('id', $userId)->first();
            expect($check)->toBeNull();
        });
    });
});
