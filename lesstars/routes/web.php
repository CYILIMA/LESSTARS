<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminTheatreController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomepagePhotoController;
use App\Http\Controllers\TeamMemberController;
use App\Http\Controllers\TheatreCommentController;
use App\Http\Controllers\TheatreController;
use App\Models\Event;
use App\Models\HomepagePhoto;
use App\Models\Setting;
use App\Models\TeamMember;
use App\Models\Theatre;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('home', [
        'events' => Event::published()
            ->latest('starts_at')
            ->get(),

        'homepagePhotos' => HomepagePhoto::published()
            ->orderBy('display_order')
            ->get(),
    ]);
})->name('home');

Route::get('/about', function () {
    return view('about', [
        'settings' => Setting::pluck('value', 'key'),
    ]);
})->name('about');

Route::get('/team', function () {
    return view('team-index');
})->name('team');

Route::get('/team/{category}', function (string $category) {
    return view('team', [
        'teamMembers' => TeamMember::published()
            ->inCategory($category)
            ->orderBy('display_order')
            ->orderBy('name')
            ->get(),

        'pageTitle' => TeamMember::CATEGORIES[$category],
        'eyebrow' => 'OUR TEAM',
    ]);
})->whereIn('category', array_keys(TeamMember::CATEGORIES))
    ->name('team.category');

/*
|--------------------------------------------------------------------------
| Activities / Theatre Routes
|--------------------------------------------------------------------------
*/

Route::get('/activities', [TheatreController::class, 'index'])
    ->name('activities');

Route::get('/activities/{id}', function (int $id) {
    return redirect()->route(
        'theatres.show',
        Theatre::findOrFail($id)
    );
})->whereNumber('id')
    ->name('theatres.legacy');

Route::get('/activities/{theatre}', [TheatreController::class, 'show'])
    ->name('theatres.show');

Route::get('/activities/{theatre}/download', [TheatreController::class, 'download'])
    ->name('theatres.download');

Route::post(
    '/activities/{theatre}/comments',
    [TheatreController::class, 'comment']
)->middleware('throttle:10,1')
    ->name('theatres.comments.store');

/*
|--------------------------------------------------------------------------
| Public Authentication Routes
|--------------------------------------------------------------------------
*/

Route::get('/sign-up', [AuthController::class, 'create'])
    ->name('auth');

Route::get('/sign-in', [AuthController::class, 'create'])
    ->name('auth.login');

Route::redirect('/donate', '/sign-up', 301)
    ->name('donate');

Route::post('/sign-up', [AuthController::class, 'register'])
    ->name('register');

Route::post('/sign-in', [AuthController::class, 'login'])
    ->name('login');

Route::post('/sign-out', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Events Routes
|--------------------------------------------------------------------------
*/

Route::get('/events', function () {
    return view('events', [
        'events' => Event::published()
            ->latest('published_at')
            ->latest('starts_at')
            ->get(),
    ]);
})->name('events');

Route::get('/events/{event}', function (string $event) {
    $eventRecord = is_numeric($event)
        ? Event::findOrFail((int) $event)
        : Event::where('slug', $event)->firstOrFail();

    abort_unless($eventRecord->status === 'published', 404);

    if (is_numeric($event)) {
        return redirect()->route('events.show', $eventRecord, 301);
    }

    return view('event', ['event' => $eventRecord]);
})->name('events.show');

/*
|--------------------------------------------------------------------------
| Admin Authentication Routes
|--------------------------------------------------------------------------
*/

Route::get('/admin/login', [AdminController::class, 'create'])
    ->middleware('guest')
    ->name('admin.login');

Route::post('/admin/login', [AdminController::class, 'store'])
    ->middleware('guest')
    ->name('admin.login.store');

Route::post('/admin/logout', [AdminController::class, 'destroy'])
    ->middleware('auth')
    ->name('admin.logout');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {

        /*
        | Dashboard
        */
        Route::get('/', [AdminController::class, 'index'])
            ->name('dashboard');

        /*
        | Events
        */
        Route::resource('events', EventController::class)
            ->except('show');

        /*
        | Theatre Activities
        */
        Route::resource('theatres', AdminTheatreController::class)
            ->except('show');

        /*
        | Theatre Comments
        */
        Route::get(
            'theatre-comments',
            [TheatreCommentController::class, 'index']
        )->name('theatre-comments.index');

        Route::patch(
            'theatre-comments/{comment}/approve',
            [TheatreCommentController::class, 'approve']
        )->name('theatre-comments.approve');

        Route::patch(
            'theatre-comments/{comment}/reject',
            [TheatreCommentController::class, 'reject']
        )->name('theatre-comments.reject');

        Route::post(
            'theatre-comments/{comment}/reply',
            [TheatreCommentController::class, 'reply']
        )->name('theatre-comments.reply');

        /*
        | Team Members
        */
        Route::resource('team-members', TeamMemberController::class)
            ->except('show');

        /*
        | Homepage Photos
        */
        Route::resource('homepage-photos', HomepagePhotoController::class)
            ->except('show');

        /*
        | Settings
        */
        Route::get('/settings', [AdminController::class, 'settings'])
            ->name('settings');

        Route::put('/settings', [AdminController::class, 'updateSettings'])
            ->name('settings.update');
    });
