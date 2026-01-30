<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class StartupAdminController extends Controller
{
    public function index()
    {
$startups = User::where('role', 'startup')
        ->with(['startup.sector']) // on charge aussi le secteur de la startup
        ->get();        return view('Admin.Startups', ['startups' => $startups]);
    }
}
