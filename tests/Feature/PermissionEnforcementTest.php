<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionEnforcementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_super_admin_can_manage_cities(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('Super Admin');

        $this->actingAs($admin)
            ->postJson(route('admin.cities.store'), [
                'name' => 'Mumbai',
                'state' => 'Maharashtra',
                'is_active' => 1,
            ])
            ->assertCreated();

        $this->assertDatabaseHas('cities', ['name' => 'Mumbai', 'state' => 'Maharashtra']);
    }

    public function test_student_cannot_access_city_management(): void
    {
        $student = User::factory()->create(['is_active' => true]);
        $student->assignRole('Student');

        $this->actingAs($student)
            ->get(route('admin.cities.index'))
            ->assertForbidden();
    }

    public function test_teacher_cannot_create_cities(): void
    {
        $teacher = User::factory()->create(['is_active' => true]);
        $teacher->assignRole('Teacher');

        $this->actingAs($teacher)
            ->postJson(route('admin.cities.store'), [
                'name' => 'Pune',
                'state' => 'Maharashtra',
                'is_active' => 1,
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('cities', 0);
    }

    public function test_city_validation_rejects_missing_fields(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('Super Admin');

        $this->actingAs($admin)
            ->postJson(route('admin.cities.store'), ['name' => '', 'state' => ''])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'state']);

        $this->assertSame(0, City::count());
    }
}
