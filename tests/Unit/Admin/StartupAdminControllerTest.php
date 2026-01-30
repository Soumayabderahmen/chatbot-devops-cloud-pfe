<?php

namespace Tests\Unit\Admin;
use App\Models\Sector;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StartupAdminControllerTest extends TestCase
{
    use RefreshDatabase;

 public function test_index_displays_startups_with_sector()
{
    $admin = User::factory()->create(['role' => 'admin']);
    $startupUser = User::factory()->create(['role' => 'startup']);

    // Créer un secteur
    $sector = Sector::factory()->create(['name' => 'Tech']);

    // Utiliser la factory Startup
    \App\Models\Startup::factory()->create([
        'user_id' => $startupUser->id,
        'sector_id' => $sector->id
    ]);

    $response = $this->actingAs($admin)->get(route('admin.startups.index'));

    $response->assertStatus(200);
    $response->assertViewHas('startups');

    $startups = $response->viewData('startups');
    $this->assertCount(1, $startups);
    $this->assertEquals('Tech', $startups[0]->startup->sector->name);
}


}
