<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TeamMember;

class TeamMemberController extends Controller
{
    private const URL_RULE = 'nullable|url';
    private const IMAGE_RULE = 'nullable|image|max:2048';
    private const TYPE_RULE = 'required|in:founder,chef,member';

    public function index()
    {
        $members = TeamMember::all()->groupBy('type');
        if (request()->wantsJson()) {
            return response()->json($members);
        }
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'role' => 'required',
            'type' => self::TYPE_RULE,
            'image' => self::IMAGE_RULE,
            'linkedin' => self::URL_RULE,
            'twitter' => self::URL_RULE,
            'email' => 'nullable|email',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('team', 'public');
        }

        $member = TeamMember::create($data);
        return response()->json(['message' => 'Membre ajouté avec succès', 'member' => $member]);
    }

    public function update(Request $request, TeamMember $teamMember)
    {
        $data = $request->validate([
            'name' => 'required',
            'role' => 'required',
            'type' => self::TYPE_RULE,
            'image' => self::IMAGE_RULE,
            'linkedin' => self::URL_RULE,
            'twitter' => self::URL_RULE,
            'email' => 'nullable|email',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('team', 'public');
        }

        $teamMember->update($data);
        return response()->json(['message' => 'Membre mis à jour avec succès', 'member' => $teamMember]);
    }

    public function destroy(TeamMember $teamMember)
    {
        $teamMember->delete();
        return response()->json(['message' => 'Membre supprimé avec succès']);
    }
}

