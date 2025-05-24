<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('AuthController', function (): void {

    it('allows register', function (): void {
        $payload = [
            'name' => 'John Doe',
            'email' => 'jdoe@example.com',
            'password' => 'cocacola1',
            'password_confirmation' => 'cocacola1',
        ];

        $response = $this->post('/api/register', $payload)
            ->assertStatus(201);

        expect($response->json('token'))->not->toBeEmpty()
            ->and(User::where('email', 'jdoe@example.com')->exists())->toBeTrue();
    });

    it('prevents registration with existing email', function () {
        $user = User::factory()->create([
            'email' => 'jdoe@example.com',
        ]);

        $payload = [
            'name' => 'John Doe',
            'email' => 'jdoe@example.com',
            'password' => 'cocacola1',
            'password_confirmation' => 'cocacola1',
        ];

        $this->post('/api/register', $payload)
            ->assertStatus(302);
    });
});
