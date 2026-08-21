<?php

namespace App\Http\Controllers;

use App\Models\Theatre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TheatreController extends Controller
{
    public function index()
    {
        return view('activities', ['theatres' => Theatre::published()->latest()->get()]);
    }

    public function show(Theatre $theatre)
    {
        abort_unless($theatre->status === 'published', 404);

        $theatre->load(['comments' => fn ($query) => $query->approved()->with(['replies' => fn ($replies) => $replies->approved()->oldest()])->oldest()]);

        return view('theatres.show', [
            'theatre' => $theatre,
            'otherTheatres' => Theatre::published()->whereKeyNot($theatre->id)->latest()->take(8)->get(),
        ]);
    }

    public function download(Theatre $theatre)
    {
        abort_unless($theatre->status === 'published' && $theatre->audio_path && Storage::disk('public')->exists($theatre->audio_path), 404);

        return Storage::disk('public')->download($theatre->audio_path, str($theatre->title)->slug().'.'.pathinfo($theatre->audio_path, PATHINFO_EXTENSION));
    }

    public function comment(Request $request, Theatre $theatre)
    {
        abort_unless($theatre->status === 'published', 404);

        $theatre->comments()->create($request->validate([
            'name' => ['required', 'string', 'max:80'],
            'email' => ['nullable', 'email', 'max:255'],
            'body' => ['required', 'string', 'max:1500'],
        ]) + ['status' => 'pending']);

        return back()->with('success', 'Thank you. Your comment will appear after approval.');
    }
}
