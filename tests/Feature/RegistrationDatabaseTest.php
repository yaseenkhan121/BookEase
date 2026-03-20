<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Provider;

class RegistrationDatabaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_register_and_data_is_stored()
    {
        $response = $this->post('/register', [
            'name' => 'Test Customer',
            'email' => 'customer.test@bookease.com',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
            'role' => 'customer',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertSessionMissing('error');
        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'customer.test@bookease.com',
            'role' => 'customer',
            'status' => 'active',
        ]);
    }

    public function test_provider_can_register_and_data_is_stored()
    {
        $response = $this->post('/register', [
            'name' => 'Test Provider',
            'email' => 'provider.test@bookease.com',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
            'role' => 'provider',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertSessionMissing('error');
        $response->assertRedirect(route('login'));

        $this->assertDatabaseHas('users', [
            'email' => 'provider.test@bookease.com',
            'role' => 'provider',
            'status' => 'pending',
        ]);

        $user = User::where('email', 'provider.test@bookease.com')->first();
        
        $this->assertDatabaseHas('providers', [
            'user_id' => $user->id,
            'owner_name' => 'Test Provider',
            'status' => 'pending',
            'setup_completed' => false,
        ]);
    }

    public function test_registration_validation_fails_on_weak_password()
    {
        $response = $this->post('/register', [
            'name' => 'Test',
            'email' => 'weak@bookease.com',
            'password' => 'weak',
            'password_confirmation' => 'weak',
            'role' => 'customer',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertDatabaseMissing('users', ['email' => 'weak@bookease.com']);
    }
}
