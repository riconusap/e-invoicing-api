<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Client;
use App\Models\User;
use App\Models\Placement;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_client()
    {
        $user = User::factory()->create();
        
        $client = Client::factory()->create([
            'name' => 'Test Company',
            'email' => 'test@company.com',
            'created_by' => $user->id,
            'updated_by' => $user->id
        ]);

        $this->assertInstanceOf(Client::class, $client);
        $this->assertEquals('Test Company', $client->name);
        $this->assertEquals('test@company.com', $client->email);
        $this->assertDatabaseHas('clients', [
            'name' => 'Test Company',
            'email' => 'test@company.com'
        ]);
    }

    /** @test */
    public function it_hides_id_attribute()
    {
        $client = Client::factory()->create();
        $clientArray = $client->toArray();

        $this->assertArrayNotHasKey('id', $clientArray);
        $this->assertArrayHasKey('uuid', $clientArray);
    }

    /** @test */
    public function it_appends_logo_url_attribute()
    {
        $client = Client::factory()->create();
        $clientArray = $client->toArray();

        $this->assertArrayHasKey('logo_url', $clientArray);
    }

    /** @test */
    public function it_returns_logo_url_when_logo_exists()
    {
        $client = Client::factory()->create(['logo' => 'logos/test-logo.png']);
        
        $expectedUrl = asset('storage/logos/test-logo.png');
        $this->assertEquals($expectedUrl, $client->logo_url);
    }

    /** @test */
    public function it_returns_null_logo_url_when_no_logo()
    {
        $client = Client::factory()->create(['logo' => null]);
        
        $this->assertNull($client->logo_url);
    }

    /** @test */
    public function it_belongs_to_created_by_user()
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['created_by' => $user->id]);

        $this->assertInstanceOf(User::class, $client->createdBy);
        $this->assertEquals($user->id, $client->createdBy->id);
    }

    /** @test */
    public function it_belongs_to_updated_by_user()
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['updated_by' => $user->id]);

        $this->assertInstanceOf(User::class, $client->updatedBy);
        $this->assertEquals($user->id, $client->updatedBy->id);
    }

    /** @test */
    public function it_belongs_to_deleted_by_user()
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['deleted_by' => $user->id]);

        $this->assertInstanceOf(User::class, $client->deletedBy);
        $this->assertEquals($user->id, $client->deletedBy->id);
    }

    /** @test */
    public function it_has_many_placements()
    {
        $client = Client::factory()->create();
        $placement1 = Placement::factory()->create(['client_id' => $client->id]);
        $placement2 = Placement::factory()->create(['client_id' => $client->id]);

        $this->assertCount(2, $client->placements);
        $this->assertTrue($client->placements->contains($placement1));
        $this->assertTrue($client->placements->contains($placement2));
    }

    /** @test */
    public function it_uses_soft_deletes()
    {
        $client = Client::factory()->create();
        $clientId = $client->id;
        
        $client->delete();
        
        $this->assertSoftDeleted('clients', ['id' => $clientId]);
        $this->assertNotNull($client->fresh()->deleted_at);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = [
            'uuid', 'name', 'logo', 'address', 'phone', 'email',
            'pic_name', 'pic_phone', 'pic_email', 'created_by',
            'updated_by', 'deleted_by'
        ];

        $client = new Client();
        $this->assertEquals($fillable, $client->getFillable());
    }
}