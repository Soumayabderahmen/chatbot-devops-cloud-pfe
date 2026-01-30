<?php

namespace Tests\Unit\Admin;

use App\Models\User;
use App\Models\ChatMessage;
use App\Models\ChatbotReaction;
use App\Models\ChatSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatbotReactionAdminControllerTest extends TestCase
{
    use RefreshDatabase;

    private const BOT_RESPONSE_MESSAGE = 'Bot response message';

    public function test_index_displays_reactions()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();

        $chatSession = ChatSession::factory()->create();

        $reaction = ChatbotReaction::factory()->create([
            'user_id' => $user->id,
            'message' => self::BOT_RESPONSE_MESSAGE,
            'reaction' => 'like'
        ]);

        $response = $this->actingAs($admin)->get(route('admin.chatbot.reactions'));

        $response->assertStatus(200);
        $response->assertViewHas('reactions');

        $reactions = $response->viewData('reactions');
        $this->assertNotEmpty($reactions);
        $this->assertEquals($reaction->reaction, $reactions[0]['reaction']);
    }

    public function test_index_displays_reactions_with_messages()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();

        $chatSession = ChatSession::factory()->create();

        $message = ChatMessage::factory()->create([
            'user_id' => $user->id,
            'is_from_bot' => false
        ]);

        $botReply = ChatMessage::factory()->create([
            'user_id' => $user->id,
            'is_from_bot' => true
        ]);

        $reaction = ChatbotReaction::factory()->create([
            'user_id' => $user->id,
            'message' => $botReply->message ?? self::BOT_RESPONSE_MESSAGE,
            'reaction' => 'like'
        ]);

        $response = $this->actingAs($admin)->get(route('admin.chatbot.reactions'));

        $response->assertStatus(200);
        $response->assertViewHas('reactions');

        $reactions = $response->viewData('reactions');
        $this->assertNotEmpty($reactions);
        $this->assertEquals($reaction->reaction, $reactions[0]['reaction']);
        $this->assertEquals($user->id, $reactions[0]['user']['id']);
        $this->assertNotNull($message->id);
        $this->assertNotNull($botReply->id);
    }

    public function test_index_shows_reactions_with_proper_context()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();

        $chatSession = ChatSession::factory()->create([
            'session_id' => 'test-session-686fd0eeb267b',
        ]);

        ChatMessage::factory()->create([
            'user_id' => $user->id,
            'message' => 'User question',
            'sender' => 'bot',
            'intent' => 'minus',
            'response_time' => 3.45,
            'is_from_bot' => false,
        ]);

        $reaction = ChatbotReaction::factory()->create([
            'user_id' => $user->id,
            'message' => 'Bot response message',
            'reaction' => 'like',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.chatbot.reactions'));

        $response->assertStatus(200);
        $response->assertViewHas('reactions');
    }
}
