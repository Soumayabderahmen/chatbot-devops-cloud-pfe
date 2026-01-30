<?php

namespace Tests\Unit\Chatbot;

use App\Models\User;
use App\Models\ChatbotReaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ChatbotReactionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_reaction()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $message = 'Hello, Bot!';
        $reaction = 'happy';

        $response = $this->postJson(route('chatbot.reaction.store'), [
            'message' => $message,
            'reaction' => $reaction,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('chatbot_reactions', [
            'user_id' => $user->id,
            'message' => $message,
            'reaction' => $reaction,
        ]);
    }

    public function test_remove_reaction()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $message = 'Hello, Bot!';
        $reaction = 'happy';

        // Créer une réaction existante
        ChatbotReaction::create([
            'user_id' => $user->id,
            'message' => $message,
            'reaction' => $reaction,
        ]);

        $response = $this->postJson(route('chatbot.reaction.store'), [
            'message' => $message,
            'reaction' => null, // On supprime la réaction
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseMissing('chatbot_reactions', [
            'user_id' => $user->id,
            'message' => $message,
        ]);
    }

    public function test_store_reaction_without_message()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('chatbot.reaction.store'), [
            'reaction' => 'happy',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('message');
    }
}
