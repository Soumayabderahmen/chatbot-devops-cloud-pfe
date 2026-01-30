<?php
namespace Tests\Unit\Chatbot;

use App\Models\ChatMessage;
use App\Models\User;
use App\Models\ChatSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ChatbotControllerTest extends TestCase
{
    use RefreshDatabase;

    private const USER_MESSAGE = 'Hello!';
    private const BOT_MESSAGE = 'Hi, how can I help you?';

    public function test_save_history_authenticated_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('chatbot.saveHistory'), [
            'userMessage' => self::USER_MESSAGE,
            'botMessage' => self::BOT_MESSAGE,
            'startTime' => now()->timestamp,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'saved']);

        $this->assertDatabaseHas('chat_messages', [
            'user_id' => $user->id,
            'message' => self::USER_MESSAGE,
            'sender' => 'user',
        ]);

        $this->assertDatabaseHas('chat_messages', [
            'user_id' => $user->id,
            'message' => self::BOT_MESSAGE,
            'sender' => 'bot',
        ]);
    }

    public function test_save_history_anonymous_user()
    {
        $sessionId = 'some-random-session-id';

        $response = $this->postJson(route('chatbot.saveHistory'), [
            'userMessage' => self::USER_MESSAGE,
            'botMessage' => self::BOT_MESSAGE,
            'startTime' => now()->timestamp,
        ], ['X-Session-ID' => $sessionId]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'saved']);
        
        $session = ChatSession::where('session_id', $sessionId)->first();
        $this->assertDatabaseHas('chat_messages', [
            'chat_session_id' => $session->id,
            'message' => self::USER_MESSAGE,
            'sender' => 'user',
        ]);

        $this->assertDatabaseHas('chat_messages', [
            'chat_session_id' => $session->id,
            'message' => self::BOT_MESSAGE,
            'sender' => 'bot',
        ]);
    }

    public function test_get_history_authenticated_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        ChatMessage::factory()->create([
            'user_id' => $user->id,
            'sender' => 'user',
            'message' => self::USER_MESSAGE,
        ]);

        $response = $this->getJson(route('chatbot.getHistory'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'history' => [
                '*' => [
                    'message',
                    'sender',
                    'created_at',
                    'reaction',
                ]
            ]
        ]);
    }

    public function test_get_history_anonymous_user()
    {
        $sessionId = 'some-random-session-id';
        
        $response = $this->getJson(route('chatbot.getHistory'), ['X-Session-ID' => $sessionId]);

        $response->assertStatus(200);
        $response->assertJson([
            'history' => [],
        ]);
    }
}
