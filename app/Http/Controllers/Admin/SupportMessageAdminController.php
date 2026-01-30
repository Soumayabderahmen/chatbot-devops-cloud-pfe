<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SupportMessageAdminController extends Controller {
    /**
     * Liste des messages de support pour l'admin.
     */
    public function index() {
        $messages = SupportMessage::latest()->paginate(10);
        return view('Support.support', compact('messages'));
    }

    /**
     * Afficher un message en détail.
     */
    public function show($id) {
        $message = SupportMessage::findOrFail($id);
        return view('Support.view', compact('message'));
    }


    
}
