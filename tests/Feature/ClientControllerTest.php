<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

class ClientControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->token = JWTAuth::fromUser($this->user);
    }

    /** @test */
    public function authenticated_user_can_get_clients_list()
    {
        Client::factory()->count(3)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/clients');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        '*' => [
                            'uuid',
                            'name',
                            'email',
                            'phone',
                            'address',
                            'pic_name',
                            'pic_phone',
                            'pic_email',
                            'logo_url',
                            'created_by',
                            'updated_by'
                        ]
                    ],
                    'pagination' => [
                        'current_page',
                        'last_page',
                        'per_page',
                        'total'
                    ]
                ])
                ->assertJson([
                    'success' => true
                ]);
    }

    /** @test */
    public function authenticated_user_can_search_clients()
    {
        $client1 = Client::factory()->create(['name' => 'ABC Company']);
        $client2 = Client::factory()->create(['name' => 'XYZ Corporation']);
        $client3 = Client::factory()->create(['email' => 'test@abc.com']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/clients?search=ABC');

        $response->assertStatus(200)
                ->assertJson(['success' => true]);

        $data = $response->json('data');
        $this->assertCount(2, $data); // Should find client1 by name and client3 by email
    }

    /** @test */
    public function authenticated_user_can_create_client()
    {
        Storage::fake('public');

        $clientData = [
            'name' => 'Test Company',
            'address' => '123 Test Street',
            'phone' => '+1234567890',
            'email' => 'test@company.com',
            'pic_name' => 'John Doe',
            'pic_phone' => '+0987654321',
            'pic_email' => 'john@company.com',
            'logo' => UploadedFile::fake()->image('logo.png')
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/clients', $clientData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'uuid',
                        'name',
                        'email',
                        'phone',
                        'address',
                        'pic_name',
                        'pic_phone',
                        'pic_email',
                        'logo_url'
                    ]
                ])
                ->assertJson([
                    'success' => true,
                    'message' => 'Client created successfully'
                ]);

        $this->assertDatabaseHas('clients', [
            'name' => 'Test Company',
            'email' => 'test@company.com',
            'created_by' => $this->user->id
        ]);

        // Check that logo was uploaded
        Storage::disk('public')->assertExists('logos/' . $response->json('data.logo'));
    }

    /** @test */
    public function authenticated_user_cannot_create_client_with_invalid_data()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/clients', [
            'name' => '', // Required field
            'email' => 'invalid-email', // Invalid email format
            'pic_email' => 'invalid-pic-email' // Invalid email format
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'email', 'pic_email']);
    }

    /** @test */
    public function authenticated_user_can_view_specific_client()
    {
        $client = Client::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson("/api/clients/{$client->uuid}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'uuid',
                        'name',
                        'email',
                        'phone',
                        'address',
                        'pic_name',
                        'pic_phone',
                        'pic_email',
                        'logo_url'
                    ]
                ])
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'uuid' => $client->uuid,
                        'name' => $client->name
                    ]
                ]);
    }

    /** @test */
    public function authenticated_user_gets_404_for_nonexistent_client()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/clients/nonexistent-uuid');

        $response->assertStatus(404);
    }

    /** @test */
    public function authenticated_user_can_update_client()
    {
        $client = Client::factory()->create([
            'name' => 'Old Company Name',
            'created_by' => $this->user->id
        ]);

        $updateData = [
            'name' => 'Updated Company Name',
            'email' => 'updated@company.com',
            'phone' => '+1111111111'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson("/api/clients/{$client->uuid}", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Client updated successfully',
                    'data' => [
                        'name' => 'Updated Company Name',
                        'email' => 'updated@company.com',
                        'phone' => '+1111111111'
                    ]
                ]);

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'name' => 'Updated Company Name',
            'email' => 'updated@company.com',
            'updated_by' => $this->user->id
        ]);
    }

    /** @test */
    public function authenticated_user_can_delete_client()
    {
        $client = Client::factory()->create(['created_by' => $this->user->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson("/api/clients/{$client->uuid}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Client deleted successfully'
                ]);

        $this->assertSoftDeleted('clients', ['id' => $client->id]);
        
        // Check that deleted_by is set
        $deletedClient = Client::withTrashed()->find($client->id);
        $this->assertEquals($this->user->id, $deletedClient->deleted_by);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_clients_endpoints()
    {
        $client = Client::factory()->create();

        // Test all endpoints without authentication
        $this->getJson('/api/clients')->assertStatus(401);
        $this->postJson('/api/clients', [])->assertStatus(401);
        $this->getJson("/api/clients/{$client->uuid}")->assertStatus(401);
        $this->putJson("/api/clients/{$client->uuid}", [])->assertStatus(401);
        $this->deleteJson("/api/clients/{$client->uuid}")->assertStatus(401);
    }

    /** @test */
    public function clients_list_includes_pagination()
    {
        Client::factory()->count(20)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/clients?per_page=5');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'pagination' => [
                        'current_page' => 1,
                        'per_page' => 5,
                        'total' => 20
                    ]
                ]);

        $this->assertCount(5, $response->json('data'));
    }
}