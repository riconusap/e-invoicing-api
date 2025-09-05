<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tymon\JWTAuth\Facades\JWTAuth;

class EmployeeControllerTest extends TestCase
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
    public function authenticated_user_can_get_employees_list()
    {
        Employee::factory()->count(3)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/employees');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        '*' => [
                            'uuid',
                            'full_name',
                            'nik',
                            'nip',
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
    public function authenticated_user_can_search_employees()
    {
        $employee1 = Employee::factory()->create(['full_name' => 'John Doe']);
        $employee2 = Employee::factory()->create(['full_name' => 'Jane Smith']);
        $employee3 = Employee::factory()->create(['nik' => '1234567890123456']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/employees?search=John');

        $response->assertStatus(200)
                ->assertJson(['success' => true]);

        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('John Doe', $data[0]['full_name']);
    }

    /** @test */
    public function authenticated_user_can_create_employee()
    {
        $employeeData = [
            'full_name' => 'John Doe',
            'nik' => '1234567890123456',
            'nip' => '123456789012345678'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/employees', $employeeData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'uuid',
                        'full_name',
                        'nik',
                        'nip'
                    ]
                ])
                ->assertJson([
                    'success' => true,
                    'message' => 'Employee created successfully'
                ]);

        $this->assertDatabaseHas('employees', [
            'full_name' => 'John Doe',
            'nik' => '1234567890123456',
            'nip' => '123456789012345678',
            'created_by' => $this->user->id
        ]);
    }

    /** @test */
    public function authenticated_user_cannot_create_employee_with_invalid_data()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/employees', [
            'full_name' => '', // Required field
            'nik' => '123', // Too short
            'nip' => '456' // Too short
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['full_name', 'nik', 'nip']);
    }

    /** @test */
    public function authenticated_user_cannot_create_employee_with_duplicate_nik()
    {
        Employee::factory()->create(['nik' => '1234567890123456']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/employees', [
            'full_name' => 'John Doe',
            'nik' => '1234567890123456', // Duplicate
            'nip' => '123456789012345678'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['nik']);
    }

    /** @test */
    public function authenticated_user_cannot_create_employee_with_duplicate_nip()
    {
        Employee::factory()->create(['nip' => '123456789012345678']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/employees', [
            'full_name' => 'John Doe',
            'nik' => '1234567890123456',
            'nip' => '123456789012345678' // Duplicate
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['nip']);
    }

    /** @test */
    public function authenticated_user_can_view_specific_employee()
    {
        $employee = Employee::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson("/api/employees/{$employee->uuid}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'uuid',
                        'full_name',
                        'nik',
                        'nip'
                    ]
                ])
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'uuid' => $employee->uuid,
                        'full_name' => $employee->full_name
                    ]
                ]);
    }

    /** @test */
    public function authenticated_user_gets_404_for_nonexistent_employee()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/employees/nonexistent-uuid');

        $response->assertStatus(404);
    }

    /** @test */
    public function authenticated_user_can_update_employee()
    {
        $employee = Employee::factory()->create([
            'full_name' => 'Old Name',
            'created_by' => $this->user->id
        ]);

        $updateData = [
            'full_name' => 'Updated Name',
            'nik' => '9876543210987654',
            'nip' => '876543210987654321'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson("/api/employees/{$employee->uuid}", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Employee updated successfully',
                    'data' => [
                        'full_name' => 'Updated Name',
                        'nik' => '9876543210987654',
                        'nip' => '876543210987654321'
                    ]
                ]);

        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'full_name' => 'Updated Name',
            'nik' => '9876543210987654',
            'updated_by' => $this->user->id
        ]);
    }

    /** @test */
    public function authenticated_user_can_delete_employee()
    {
        $employee = Employee::factory()->create(['created_by' => $this->user->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson("/api/employees/{$employee->uuid}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Employee deleted successfully'
                ]);

        $this->assertSoftDeleted('employees', ['id' => $employee->id]);
        
        // Check that deleted_by is set
        $deletedEmployee = Employee::withTrashed()->find($employee->id);
        $this->assertEquals($this->user->id, $deletedEmployee->deleted_by);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_employees_endpoints()
    {
        $employee = Employee::factory()->create();

        // Test all endpoints without authentication
        $this->getJson('/api/employees')->assertStatus(401);
        $this->postJson('/api/employees', [])->assertStatus(401);
        $this->getJson("/api/employees/{$employee->uuid}")->assertStatus(401);
        $this->putJson("/api/employees/{$employee->uuid}", [])->assertStatus(401);
        $this->deleteJson("/api/employees/{$employee->uuid}")->assertStatus(401);
    }

    /** @test */
    public function employees_list_includes_pagination()
    {
        Employee::factory()->count(20)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/employees?per_page=5');

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