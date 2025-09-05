<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Placement;
use App\Models\Client;
use App\Models\User;
use App\Models\PicExternal;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlacementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_placement()
    {
        $client = Client::factory()->create();
        $picExternal = PicExternal::factory()->create();
        $picInternal = User::factory()->create();
        $creator = User::factory()->create();
        
        $placement = Placement::factory()->create([
            'name' => 'Software Developer Placement',
            'client_id' => $client->id,
            'pic_external_id' => $picExternal->id,
            'pic_internal_id' => $picInternal->id,
            'created_by' => $creator->id,
            'updated_by' => $creator->id
        ]);

        $this->assertInstanceOf(Placement::class, $placement);
        $this->assertEquals('Software Developer Placement', $placement->name);
        $this->assertEquals($client->id, $placement->client_id);
        $this->assertDatabaseHas('placements', [
            'name' => 'Software Developer Placement',
            'client_id' => $client->id
        ]);
    }

    /** @test */
    public function it_hides_id_attribute()
    {
        $placement = Placement::factory()->create();
        $placementArray = $placement->toArray();

        $this->assertArrayNotHasKey('id', $placementArray);
        $this->assertArrayHasKey('uuid', $placementArray);
    }

    /** @test */
    public function it_belongs_to_client()
    {
        $client = Client::factory()->create();
        $placement = Placement::factory()->create(['client_id' => $client->id]);

        $this->assertInstanceOf(Client::class, $placement->client);
        $this->assertEquals($client->id, $placement->client->id);
    }

    /** @test */
    public function it_belongs_to_pic_external()
    {
        $picExternal = PicExternal::factory()->create();
        $placement = Placement::factory()->create(['pic_external_id' => $picExternal->id]);

        $this->assertInstanceOf(PicExternal::class, $placement->picExternal);
        $this->assertEquals($picExternal->id, $placement->picExternal->id);
    }

    /** @test */
    public function it_belongs_to_pic_internal_user()
    {
        $user = User::factory()->create();
        $placement = Placement::factory()->create(['pic_internal_id' => $user->id]);

        $this->assertInstanceOf(User::class, $placement->picInternal);
        $this->assertEquals($user->id, $placement->picInternal->id);
    }

    /** @test */
    public function it_belongs_to_created_by_user()
    {
        $user = User::factory()->create();
        $placement = Placement::factory()->create(['created_by' => $user->id]);

        $this->assertInstanceOf(User::class, $placement->createdBy);
        $this->assertEquals($user->id, $placement->createdBy->id);
    }

    /** @test */
    public function it_belongs_to_updated_by_user()
    {
        $user = User::factory()->create();
        $placement = Placement::factory()->create(['updated_by' => $user->id]);

        $this->assertInstanceOf(User::class, $placement->updatedBy);
        $this->assertEquals($user->id, $placement->updatedBy->id);
    }

    /** @test */
    public function it_belongs_to_deleted_by_user()
    {
        $user = User::factory()->create();
        $placement = Placement::factory()->create(['deleted_by' => $user->id]);

        $this->assertInstanceOf(User::class, $placement->deletedBy);
        $this->assertEquals($user->id, $placement->deletedBy->id);
    }

    /** @test */
    public function it_uses_soft_deletes()
    {
        $placement = Placement::factory()->create();
        $placementId = $placement->id;
        
        $placement->delete();
        
        $this->assertSoftDeleted('placements', ['id' => $placementId]);
        $this->assertNotNull($placement->fresh()->deleted_at);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = [
            'uuid', 'name', 'client_id', 'pic_external_id',
            'pic_internal_id', 'created_by', 'updated_by', 'deleted_by'
        ];

        $placement = new Placement();
        $this->assertEquals($fillable, $placement->getFillable());
    }
}