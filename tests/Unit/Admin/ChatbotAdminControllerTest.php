<?php
namespace Tests\Unit\Admin;

use App\Models\User;
use App\Models\ChatMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatbotAdminControllerTest extends TestCase
{
    use RefreshDatabase;

   public function test_index_displays_users_with_chat_messages()
{
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    ChatMessage::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($admin)->get(route('admin.chatbot.index'));

    $response->assertStatus(200);
    $response->assertViewHas('users');

    $users = $response->viewData('users');
    $this->assertCount(1, $users);
    $this->assertEquals($user->id, $users->first()->id);
}

public function test_management_displays_chatbot_stats()
{
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get(route('admin.chatbot.management'));

    $response->assertStatus(200);
    $response->assertViewHas('stats');
    $response->assertViewHas('settings');

    $stats = $response->viewData('stats');
    $this->assertEquals(0, $stats['total_conversations']);
    $this->assertEquals(78, $stats['resolution_rate']);

    $settings = $response->viewData('settings');
    $this->assertTrue($settings['enabled']);
}

}
