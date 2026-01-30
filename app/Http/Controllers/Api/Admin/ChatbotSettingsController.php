<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatbotSetting;
use Illuminate\Http\Request;

class ChatbotSettingsController extends Controller
{
    private const NULLABLE_STRING = 'nullable|string';

    public function getSettings()
    {
        $settings = ChatbotSetting::first();
        return response()->json($settings);
    }

    public function saveSettings(Request $request)
    {
        $validated = $request->validate([
            'bot_name'         => self::NULLABLE_STRING . '|max:255',
            'welcome_message'  => self::NULLABLE_STRING,
            'timeout_message'  => self::NULLABLE_STRING,
            'primary_color'    => self::NULLABLE_STRING . '|max:7', // ex: #2563eb
            'training_text'    => self::NULLABLE_STRING,
        ]);

        $settings = ChatbotSetting::first() ?? new ChatbotSetting();
        $settings->fill($validated);
        $settings->save();

        return response()->json(['message' => 'Paramètres mis à jour']);
    }

    public function saveTraining(Request $request)
    {
        $text = $request->input('training_text');

        if (!$text) {
            return response()->json(['error' => 'Texte vide'], 400);
        }

        $blocks = preg_split('/\n(?=#)/', $text);
        foreach ($blocks as $block) {
            $lines = array_filter(array_map('trim', explode("\n", $block)));
            $intent = null;
            $samples = [];

            foreach ($lines as $line) {
                if (str_starts_with($line, '#')) {
                    $intent = trim(substr($line, 1));
                } elseif ($intent) {
                    $samples[] = $line;
                }
            }

            foreach ($samples as $sample) {
                \App\Models\ChatbotTraining::create([
                    'intent_name'    => $intent,
                    'training_text'  => $sample,
                    'source'         => 'admin-ui',
                ]);
            }
        }

        return response()->json(['message' => 'Entraînement sauvegardé dans Laravel']);
    }
}
