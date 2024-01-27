<?php

namespace App\Repositories;

use App\Events\UserCreated;
use App\Models\Account;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    public function getUserByEmail($email)
    {
        return User::where('email', $email)->first();
    }

    public function createAccount(array $params)
    {
        return Account::create([
            'name' => $params['name'],
            'status' => 'active',
            'settings' => ['language' => 'en']
        ]);
    }

    public function registerNewUser(array $input): User
    {
        $account = $this->createAccount($input);

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'account_id' => $account->id
        ]);

        event(new UserCreated($user));

        return $user;
    }

    public function registerNewUserFromProvider(array $input): User
    {
        $account = $this->createAccount($input);

        $user = User::create([
            'account_id' => $account->id,
            ...$input
        ]);

        event(new UserCreated($user));

        return $user;
    }
}
