<?php

namespace App\Http\Controllers;

use App\Models\Theatre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminTheatreController extends Controller
{
    public function index() { return view('admin.theatres.index', ['theatres' => Theatre::latest()->paginate(12)]); }
    public function create() { return view('admin.theatres.form', ['theatre' => new Theatre]); }
    public function store(Request $request) { Theatre::create($this->data($request)); return redirect()->route('admin.theatres.index')->with('success', 'Theatre audio added.'); }
    public function edit(Theatre $theatre) { return view('admin.theatres.form', compact('theatre')); }
    public function update(Request $request, Theatre $theatre) { $theatre->update($this->data($request, $theatre)); return redirect()->route('admin.theatres.index')->with('success', 'Theatre audio updated.'); }

    public function destroy(Theatre $theatre)
    {
        Storage::disk('public')->delete([$theatre->thumbnail_path, $theatre->audio_path]);
        $theatre->delete();
        return back()->with('success', 'Theatre audio removed.');
    }

    private function data(Request $request, ?Theatre $theatre = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'description' => ['required', 'string', 'max:4000'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'audio' => [$theatre ? 'nullable' : 'required', 'file', 'mimes:mp3,wav,ogg,m4a', 'max:102400'],
            'status' => ['required', 'in:draft,published'],
        ]);
        unset($data['thumbnail'], $data['audio']);

        foreach (['thumbnail' => 'thumbnail_path', 'audio' => 'audio_path'] as $input => $column) {
            if ($request->hasFile($input)) {
                if ($theatre?->{$column}) Storage::disk('public')->delete($theatre->{$column});
                $data[$column] = $request->file($input)->store('theatres', 'public');
            }
        }

        return $data;
    }
}
