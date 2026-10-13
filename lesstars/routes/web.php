<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminMemberController;
use App\Http\Controllers\AdminTheatreController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CacahueteController;
use App\Http\Controllers\ContributionController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomepagePhotoController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MembershipApplicationController;
use App\Http\Controllers\MembershipApplicationFormController;
use App\Http\Controllers\TeamMemberController;
use App\Http\Controllers\TheatreCommentController;
use App\Http\Controllers\TheatreController;
use App\Http\Middleware\EnsureApprovedMember;
use App\Http\Middleware\EnsureUserIsAdmin;
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

Route::middleware('auth')->group(function (): void {
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

    Route::get('/activities/{theatre}/audio', [TheatreController::class, 'audio'])
        ->name('theatres.audio');

    Route::get('/activities/{theatre}/download', [TheatreController::class, 'download'])
        ->name('theatres.download');

    Route::post(
        '/activities/{theatre}/comments',
        [TheatreController::class, 'comment']
    )->middleware('throttle:10,1')
        ->name('theatres.comments.store');
});

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

Route::get('/apply/{form:public_token}', [MembershipApplicationController::class, 'create'])->name('membership-applications.create');
Route::post('/apply/{form:public_token}', [MembershipApplicationController::class, 'store'])->middleware('throttle:10,1')->name('membership-applications.store');
Route::get('/application-submitted', [MembershipApplicationController::class, 'thankYou'])->name('membership-applications.thank-you');

Route::get('/members', [MemberController::class, 'create'])->middleware('auth')->name('members.login');
Route::post('/members', [MemberController::class, 'store'])->middleware(['auth', 'throttle:10,1'])->name('members.login.store');
Route::middleware(['auth', EnsureApprovedMember::class])->prefix('members')->name('members.')->group(function (): void {
    Route::get('/dashboard', [MemberController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [MemberController::class, 'destroy'])->name('logout');
    Route::post('/cacahuete/{participant}/pick', [CacahueteController::class, 'pick'])->name('cacahuete.pick');
});

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
    ->middleware(EnsureUserIsAdmin::class)
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {

        /*
        | Dashboard
        */
        Route::get('/', [AdminController::class, 'index'])
            ->name('dashboard');

        Route::get('/registered-users', [AdminController::class, 'registeredUsers'])
            ->name('registered-users.index');

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

        Route::resource('members', AdminMemberController::class)
            ->except('show');

        Route::resource('attendance', AttendanceController::class)
            ->only(['index', 'create', 'store', 'destroy']);

        Route::resource('contributions', ContributionController::class)
            ->except('show');

        Route::get('membership-applications', [MembershipApplicationFormController::class, 'index'])->name('membership-applications.index');
        Route::get('membership-applications/create', [MembershipApplicationFormController::class, 'create'])->name('membership-applications.create');
        Route::post('membership-applications', [MembershipApplicationFormController::class, 'store'])->name('membership-applications.store');
        Route::patch('membership-applications/{membershipApplication}/toggle', [MembershipApplicationFormController::class, 'toggle'])->name('membership-applications.toggle');
        Route::get('membership-applications/export', [MembershipApplicationFormController::class, 'export'])->name('membership-applications.export');
        Route::get('membership-applications/{membershipApplication}/submissions', [MembershipApplicationFormController::class, 'applications'])->name('membership-applications.submissions');
        Route::get('member-applications/{application}', [MembershipApplicationFormController::class, 'showApplication'])->name('member-applications.show');
        Route::patch('member-applications/{application}/review', [MembershipApplicationFormController::class, 'review'])->name('member-applications.review');

        Route::get('cacahuete', [CacahueteController::class, 'index'])->name('cacahuete.index');
        Route::get('cacahuete/create', [CacahueteController::class, 'create'])->name('cacahuete.create');
        Route::post('cacahuete', [CacahueteController::class, 'store'])->name('cacahuete.store');
        Route::patch('cacahuete/{cacahuete}/toggle', [CacahueteController::class, 'toggle'])->name('cacahuete.toggle');
        Route::delete('cacahuete/{cacahuete}', [CacahueteController::class, 'destroy'])->name('cacahuete.destroy');

        /*
        | Settings
        */
        Route::get('/settings', [AdminController::class, 'settings'])
            ->name('settings');

        Route::put('/settings', [AdminController::class, 'updateSettings'])
            ->name('settings.update');
    });
