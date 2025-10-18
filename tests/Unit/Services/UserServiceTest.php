<?php

use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->service = new UserService;
});

describe('UserService', function () {
    test('it creates a new user with valid data', function () {
        $userData = [
            'name'     => 'Test User',
            'email'    => 'test@example.com',
            'password' => 'password123',
            'role'     => 'member',
        ];

        $user = $this->service->createUser($userData);

        expect($user)->toBeInstanceOf(User::class)
            ->and($user->name)->toBe('Test User')
            ->and($user->email)->toBe('test@example.com')
            ->and($user->role)->toBe('member')
            ->and($user->exists)->toBeTrue();

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name'  => 'Test User',
            'role'  => 'member',
        ]);
    });

    test('it hashes password when creating user', function () {
        $userData = [
            'name'     => 'Test User',
            'email'    => 'test@example.com',
            'password' => 'plainpassword',
            'role'     => 'member',
        ];

        $user = $this->service->createUser($userData);

        expect($user->password)->not->toBe('plainpassword')
            ->and(Hash::check('plainpassword', $user->password))->toBeTrue();
    });

    test('it updates user profile information', function () {
        $user = User::factory()->create([
            'name'  => 'Original Name',
            'email' => 'original@example.com',
        ]);

        $updatedUser = $this->service->updateProfile($user, [
            'name' => 'Updated Name',
        ]);

        expect($updatedUser->name)->toBe('Updated Name')
            ->and($updatedUser->email)->toBe('original@example.com');

        $this->assertDatabaseHas('users', [
            'id'   => $user->id,
            'name' => 'Updated Name',
        ]);
    });

    test('it updates user password', function () {
        $user        = User::factory()->create();
        $oldPassword = $user->password;

        $updatedUser = $this->service->updateProfile($user, [
            'password' => 'newpassword123',
        ]);

        expect($updatedUser->password)->not->toBe($oldPassword)
            ->and(Hash::check('newpassword123', $updatedUser->password))->toBeTrue();
    });

    test('it finds user by id', function () {
        $user = User::factory()->create(['name' => 'Find Me']);

        $found = $this->service->findById($user->id);

        expect($found)->toBeInstanceOf(User::class)
            ->and($found->id)->toBe($user->id)
            ->and($found->name)->toBe('Find Me');
    });

    test('it returns null when user not found by id', function () {
        $found = $this->service->findById(99999);

        expect($found)->toBeNull();
    });

    test('it finds user by email', function () {
        $user = User::factory()->create(['email' => 'findme@example.com']);

        $found = $this->service->findByEmail('findme@example.com');

        expect($found)->toBeInstanceOf(User::class)
            ->and($found->email)->toBe('findme@example.com');
    });

    test('it returns null when user not found by email', function () {
        $found = $this->service->findByEmail('nonexistent@example.com');

        expect($found)->toBeNull();
    });

    test('it gets all users', function () {
        User::factory()->count(5)->create();

        $users = $this->service->getAllUsers();

        expect($users)->toHaveCount(5)
            ->and($users->first())->toBeInstanceOf(User::class);
    });

    test('it filters users by role', function () {
        User::factory()->count(3)->create(['role' => 'member']);
        User::factory()->count(2)->create(['role' => 'admin']);

        $members = $this->service->getUsersByRole('member');
        $admins  = $this->service->getUsersByRole('admin');

        expect($members)->toHaveCount(3)
            ->and($admins)->toHaveCount(2);
    });

    test('it throws exception when creating user with duplicate email', function () {
        $user = User::factory()->create(['email' => 'duplicate@example.com']);

        expect(fn () => $this->service->createUser([
            'name'     => 'Another User',
            'email'    => 'duplicate@example.com',
            'password' => 'password123',
            'role'     => 'member',
        ]))->toThrow(\Illuminate\Database\QueryException::class);
    });

    test('it does not update email to existing email', function () {
        $user1 = User::factory()->create(['email' => 'user1@example.com']);
        $user2 = User::factory()->create(['email' => 'user2@example.com']);

        expect(fn () => $this->service->updateProfile($user1, [
            'email' => 'user2@example.com',
        ]))->toThrow(\Illuminate\Database\QueryException::class);
    });
});
