<?php

namespace Tests\Unit\Admin;

use App\Models\User;  // ← Ajoutez cette ligne
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TutorialControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_displays_tutorials()
    {
        // Créer un utilisateur admin
        $admin = User::factory()->create([
            'role' => 'admin', // Utilisez 'role' au lieu de 'is_admin' selon votre structure
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.tutorials.index'));

        $response->assertStatus(200);
    }
}
