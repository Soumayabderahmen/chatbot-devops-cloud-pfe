<?php

namespace App\Http\Controllers\Api\Startup;

use App\Http\Controllers\Controller;
use App\Models\User;

class StartupPublicController extends Controller
{
    public function graduated()
    {
        // Tu peux ajouter une colonne `graduated` si besoin, ici on filtre par `statut`
        $startups = User::with('startup.sector')
            ->where('role', 'startup')
            ->where('statut', 'active') // ou 'graduated' si tu ajoutes ce statut
            ->get()
            ->map(function ($user) {
                return [
                    'name' => $user->name,
                    'description' => $user->startup->description ?? '',
                    'image' => $user->startup->logo_startup ? asset('storage/' . $user->startup->logo_startup) : '/asset/img/default-logo.png',
                    'phone' => $user->startup->phone_number,
                    'website' => $user->startup->website_link,
                    'link' => '/startinc2',
                    'category' => $user->startup->sector->name ?? 'Autre',
                ];
            });

        return response()->json($startups);
    }
}
