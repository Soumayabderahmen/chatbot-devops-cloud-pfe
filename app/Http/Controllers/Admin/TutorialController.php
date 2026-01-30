<?php

namespace App\Http\Controllers\Admin;

use App\Models\Tutorial;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TutorialController extends Controller
{
    public function index()
{
    return view('Admin.Tutorial');
}

}
