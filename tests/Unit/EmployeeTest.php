<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Employee;
use App\Models\User;
use App\Models\EmployeeDocument;
use App\Models\ContractEmployee;
use App\Models\Placement;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployeeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_an_employee()
    {
        $user = User::factory()->create();
        
        $employee = Employee::factory()->create([
            'full_name' => 'John Doe',
            'nik' => '1234567890123456',
            'nip' => '123456789012345678',
            'created_by' => $user->id,
            'updated_by' => $user->id
        ]);

        $this->assertInstanceOf(Employee::class, $employee);
        $this->assertEquals('John Doe', $employee->full_name);
        $this->assertEquals('1234567890123456', $employee->nik);
        $this->assertEquals('123456789012345678', $employee->nip);
        $this->assertDatabaseHas('employees', [
            'full_name' => 'John Doe',
            'nik' => '1234567890123456',
            'nip' => '123456789012345678'
        ]);
    }

    /** @test */
    public function it_hides_id_attribute()
    {
        $employee = Employee::factory()->create();
        $employeeArray = $employee->toArray();

        $this->assertArrayNotHasKey('id', $employeeArray);
        $this->assertArrayHasKey('uuid', $employeeArray);
    }

    /** @test */
    public function it_has_one_user()
    {
        $employee = Employee::factory()->create();
        $user = User::factory()->create(['employee_id' => $employee->id]);

        $this->assertInstanceOf(User::class, $employee->user);
        $this->assertEquals($user->id, $employee->user->id);
    }

    /** @test */
    public function it_belongs_to_created_by_user()
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['created_by' => $user->id]);

        $this->assertInstanceOf(User::class, $employee->createdBy);
        $this->assertEquals($user->id, $employee->createdBy->id);
    }

    /** @test */
    public function it_belongs_to_updated_by_user()
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['updated_by' => $user->id]);

        $this->assertInstanceOf(User::class, $employee->updatedBy);
        $this->assertEquals($user->id, $employee->updatedBy->id);
    }

    /** @test */
    public function it_belongs_to_deleted_by_user()
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['deleted_by' => $user->id]);

        $this->assertInstanceOf(User::class, $employee->deletedBy);
        $this->assertEquals($user->id, $employee->deletedBy->id);
    }

    /** @test */
    public function it_has_many_employee_documents()
    {
        $employee = Employee::factory()->create();
        
        // We would need to create EmployeeDocument factory and instances here
        // This is a placeholder for when EmployeeDocument factory exists
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $employee->employeeDocuments);
    }

    /** @test */
    public function it_has_many_contract_employees()
    {
        $employee = Employee::factory()->create();
        
        // We would need to create ContractEmployee factory and instances here
        // This is a placeholder for when ContractEmployee factory exists
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $employee->contractEmployees);
    }

    /** @test */
    public function it_has_many_placements_as_pic_internal()
    {
        $employee = Employee::factory()->create();
        $placement = Placement::factory()->create(['pic_internal_id' => $employee->id]);

        $this->assertCount(1, $employee->placements);
        $this->assertTrue($employee->placements->contains($placement));
    }

    /** @test */
    public function it_uses_soft_deletes()
    {
        $employee = Employee::factory()->create();
        $employeeId = $employee->id;
        
        $employee->delete();
        
        $this->assertSoftDeleted('employees', ['id' => $employeeId]);
        $this->assertNotNull($employee->fresh()->deleted_at);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = [
            'uuid', 'full_name', 'nik', 'nip', 'created_by',
            'updated_by', 'deleted_by'
        ];

        $employee = new Employee();
        $this->assertEquals($fillable, $employee->getFillable());
    }
}