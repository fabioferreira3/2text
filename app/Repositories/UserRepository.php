<?php

namespace App\Repositories;

use App\Models\Account;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    public static function getUserByEmail($email)
    {
        return User::where('email', $email)->first();
    }

    public static function registerNewUser(array $input): User
    {
        $account = Account::create([
            'name' => $input['name']
        ]);

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'account_id' => $account->id
        ]);

        return $user;
    }

    public static function registerNewUserFromProvider(array $input): User
    {
        $account = Account::create([
            'name' => $input['name'],
            'settings' => ['language' => 'en']
        ]);

        $user = User::create([
            'account_id' => $account->id,
            ...$input
        ]);

        return $user;
    }
}
