<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index()
    {
        return view('admin.events.index', [
            'events' => Event::latest('published_at')->latest('starts_at')->paginate(12),
        ]);
    }

    public function create()
    {
        return view('admin.events.form', ['event' => new Event]);
    }

    public function store(Request $request)
    {
        Event::create($this->validatedData($request));

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event created.');
    }

    public function edit(Event $event)
    {
        return view('admin.events.form', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $event->update($this->validatedData($request, $event));

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event updated.');
    }

    public function destroy(Event $event)
    {
        if ($event->thumbnail_path) {
            Storage::disk('public')->delete($event->thumbnail_path);
        }

        $event->delete();

        return back()->with('success', 'Event removed.');
    }

    private function validatedData(Request $request, ?Event $event = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'summary' => ['required', 'string', 'max:1000'],
            'body' => ['nullable', 'string'],
            'venue' => ['required', 'string', 'max:160'],
            'author_name' => ['nullable', 'string', 'max:120'],
            'author_bio' => ['nullable', 'string', 'max:1000'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
            'image_url' => ['nullable', 'url'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'published_at' => ['nullable', 'date'],
            'status' => ['required', 'in:draft,published'],
        ]);

        $data['author_name'] ??= $request->user()->name;

        if ($data['status'] === 'published' && blank($data['published_at'] ?? null)) {
            $data['published_at'] = now();
        }

        unset($data['thumbnail']);

        if ($request->hasFile('thumbnail')) {
            if ($event?->thumbnail_path) {
                Storage::disk('public')->delete($event->thumbnail_path);
            }

            $data['thumbnail_path'] = $request->file('thumbnail')->store('event-thumbnails', 'public');
        }

        return $data;
    }
}
