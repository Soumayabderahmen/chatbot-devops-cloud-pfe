<?php

namespace Tests\Unit\Admin;
use App\Models\User;
use App\Models\SupportMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupportMessageAdminControllerTest extends TestCase
{
    use RefreshDatabase;

   public function test_index_displays_support_messages()
{
    $admin = User::factory()->create(['role' => 'admin']);
    SupportMessage::factory()->create();


    $response = $this->actingAs($admin)->get('admin/support-messages');

    $response->assertStatus(200);
    $response->assertViewHas('messages');

    $messages = $response->viewData('messages');
    $this->assertEquals(1, $messages->total()); // si paginé
}


   public function test_show_displays_message_details()
{
    $admin = User::factory()->create(['role' => 'admin']);
    $message = SupportMessage::factory()->create();

    $response = $this->actingAs($admin)->get("/admin/support-messages/{$message->id}");

    $response->assertStatus(200);
    $response->assertViewHas('message');
    $this->assertEquals($message->id, $response->viewData('message')->id);
}

}
