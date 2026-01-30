<?php
use App\Http\Controllers\Api\Faq\FaqController;
use App\Http\Controllers\Api\Admin\TutorialController;
use App\Http\Controllers\Api\Admin\TeamMemberController;
use App\Http\Controllers\Api\Admin\AdminFaqController;
use App\Http\Controllers\Api\Admin\SupportMessageAdminController;
use App\Http\Controllers\Api\ContactUs\SupportMessageController;
use App\Http\Controllers\Api\Admin\ChatbotSettingsController;
use App\Http\Controllers\Api\Admin\ChatbotAdminController;
use App\Http\Middleware\CheckAdmin;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\Startup\StartupPublicController;
use App\Http\Middleware\CheckRole;
use Illuminate\Support\Facades\Route;

//Public
Route::get('/faqs/list', [FaqController::class, 'index']);
Route::get('/tutorials/public', [TutorialController::class, 'publicList']);
Route::post('/contact/store', [SupportMessageController::class, 'store'])->name('contact.store');
Route::get('/startups/graduated', [StartupPublicController::class, 'graduated']);

//Admin
Route::prefix('admin')->group(function () {

Route::prefix('tutorials')->group(function () {
    Route::get('/list', [TutorialController::class, 'list']);
    Route::post('/', [TutorialController::class, 'store']);
    Route::put('/{tutorial}', [TutorialController::class, 'update']);
    Route::delete('/{tutorial}', [TutorialController::class, 'destroy']);
});
Route::prefix('team')->group(function () {
        Route::get('/', [TeamMemberController::class, 'index']); 
        Route::post('/', [TeamMemberController::class, 'store']); 
        Route::put('/{teamMember}', [TeamMemberController::class, 'update']); 
        Route::delete('/{teamMember}', [TeamMemberController::class, 'destroy']);
});
Route::prefix('faqs')->group(function () {
  Route::post('/', [AdminFaqController::class, 'store'])->name('faqs.store');
    Route::put('/{faq}', [AdminFaqController::class, 'update'])->name('faqs.update');
    Route::delete('/{faq}', [AdminFaqController::class, 'destroy'])->name('faqs.destroy');
});
Route::prefix('support-messages')->group(function () {
    Route::put('/{id}/status', [SupportMessageAdminController::class, 'updateStatus'])->name('support.message.status');
    Route::get('/download/{id}', [SupportMessageAdminController::class, 'download'])->name('support.download');
    Route::delete('/{supportMessage}', [SupportMessageAdminController::class, 'destroy'])->name('support.messages.delete');
});
Route::prefix('chatbot')->group(function () {
    Route::get('/settings', [ChatbotSettingsController::class, 'getSettings']);
    Route::post('/settings', [ChatbotSettingsController::class, 'saveSettings']);
    Route::get('/messages', [ChatbotAdminController::class, 'messages']);
    Route::get('/management', [ChatbotAdminController::class, 'management'])->name('chatbot.management');
    Route::get('/stats', [ChatbotAdminController::class, 'stats'])->name('chatbot.stats');
});

});