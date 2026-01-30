<?php
namespace Tests\Unit\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    // tests/Unit/Admin/AdminDashboardControllerTest.php
    public function test_index_displays_users_except_admin()
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => 'admin']);
        
        // Create a regular user
        $user = User::factory()->create(['role' => 'user']);
        
        // Act as admin
        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        
        $response->assertStatus(200);
        $response->assertViewHas('users');
        $users = $response->viewData('users');
        
        // Remplacer dump() par des assertions plus spécifiques
        $this->assertCount(1, $users, 'Should have exactly 1 non-admin user');
        $this->assertEquals($user->id, $users->first()->id);
        $this->assertNotEquals('admin', $users->first()->role);
    }

    public function test_index_displays_only_non_admin_users()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user1 = User::factory()->create(['role' => 'startup']);
        $user2 = User::factory()->create(['role' => 'coach']);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $users = $response->viewData('users');

        $this->assertCount(2, $users); // admin exclu
        $this->assertFalse($users->contains(fn($u) => $u->role === 'admin'));
        
        // Use the created users in assertions to fix SonarLint warnings
        $userIds = $users->pluck('id')->toArray();
        $this->assertContains($user1->id, $userIds, 'Startup user should be included');
        $this->assertContains($user2->id, $userIds, 'Coach user should be included');
        
        // Verify the specific roles are present
        $userRoles = $users->pluck('role')->toArray();
        $this->assertContains('startup', $userRoles);
        $this->assertContains('coach', $userRoles);
    }
}
