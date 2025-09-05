<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_register_with_valid_data()
    {
        $userData = [
            'name' => 'John Doe',
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'user' => [
                        'uuid',
                        'name',
                        'username',
                        'email'
                    ],
                    'access_token',
                    'token_type',
                    'expires_in'
                ]);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'username' => 'johndoe',
            'email' => 'john@example.com'
        ]);
    }

    /** @test */
    public function user_cannot_register_with_invalid_data()
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => '',
            'email' => 'invalid-email',
            'password' => '123',
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    /** @test */
    public function user_cannot_register_with_existing_email()
    {
        User::factory()->create(['email' => 'john@example.com']);

        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('password123')
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'john@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'user' => [
                        'uuid',
                        'name',
                        'username',
                        'email'
                    ],
                    'access_token',
                    'token_type',
                    'expires_in'
                ]);

        // Check that user session was created
        $this->assertDatabaseHas('user_sessions', [
            'user_id' => $user->id
        ]);

        // Check that last login info was updated
        $user->refresh();
        $this->assertNotNull($user->last_login_at);
        $this->assertNotNull($user->last_login_ip);
    }

    /** @test */
    public function user_cannot_login_with_invalid_credentials()
    {
        User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('password123')
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'john@example.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(401)
                ->assertJson([
                    'error' => 'Wrong Email or Password'
                ]);
    }

    /** @test */
    public function user_cannot_login_when_already_logged_in()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('password123')
        ]);

        // Create an active session
        UserSession::create([
            'user_id' => $user->id,
            'token' => 'existing-token',
            'expires_at' => Carbon::now()->addHours(2),
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Browser'
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'john@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(409)
                ->assertJson([
                    'success' => false,
                    'message' => 'User is already logged in on another device',
                    'error' => 'ALREADY_LOGGED_IN'
                ]);
    }

    /** @test */
    public function user_can_logout()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/auth/logout');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Successfully logged out'
                ]);
    }

    /** @test */
    public function user_can_get_profile()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/auth/me');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'user' => [
                        'uuid',
                        'name',
                        'username',
                        'email'
                    ]
                ]);
    }

    /** @test */
    public function user_can_refresh_token()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/auth/refresh');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'access_token',
                    'token_type',
                    'expires_in'
                ]);
    }

    /** @test */
    public function user_can_check_login_status()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/auth/is-logged-in');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'is_logged_in' => true
                ]);
    }

    /** @test */
    public function guest_is_not_logged_in()
    {
        $response = $this->getJson('/api/auth/is-logged-in');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'is_logged_in' => false
                ]);
    }

    /** @test */
    public function user_can_logout_from_all_devices()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        // Create multiple sessions
        UserSession::create([
            'user_id' => $user->id,
            'token' => 'token-1',
            'expires_at' => Carbon::now()->addHours(2),
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Browser 1'
        ]);

        UserSession::create([
            'user_id' => $user->id,
            'token' => 'token-2',
            'expires_at' => Carbon::now()->addHours(2),
            'ip_address' => '192.168.1.1',
            'user_agent' => 'Browser 2'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/auth/logout-all-devices');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Successfully logged out from all devices'
                ]);

        // Check that all sessions were deleted
        $this->assertEquals(0, UserSession::where('user_id', $user->id)->count());
    }

    /** @test */
    public function user_can_get_active_sessions()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        // Create active sessions
        UserSession::create([
            'user_id' => $user->id,
            'token' => 'token-1',
            'expires_at' => Carbon::now()->addHours(2),
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Browser 1'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/auth/active-sessions');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'active_sessions' => [
                        '*' => [
                            'ip_address',
                            'user_agent',
                            'expires_at'
                        ]
                    ],
                    'total_sessions'
                ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_protected_routes()
    {
        $response = $this->getJson('/api/auth/me');
        $response->assertStatus(401);

        $response = $this->postJson('/api/auth/logout');
        $response->assertStatus(401);

        $response = $this->postJson('/api/auth/refresh');
        $response->assertStatus(401);
    }
}