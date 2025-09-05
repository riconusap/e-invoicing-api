<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_user()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'username' => 'johndoe'
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertEquals('johndoe', $user->username);
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'username' => 'johndoe'
        ]);
    }

    /** @test */
    public function it_hides_sensitive_attributes()
    {
        $user = User::factory()->create();
        $userArray = $user->toArray();

        $this->assertArrayNotHasKey('password', $userArray);
        $this->assertArrayNotHasKey('remember_token', $userArray);
        $this->assertArrayNotHasKey('id', $userArray);
        $this->assertArrayNotHasKey('verification_at', $userArray);
    }

    /** @test */
    public function it_can_hash_password_when_set()
    {
        $user = User::factory()->make();
        $user->password = 'plain-password';
        
        $this->assertTrue(Hash::check('plain-password', $user->password));
    }

    /** @test */
    public function it_implements_jwt_subject()
    {
        $user = User::factory()->create();

        $this->assertEquals($user->id, $user->getJWTIdentifier());
        $this->assertEquals([], $user->getJWTCustomClaims());
    }

    /** @test */
    public function it_can_get_route_key_name()
    {
        $user = new User();
        $this->assertEquals('uuid', $user->getRouteKeyName());
    }

    /** @test */
    public function it_can_clean_expired_sessions()
    {
        $user = User::factory()->create();
        
        // Create an expired session
        UserSession::create([
            'user_id' => $user->id,
            'token' => 'expired-token',
            'expires_at' => Carbon::now()->subHours(2),
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Browser'
        ]);

        // Create a valid session
        UserSession::create([
            'user_id' => $user->id,
            'token' => 'valid-token',
            'expires_at' => Carbon::now()->addHours(2),
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Browser'
        ]);

        $this->assertEquals(2, $user->sessions()->count());
        
        $user->cleanExpiredSessions();
        
        $this->assertEquals(1, $user->sessions()->count());
        $this->assertEquals('valid-token', $user->sessions()->first()->token);
    }

    /** @test */
    public function it_can_check_if_user_has_active_sessions()
    {
        $user = User::factory()->create();
        
        $this->assertFalse($user->hasActiveSessions());

        // Create an active session
        UserSession::create([
            'user_id' => $user->id,
            'token' => 'active-token',
            'expires_at' => Carbon::now()->addHours(2),
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Browser'
        ]);

        $this->assertTrue($user->hasActiveSessions());
    }

    /** @test */
    public function it_can_get_active_sessions_count()
    {
        $user = User::factory()->create();
        
        $this->assertEquals(0, $user->getActiveSessionsCount());

        // Create multiple active sessions
        UserSession::create([
            'user_id' => $user->id,
            'token' => 'token-1',
            'expires_at' => Carbon::now()->addHours(2),
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Browser'
        ]);

        UserSession::create([
            'user_id' => $user->id,
            'token' => 'token-2',
            'expires_at' => Carbon::now()->addHours(2),
            'ip_address' => '192.168.1.1',
            'user_agent' => 'Another Browser'
        ]);

        $this->assertEquals(2, $user->getActiveSessionsCount());
    }

    /** @test */
    public function it_uses_soft_deletes()
    {
        $user = User::factory()->create();
        $userId = $user->id;
        
        $user->delete();
        
        $this->assertSoftDeleted('users', ['id' => $userId]);
        $this->assertNotNull($user->fresh()->deleted_at);
    }
}