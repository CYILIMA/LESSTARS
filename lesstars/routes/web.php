<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\TeamMemberController;
use App\Http\Controllers\UpdateController;
use App\Models\Event;
use App\Models\TeamMember;
use App\Models\Update;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home', [
        'featuredEvent' => Event::published()
            ->upcoming()
            ->orderBy('starts_at')
            ->first(),
    ]);
})->name('home');

Route::view('/about', 'about')->name('about');
Route::get('/team', function () {
    return view('team', [
        'teamMembers' => TeamMember::published()->orderBy('display_order')->orderBy('name')->get(),
    ]);
})->name('team');

Route::get('/activities', function () {
    return view('activities', [
        'events' => Event::published()
            ->upcoming()
            ->orderBy('starts_at')
            ->get(),
    ]);
})->name('activities');

Route::view('/donate', 'donate')->name('donate');

Route::get('/updates', function () {
    return view('updates', [
        'updates' => Update::published()->latest('published_at')->get(),
    ]);
})->name('updates');

Route::get('/admin/login', [AdminController::class, 'create'])
    ->middleware('guest')
    ->name('admin.login');

Route::post('/admin/login', [AdminController::class, 'store'])
    ->middleware('guest')
    ->name('admin.login.store');

Route::post('/admin/logout', [AdminController::class, 'destroy'])
    ->middleware('auth')
    ->name('admin.logout');

Route::middleware('auth')
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/', [AdminController::class, 'index'])->name('dashboard');
        Route::resource('events', EventController::class)->except('show');
        Route::resource('updates', UpdateController::class)->except('show');
        Route::resource('team-members', TeamMemberController::class)->except('show');
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
        Route::put('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
    });
