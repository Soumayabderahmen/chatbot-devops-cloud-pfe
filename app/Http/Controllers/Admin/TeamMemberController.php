<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TeamMember;

class TeamMemberController extends Controller
{
    // LISTE DES MEMBRES
    public function index()
    {
        $members = TeamMember::all()->groupBy('type');
        // Si requête AJAX (depuis Vue)
        if (request()->wantsJson()) {
            return response()->json($members);
        }

        // Sinon classique (si jamais appelé depuis Blade ou testé)
        return view('Admin.Equipe', compact('members'));
    }

    
}
