<?php

namespace App\Http\Controllers;

use App\Models\HomepagePhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomepagePhotoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.homepage-photos.index', [
            'photos' => HomepagePhoto::orderBy('display_order')->latest()->paginate(12),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.homepage-photos.form', ['homepagePhoto' => new HomepagePhoto]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        HomepagePhoto::create($this->validatedData($request));

        return redirect()->route('admin.homepage-photos.index')->with('success', 'Homepage photo added.');
    }

    /**
     * Display the specified resource.
     */
    public function edit(HomepagePhoto $homepagePhoto)
    {
        return view('admin.homepage-photos.form', compact('homepagePhoto'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HomepagePhoto $homepagePhoto)
    {
        $homepagePhoto->update($this->validatedData($request, $homepagePhoto));

        return redirect()->route('admin.homepage-photos.index')->with('success', 'Homepage photo updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HomepagePhoto $homepagePhoto)
    {
        Storage::disk('public')->delete($homepagePhoto->image_path);
        $homepagePhoto->delete();

        return back()->with('success', 'Homepage photo removed.');
    }

    /** @return array<string, mixed> */
    private function validatedData(Request $request, ?HomepagePhoto $homepagePhoto = null): array
    {
        $data = $request->validate([
            'image' => [$homepagePhoto ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'description' => ['required', 'string', 'max:180'],
            'display_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'status' => ['required', 'in:draft,published'],
        ]);

        $data['display_order'] ??= 0;
        unset($data['image']);

        if ($request->hasFile('image')) {
            if ($homepagePhoto) {
                Storage::disk('public')->delete($homepagePhoto->image_path);
            }

            $data['image_path'] = $request->file('image')->store('homepage-photos', 'public');
        }

        return $data;
    }
}
