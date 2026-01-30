<?php

namespace App\Http\Controllers\Api\Admin;
use App\Http\Controllers\Controller;
use App\Models\SupportMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SupportMessageAdminController extends Controller {
 


    /**
 * Supprimer un message de support.
 */
public function destroy(Request $request, SupportMessage $supportMessage)
{
    $supportMessage->delete();

    // Réponse JSON pour les requêtes AJAX
    if ($request->ajax() || $request->wantsJson()) {
        return response()->json([
            'message' => 'Message de support supprimé avec succès.'
        ]);
    }

    return redirect()->back()->with('success', 'Message de support supprimé avec succès.');
}
public function updateStatus(Request $request, $id)
{
    $request->validate(['status' => 'required|in:new,read,replied']);
    $msg = SupportMessage::findOrFail($id);
    $msg->status = $request->status;
    $msg->save();

    return response()->json(['success' => true]);
}
public function download($id)
{
    $message = SupportMessage::findOrFail($id);

    if (!$message->file_path || !Storage::disk('public')->exists($message->file_path)) {
        abort(404, 'Fichier introuvable');
    }

    return Storage::disk('public')->download($message->file_path);
}
    
}
