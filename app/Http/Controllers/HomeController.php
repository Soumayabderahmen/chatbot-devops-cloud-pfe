<?php

namespace App\Http\Controllers;
use App\Models\TeamMember;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function coach()
    {
        return view('Home.coach');
    }
public function startup()
{
    $aiFeatures = [
        ['title' => 'Assistance 24/7', 'description' => 'Réponses disponibles à tout moment, jour et nuit.'],
        ['title' => 'Apprentissage adaptatif', 'description' => 'L’IA s’adapte à votre niveau et vos besoins.'],
        ['title' => 'Ressources intelligentes', 'description' => 'Des outils pertinents sélectionnés pour vous.'],
        ['title' => 'Suivi de progression détaillé', 'description' => 'Visualisez vos performances et progrès.'],
    ];

    return view('Home.startup', compact('aiFeatures'));
}



    public function investisseur()
    {
        return view('Home.investisseur');
    }
    
    public function equipe()
{
    $all = TeamMember::all()->groupBy('type');

    $founders = $all['founder'] ?? [];
    $chefs = $all['chef'] ?? [];
    $members = $all['member'] ?? [];

    return view('Home.equipe', compact('founders', 'chefs', 'members'));
}
    public function forum()
    {
        return view('Home.forum');
    }
    public function startinc()
    {
        return view('Home.startup-incube');
    }
    public function formation()
    {
        return view('Home.formation');
    }
    public function resources()
    {
        return view('Home.ressources');
    }
    public function agentia()
    {
        return view('Home.agentia');
    }
    public function agentia2()
    {
    return view('Home.agentia2');
    }

        public function tuto1()
    {
        return view('Home.tuto1');
    }
    public function tuto2()
    {
        return view('Home.tuto2');
    }
    public function tuto3()
    {
        return view('Home.tuto3');
    }
  
    
   
    
}
