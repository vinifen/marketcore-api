<?php

namespace App\Services;

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\AuthException;

class AuthService
{
    public function register(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        $token = $user->createToken('UserToken')->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }

    public function login(array $credentials): array
    {
        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw new AuthException(["auth" => ['Invalid credentials provided.']], null, 401);
        }

        $token = $user->createToken('UserToken')->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }

    public static function validatePassword(User $user, string $password): void
    {
        if(! Hash::check($password, $user->password)) {
            throw new AuthException(["auth" => ['Password is incorrect.']], null, 403);
        }
    }

}
