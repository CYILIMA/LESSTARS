<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Setting;
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

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
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
            'donation_message' => ['nullable', 'string', 'max:500'],
            'organisation_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
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

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value ?? ''],
            );
        }

        return back()->with('success', 'Organisation settings saved.');
    }
}
