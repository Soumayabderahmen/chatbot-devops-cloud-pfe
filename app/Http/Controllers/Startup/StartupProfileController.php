<?php

namespace App\Http\Controllers\Startup;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Sector;
use Illuminate\Support\Facades\Storage;

class StartupProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $startup = $user->startup;
        $sectors = Sector::all();

        return view('Startup.profil', [
            'user' => $user,
            'startup' => $startup,
            'sectors' => $sectors,
        ]);
    }

    public function update(Request $request)
    {
        $response = ['success' => false, 'message' => '', 'data' => null, 'errors' => null];
        $statusCode = 200;

        try {
            $user = Auth::user();
            
            // Vérifier que l'utilisateur a bien un profil startup
            if (!$user->startup) {
                $response['message'] = 'Profil startup non trouvé';
                $statusCode = 404;
            } else {
                $startup = $user->startup;

                $validated = $request->validate([
                    'name' => 'required|string|max:255',
                    'email' => 'required|email|max:255',
                    'phone_number' => 'required|string|max:20',
                    'website_link' => 'nullable|url|max:255',
                    'description' => 'nullable|string|max:500',
                    'sector_id' => 'nullable|exists:sectors,id',
                    'logo_startup' => 'nullable|image|max:2048', // 2MB max
                ]);

                // Mettre à jour les informations utilisateur
                $user->update([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                ]);

                // Gérer l'upload du logo
                if ($request->hasFile('logo_startup')) {
                    // Supprimer l'ancien logo s'il existe
                    if ($startup->logo_startup) {
                        Storage::disk('public')->delete($startup->logo_startup);
                    }
                    
                    // Stocker le nouveau logo
                    $path = $request->file('logo_startup')->store('logos', 'public');
                    $startup->logo_startup = $path;
                }

                // Mettre à jour les informations startup
                $startup->fill([
                    'phone_number' => $validated['phone_number'],
                    'website_link' => $validated['website_link'],
                    'description' => $validated['description'],
                    'sector_id' => $validated['sector_id'],
                ])->save();

                $response['success'] = true;
                $response['message'] = 'Profil mis à jour avec succès ✅';
                $response['data'] = [
                    'user' => $user->fresh(),
                    'startup' => $startup->fresh()
                ];
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            $response['message'] = 'Erreur de validation';
            $response['errors'] = $e->errors();
            $statusCode = 422;
        } catch (\Exception $e) {
            $response['message'] = 'Une erreur est survenue lors de la mise à jour du profil';
            $response['error'] = $e->getMessage();
            $statusCode = 500;
        }

        return response()->json($response, $statusCode);
    }
}
