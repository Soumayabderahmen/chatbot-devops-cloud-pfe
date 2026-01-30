<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminFaqController extends Controller
{
    /**
     * Afficher la liste des FAQs.
     */
    public function index()
    {
        return view('Faq.faq', [
            'faqs' => Faq::orderBy('id')->get()
        ]);
    }

   
}
