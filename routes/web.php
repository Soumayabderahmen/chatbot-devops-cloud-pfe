<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Controllers
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ContactUs\SupportMessageController;
use App\Http\Controllers\Faq\FaqController;
use App\Http\Controllers\Chatbot\ChatbotController;
use App\Http\Controllers\Chatbot\ChatbotReactionController;
use App\Http\Controllers\Startup\StartupProfileController;

// Admin Controllers
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\SupportMessageAdminController;
use App\Http\Controllers\Admin\AdminFaqController;
use App\Http\Controllers\Admin\ChatbotAdminController;
use App\Http\Controllers\Admin\ChatbotReactionAdminController;
use App\Http\Controllers\Admin\TutorialController;
use App\Http\Controllers\Admin\TeamMemberController;
use App\Http\Controllers\Admin\StartupAdminController;

// Middlewares
use App\Http\Middleware\CheckAdmin;
use App\Http\Middleware\CheckRole;

// === 🏠 PUBLIC ROUTES ===
Route::get('/', fn () => view('Home.home_front'))->name('home');

Route::get('/startup', [HomeController::class, 'startup'])->name('startup');
Route::get('/coach', [HomeController::class, 'coach'])->name('coach');
Route::get('/investisseur', [HomeController::class, 'investisseur'])->name('investisseur');
Route::get('/forum', [HomeController::class, 'forum'])->name('forum');
Route::get('/equipe', [HomeController::class, 'equipe'])->name('equipe');
Route::get('/startinc', [HomeController::class, 'startinc'])->name('startinc');
Route::get('/formation', [HomeController::class, 'formation'])->name('formation');
Route::get('/resources', [HomeController::class, 'resources'])->name('resources');
Route::get('/agentia', [HomeController::class, 'agentia'])->name('agentia');
Route::get('/agentia2', [HomeController::class, 'agentia2'])->name('agentia2');
Route::get('/tuto1', [HomeController::class, 'tuto1'])->name('tuto1');
Route::get('/tuto2', [HomeController::class, 'tuto2'])->name('tuto2');
Route::get('/tuto3', [HomeController::class, 'tuto3'])->name('tuto3');

Route::get('/health-check', function () {
    return response()->json(['status' => 'ok'], 200);
});

// Contact / FAQ
Route::get('/contact', [SupportMessageController::class, 'index'])->name('contactus');
Route::get('/faqs', [FaqController::class, 'index'])->name('faq');


// ===  AUTH PROFILE ===
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Redirection selon rôle
Route::get('/mon-profil', function () {
    $user = auth()->user();
    return match ($user->role) {
        'startup' => redirect()->route('startup.profile'),
        'coach' => redirect()->route('dashboard.coach'),
        'investisseur' => redirect()->route('dashboard.investisseur'),
        default => abort(403),
    };
})->middleware('auth')->name('mon.profil');

// ===  DASHBOARDS PAR RÔLE ===
Route::middleware(['auth', CheckRole::class . ':coach'])->get('/dashboard/coach', fn () => view('Coach.dashboard'))->name('dashboard.coach');
Route::middleware(['auth', CheckRole::class . ':startup'])->get('/dashboard/startup', fn () => view('Startup.dashboard'))->name('dashboard.startup');
Route::middleware(['auth', CheckRole::class . ':investisseur'])->get('/dashboard/investisseur', fn () => view('Investisseur.dashboard'))->name('dashboard.investisseur');

// ===  STARTUP PROFILE ===
Route::middleware(['auth', CheckRole::class . ':startup'])->prefix('startup')->name('startup.')->group(function () {
    Route::get('/profile', [StartupProfileController::class, 'index'])->name('profile');
    Route::post('/profile', [StartupProfileController::class, 'update'])->name('profile.update');

});

// === 🤖 CHATBOT API (public et auth) ===
Route::get('/api/chatbot/history', [ChatbotController::class, 'getHistory'])->name('chatbot.getHistory');
Route::get('/chatbot/history/anonymous', [ChatbotController::class, 'getAnonymousHistory']);
Route::post('/api/chatbot/history/save', [ChatbotController::class, 'saveHistory'])->name('chatbot.saveHistory');
Route::post('/api/chatbot', [ChatbotController::class, 'sendMessage'])->name('chatbot.send');
Route::middleware('auth')->post('/chatbot/reaction', [ChatbotReactionController::class, 'store'])->name('chatbot.reaction.store');
Route::get('/api/public/chatbot/settings', fn () => \App\Models\ChatbotSetting::first());


// === ADMIN ZONE ===
Route::prefix('admin')->middleware(['auth', 'verified', CheckAdmin::class])->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    // Équipe
    Route::get('/team', [TeamMemberController::class, 'index'])->name('team');
    // Support
    Route::get('/support-messages', [SupportMessageAdminController::class, 'index'])->name('support.messages');
    Route::get('/support-messages/{id}', [SupportMessageAdminController::class, 'show'])->name('support.message.view');
    // FAQ
    Route::get('/faqs', [AdminFaqController::class, 'index'])->name('faqs.index');
    // Chatbot
Route::prefix('chatbot')->group(function () {
    Route::get('/', [ChatbotAdminController::class, 'index'])->name('chatbot.index');
    Route::get('/management', [ChatbotAdminController::class, 'management'])->name('chatbot.management');
    Route::get('/reactions', [ChatbotReactionAdminController::class, 'index'])->name('chatbot.reactions');

});
    // Tutoriels
    Route::get('/tutorials', [TutorialController::class, 'index'])->name('tutorials.index');
    // Startups
    Route::get('/startups', [StartupAdminController::class, 'index'])->name('startups.index');

});

// === INTENTIONS IA (publique ou admin) ===

require __DIR__.'/auth.php';
