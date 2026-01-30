<?php

namespace App\Http\Controllers\Api\Faq;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Faq;
use Inertia\Inertia;
class FaqController extends Controller
{

    // Appel API pour récupérer les données dynamiques
    public function index()
    {
        return Faq::where('is_active', true)->orderBy('id')->get();
    }
}




