<?php

use App\Enums\DocumentStatus;
use App\Enums\DocumentType;
use App\Http\Controllers\GoogleAuthController;
use App\Models\Document;
use App\Models\DocumentTask;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

beforeEach(function () {
    // Mock Socialite facade
    $this->socialiteUser = new \Laravel\Socialite\Two\User();
    $this->socialiteUser->name = 'John Doe';
    $this->socialiteUser->email = 'john.doe2@example.com';
    $this->socialiteUser->id = '1234';

    Socialite::shouldReceive('driver')
        ->with('google')
        ->andReturnSelf()
        ->shouldReceive('user')
        ->andReturn($this->socialiteUser);
});

describe('GoogleAuthControler controller', function () {
    it('successfully logs in a new user through Google', function () {
        $userRepositoryMock = $this->createMock(UserRepository::class);
        $userRepositoryMock->method('getUserByEmail')
            ->willReturn(null);
        $userRepositoryMock->expects($this->once())
            ->method('registerNewUserFromProvider')
            ->willReturn(new User(['id' => Str::uuid()]));

        Auth::shouldReceive('login')->once();

        $controller = new GoogleAuthController();
        $response = $controller->handleProviderCallback($userRepositoryMock);
        expect($response->getTargetUrl())->toBe(route('tools'));
    });

    it('successfully logs in an existing user through Google', function () {
        $user = User::factory()->create(['email' => 'john.doe@example.com']);
        $userRepositoryMock = $this->createMock(UserRepository::class);
        $userRepositoryMock->method('getUserByEmail')
            ->willReturn($user);

        Auth::shouldReceive('login')->once();

        $controller = new GoogleAuthController();
        $response = $controller->handleProviderCallback($userRepositoryMock);
        expect($response->getTargetUrl())->toBe(route('tools'));
    });

    it('handles exceptions during the login process', function () {

        $userRepositoryMock = Mockery::mock(UserRepository::class);
        $userRepositoryMock->shouldReceive('getUserByEmail')
            ->andThrow(new Exception('Error during authentication'));
        $controller = new GoogleAuthController();
        $response = $controller->handleProviderCallback($userRepositoryMock);
        expect($response->getSession()->all()['error'])->toBe('Unable to login with Google. Please try again.');
        expect($response->getTargetUrl())->toBe(route('login'));
    });
})->group('controllers');
