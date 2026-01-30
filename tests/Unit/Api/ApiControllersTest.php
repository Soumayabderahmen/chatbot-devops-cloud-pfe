<?php

namespace Tests\Unit\Api;

use App\Models\User;
use App\Models\Startup;
use App\Models\Faq;
use App\Models\SupportMessage;
use App\Models\TeamMember;
use App\Models\Tutorial;
use App\Models\ChatMessage;
use App\Models\ChatbotReaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ApiControllersTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test pour l'API FaqController - récupération des FAQs.
     */
    public function test_faq_index()
    {
        $faq = Faq::factory()->create(['is_active' => true]);

        $response = $this->getJson('/api/faqs/list');

        $response->assertStatus(200);
        $response->assertJsonFragment(['question' => $faq->question]);
    }

    /**
     * Test pour l'API StartupPublicController - Récupération des startups graduées.
     */
    public function test_startup_graduated()
    {
        $user = User::factory()->create(['role' => 'startup', 'statut' => 'active']);
        Startup::factory()->create(['user_id' => $user->id, 'description' => 'Test Startup']);
        
        $response = $this->getJson('/api/startups/graduated');

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => $user->name]);
    }

    /**
     * Test pour l'API SupportMessageController - Envoi d'un message de support.
     */
    public function test_support_message_store()
    {
        $response = $this->postJson('/api/contact/store', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'Test message',
            'category' => 'general',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('contactus'));
        $this->assertDatabaseHas('support_messages', ['name' => 'John Doe']);
    }

  /**
 * Test pour l'API SupportMessageAdminController - Suppression d'un message de support.
 */
public function test_support_message_admin_destroy()
{
    $message = SupportMessage::factory()->create();

    $response = $this->deleteJson("/api/admin/support-messages/{$message->id}");

    $response->assertStatus(200);
    $response->assertJson(['message' => 'Message de support supprimé avec succès.']); // Added period to match controller
    $this->assertDatabaseMissing('support_messages', ['id' => $message->id]);
}

    /**
     * Test pour l'API TeamMemberController - Ajout d'un membre de l'équipe.
     */
    public function test_team_member_store()
{
    // Skip test if GD extension is not available
    if (!extension_loaded('gd') || !function_exists('imagejpeg')) {
        $this->markTestSkipped('GD extension with JPEG support is not available');
    }

    $response = $this->postJson('api/admin/team', [
        'name' => 'Jane Doe',
        'role' => 'Founder',
        'type' => 'founder',
        'image' => UploadedFile::fake()->image('profile.jpg'),
    ]);

    $response->assertStatus(200);
    $response->assertJson(['message' => 'Membre ajouté avec succès']);
    $this->assertDatabaseHas('team_members', ['name' => 'Jane Doe']);
}


/**
 * Test pour l'API TeamMemberController - Mise à jour d'un membre de l'équipe.
 */
public function test_team_member_update()
{
    // Create a team member with a valid type that matches controller validation
    $member = TeamMember::factory()->create([
        'type' => 'member' // Ensure the factory creates with a valid type
    ]);

    $response = $this->putJson("/api/admin/team/{$member->id}", [
        'name' => 'John Smith',
        'role' => 'Member',
        'type' => 'member', // Use a valid type from controller validation
    ]);

    $response->assertStatus(200);
    $response->assertJson(['message' => 'Membre mis à jour avec succès']);
    $this->assertDatabaseHas('team_members', ['name' => 'John Smith']);
}

    /**
     * Test pour l'API TutorialController - Récupération de la liste des tutoriels.
     */
    public function test_tutorial_list()
    {
        $tutorial = Tutorial::factory()->create();

        $response = $this->getJson('api/admin/tutorials/list');

        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => $tutorial->title]);
    }

    /**
     * Test pour l'API TutorialController - Ajouter un tutoriel.
     */
 /**
 * Test pour l'API TutorialController - Ajouter un tutoriel.
 */
public function test_tutorial_store()
{
    $title = 'New Tutorial';

    $response = $this->postJson('api/admin/tutorials', [
        'title' => $title,
        'type' => 'url',
        'video_url' => 'https://example.com/tutorial-video',
    ]);

    $response->assertStatus(201); // Status 201 is correct for creation
    $response->assertJsonFragment(['title' => $title]); // Check for the created tutorial data
    $this->assertDatabaseHas('tutorials', ['title' => $title]);
}


  /**
 * Test pour l'API AdminFaqController - Ajouter une FAQ.
 */
public function test_admin_faq_store()
{
    $response = $this->postJson('/api/admin/faqs', [
        'question' => 'What is Laravel?',
        'answer' => 'Laravel is a PHP framework.',
    ]);

    $response->assertStatus(200);
    $response->assertJson(['message' => 'FAQ ajoutée avec succès.']); // Added period here
    $this->assertDatabaseHas('faqs', ['question' => 'What is Laravel?']);
}

    /**
     * Test pour l'API ChatbotAdminController - Statistiques du chatbot.
     */
    public function test_chatbot_stats()
    {
        $response = $this->getJson('/api/admin/chatbot/stats');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'totalMessages',
            'userCount',
            'guestCount',
            'positiveReactions',
            'negativeReactions',
            'satisfaction',
            'engagementRate',
            'avgResponseTime',
            'topQuestions',
            'roleDistribution',
            'satisfactionBreakdown',
        ]);
    }

    /**
     * Test pour l'API ChatbotAdminController - Récupérer les messages du chatbot.
     */
    public function test_chatbot_messages()
    {
        $response = $this->getJson('/api/admin/chatbot/messages');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'messages' => [
                '*' => [
                    'user_id',
                    'session_id',
                    'user_name',
                    'user_role',
                    'message',
                    'sender',
                    'intent',
                    'created_at',
                ],
            ]
        ]);
    }
}
