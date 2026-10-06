<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function create()
    {
        return view('admin.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials + ['is_admin' => true], true)) {
            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()
            ->withErrors(['email' => 'These credentials do not match our records.'])
            ->onlyInput('email');
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function index()
    {
        return view('admin.dashboard', [
            'events' => Event::latest('starts_at')->take(5)->get(),
            'publishedCount' => Event::published()->count(),
        ]);
    }

    public function registeredUsers()
    {
        return view('admin.registered-users', [
            'users' => User::query()
                ->where('is_admin', false)
                ->whereNull('registration_number')
                ->latest()
                ->paginate(20),
        ]);
    }

    public function settings()
    {
        return view('admin.settings', [
            'settings' => Setting::pluck('value', 'key'),
        ]);
    }

    public function updateSettings(Request $request)
    {
        $settings = $request->validate([
            'organisation_email' => ['nullable', 'email'],
            'instagram_url' => ['nullable', 'url'],
            'facebook_url' => ['nullable', 'url'],
            'twitter_url' => ['nullable', 'url'],
            'youtube_url' => ['nullable', 'url'],
            'whatsapp_url' => ['nullable', 'url'],
            'donation_message' => ['nullable', 'string', 'max:500'],
            'about_content' => ['nullable', 'string', 'max:5000'],
            'mission_statement' => ['nullable', 'string', 'max:2000'],
            'vision_statement' => ['nullable', 'string', 'max:2000'],
            'organisation_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'site_background' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:6144'],
        ]);

        foreach ($settings as $key => $value) {
            if ($key === 'organisation_logo' && $request->hasFile('organisation_logo')) {
                $path = $request->file('organisation_logo')->store('logos', 'public');
                Setting::updateOrCreate(
                    ['key' => 'organisation_logo_path'],
                    ['value' => $path],
                );

                continue;
            }

            if ($key === 'site_background' && $request->hasFile('site_background')) {
                $previousPath = Setting::where('key', 'site_background_path')->value('value');

                if ($previousPath) {
                    Storage::disk('public')->delete($previousPath);
                }

                $path = $request->file('site_background')->store('site-backgrounds', 'public');
                Setting::updateOrCreate(
                    ['key' => 'site_background_path'],
                    ['value' => $path],
                );

                continue;
            }

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value ?? ''],
            );
        }

        return back()->with('success', 'Organisation settings saved.');
    }
}
