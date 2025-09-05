<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Placement;
use App\Models\Client;
use App\Models\User;
use App\Models\PicExternal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tymon\JWTAuth\Facades\JWTAuth;

class PlacementControllerTest extends TestCase
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
    public function authenticated_user_can_get_placements_list()
    {
        Placement::factory()->count(3)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/placements');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        '*' => [
                            'uuid',
                            'name',
                            'client_id',
                            'pic_external_id',
                            'pic_internal_id',
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
    public function authenticated_user_can_search_placements()
    {
        $placement1 = Placement::factory()->create(['name' => 'Software Developer']);
        $placement2 = Placement::factory()->create(['name' => 'Project Manager']);
        $placement3 = Placement::factory()->create(['name' => 'Software Tester']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/placements?search=Software');

        $response->assertStatus(200)
                ->assertJson(['success' => true]);

        $data = $response->json('data');
        $this->assertCount(2, $data); // Should find placement1 and placement3
    }

    /** @test */
    public function authenticated_user_can_create_placement()
    {
        $client = Client::factory()->create();
        $picExternal = PicExternal::factory()->create();
        $picInternal = User::factory()->create();

        $placementData = [
            'name' => 'Senior Developer Placement',
            'client_id' => $client->uuid,
            'pic_external_id' => $picExternal->uuid,
            'pic_internal_id' => $picInternal->uuid
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/placements', $placementData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'uuid',
                        'name',
                        'client_id',
                        'pic_external_id',
                        'pic_internal_id'
                    ]
                ])
                ->assertJson([
                    'success' => true,
                    'message' => 'Placement created successfully'
                ]);

        $this->assertDatabaseHas('placements', [
            'name' => 'Senior Developer Placement',
            'client_id' => $client->id,
            'pic_external_id' => $picExternal->id,
            'pic_internal_id' => $picInternal->id,
            'created_by' => $this->user->id
        ]);
    }

    /** @test */
    public function authenticated_user_cannot_create_placement_with_invalid_data()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/placements', [
            'name' => '', // Required field
            'client_id' => 'invalid-uuid', // Invalid UUID
            'pic_external_id' => 'invalid-uuid', // Invalid UUID
            'pic_internal_id' => 'invalid-uuid' // Invalid UUID
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'client_id', 'pic_external_id', 'pic_internal_id']);
    }

    /** @test */
    public function authenticated_user_can_view_specific_placement()
    {
        $placement = Placement::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson("/api/placements/{$placement->uuid}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'uuid',
                        'name',
                        'client_id',
                        'pic_external_id',
                        'pic_internal_id',
                        'client' => [
                            'uuid',
                            'name'
                        ],
                        'pic_external' => [
                            'uuid',
                            'name'
                        ],
                        'pic_internal' => [
                            'uuid',
                            'name'
                        ]
                    ]
                ])
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'uuid' => $placement->uuid,
                        'name' => $placement->name
                    ]
                ]);
    }

    /** @test */
    public function authenticated_user_gets_404_for_nonexistent_placement()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/placements/nonexistent-uuid');

        $response->assertStatus(404);
    }

    /** @test */
    public function authenticated_user_can_update_placement()
    {
        $placement = Placement::factory()->create([
            'name' => 'Old Placement Name',
            'created_by' => $this->user->id
        ]);

        $newClient = Client::factory()->create();
        $newPicExternal = PicExternal::factory()->create();
        $newPicInternal = User::factory()->create();

        $updateData = [
            'name' => 'Updated Placement Name',
            'client_id' => $newClient->uuid,
            'pic_external_id' => $newPicExternal->uuid,
            'pic_internal_id' => $newPicInternal->uuid
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson("/api/placements/{$placement->uuid}", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Placement updated successfully',
                    'data' => [
                        'name' => 'Updated Placement Name'
                    ]
                ]);

        $this->assertDatabaseHas('placements', [
            'id' => $placement->id,
            'name' => 'Updated Placement Name',
            'client_id' => $newClient->id,
            'pic_external_id' => $newPicExternal->id,
            'pic_internal_id' => $newPicInternal->id,
            'updated_by' => $this->user->id
        ]);
    }

    /** @test */
    public function authenticated_user_can_delete_placement()
    {
        $placement = Placement::factory()->create(['created_by' => $this->user->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson("/api/placements/{$placement->uuid}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Placement deleted successfully'
                ]);

        $this->assertSoftDeleted('placements', ['id' => $placement->id]);
        
        // Check that deleted_by is set
        $deletedPlacement = Placement::withTrashed()->find($placement->id);
        $this->assertEquals($this->user->id, $deletedPlacement->deleted_by);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_placements_endpoints()
    {
        $placement = Placement::factory()->create();

        // Test all endpoints without authentication
        $this->getJson('/api/placements')->assertStatus(401);
        $this->postJson('/api/placements', [])->assertStatus(401);
        $this->getJson("/api/placements/{$placement->uuid}")->assertStatus(401);
        $this->putJson("/api/placements/{$placement->uuid}", [])->assertStatus(401);
        $this->deleteJson("/api/placements/{$placement->uuid}")->assertStatus(401);
    }

    /** @test */
    public function placements_list_includes_pagination()
    {
        Placement::factory()->count(20)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/placements?per_page=5');

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

    /** @test */
    public function placement_includes_relationships_when_loaded()
    {
        $placement = Placement::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson("/api/placements/{$placement->uuid}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'client' => ['uuid', 'name'],
                        'pic_external' => ['uuid', 'name'],
                        'pic_internal' => ['uuid', 'name']
                    ]
                ]);
    }
}