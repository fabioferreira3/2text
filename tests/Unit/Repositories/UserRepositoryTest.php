<?php

use App\Events\UserCreated;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    $this->repository = new UserRepository();
});

describe('UserRepository', function () {
    it('gets user by email', function () {
        $user = User::factory()->create();
        $response = $this->repository->getUserByEmail($user->email);
        expect($response->id)->toBe($user->id);
    });

    it('creates an account', function () {
        $response = $this->repository->createAccount(['name' => 'Test Account']);
        expect($response->name)->toBe('Test Account');
        expect($response->status)->toBe('active');
        expect($response->settings['language'])->toBe('en');
    });

    it('registers a new user', function () {
        Event::fake();
        $response = $this->repository->registerNewUser([
            'name' => 'Fabio',
            'email' => 'experior@experior.ai',
            'password' => 'password123'
        ]);

        expect($response->name)->toBe('Fabio');
        expect($response->email)->toBe('experior@experior.ai');

        Event::assertDispatched(UserCreated::class);
    });

    it('registers a new user from provider', function ($name, $email) {
        Event::fake();
        $response = $this->repository->registerNewUserFromProvider([
            'name' => $name,
            'email' => $email,
            'password' => 'password123'
        ]);

        expect($response->name)->toBe($name);
        expect($response->email)->toBe($email);

        $this->assertDatabaseHas('accounts', [
            'name' => $name,
            'status' => 'active',
            'settings->language' => 'en'
        ]);

        Event::assertDispatched(UserCreated::class);
    })->with([
        ['name' => 'Fabio', 'email' => 'experior@experior.ai'],
        ['name' => 'Jonathan Flavours', 'email' => 'jon.flav@experior.ai'],
        ['name' => 'Meghan Stripe', 'email' => 'meganstripe@experior.ai']
    ]);
})->group('repositories');
