<?php

namespace Tests\Unit\Admin;

use App\Models\User;
use App\Models\TeamMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamMemberControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_displays_team_members()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        // Spécifiez explicitement une valeur valide de l'ENUM
        $member = TeamMember::factory()->create([
            'type' => 'member' // Valeur autorisée par votre ENUM
        ]);

        $response = $this->actingAs($admin)->get(route('admin.team'));

        $response->assertStatus(200);
        $response->assertViewHas('members');

        $members = $response->viewData('members');
        $this->assertArrayHasKey($member->type, $members);
        $this->assertTrue($members[$member->type]->contains($member));
    }
}
