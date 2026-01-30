<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\ChatbotReaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class ChatbotAdminController extends Controller
{
    public function index()
    {
        $users = User::whereHas('chatMessages')->get();

        return view('Chatbot.chatbot', [
            'users' => $users
        ]);
    }
 public function management()
    {
        return view('Chatbot.management', [
            'stats' => [
                'total_conversations' => ChatMessage::count(),
                'resolution_rate' => 78,
                'avg_response_time' => 3.2, // idem
                'satisfaction' => 86, // idem
            ],
            'settings' => [
                'enabled' => true,
                'bot_name' => 'Support Bot',
                'welcome_message' => 'Hello! How can I help you today?',
                'timeout_message' => 'Sorry for the delay...',
                'primary_color' => '#2563eb',
            ],
            
        ]);
    }


}
