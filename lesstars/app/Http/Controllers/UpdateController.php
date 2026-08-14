<?php

namespace App\Http\Controllers;

use App\Models\Update;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UpdateController extends Controller
{
    public function index()
    {
        return view('admin.updates.index', ['updates' => Update::latest('published_at')->latest()->paginate(12)]);
    }

    public function create()
    {
        return view('admin.updates.form', ['update' => new Update]);
    }

    public function store(Request $request)
    {
        Update::create($this->dataWithThumbnail($request));

        return redirect()->route('admin.updates.index')->with('success', 'Update published.');
    }

    public function edit(Update $update)
    {
        return view('admin.updates.form', compact('update'));
    }

    public function update(Request $request, Update $update)
    {
        $update->update($this->dataWithThumbnail($request, $update));

        return redirect()->route('admin.updates.index')->with('success', 'Update updated.');
    }

    public function destroy(Update $update)
    {
        if ($update->thumbnail_path) {
            Storage::disk('public')->delete($update->thumbnail_path);
        }

        $update->delete();

        return back()->with('success', 'Update removed.');
    }

    /** @return array<string, mixed> */
    private function dataWithThumbnail(Request $request, ?Update $update = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'summary' => ['required', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'author_name' => ['nullable', 'string', 'max:120'],
            'author_bio' => ['nullable', 'string', 'max:1000'],
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
            if ($update?->thumbnail_path) {
                Storage::disk('public')->delete($update->thumbnail_path);
            }

            $data['thumbnail_path'] = $request->file('thumbnail')->store('update-thumbnails', 'public');
        }

        return $data;
    }
}
