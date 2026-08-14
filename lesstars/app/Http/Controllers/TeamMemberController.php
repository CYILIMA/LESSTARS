<?php

namespace App\Http\Controllers;

use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeamMemberController extends Controller
{
    public function index()
    {
        return view('admin.team-members.index', [
            'teamMembers' => TeamMember::orderBy('display_order')->orderBy('name')->paginate(12),
        ]);
    }

    public function create()
    {
        return view('admin.team-members.form', ['teamMember' => new TeamMember]);
    }

    public function store(Request $request)
    {
        TeamMember::create($this->dataWithProfileImage($request));

        return redirect()->route('admin.team-members.index')->with('success', 'Team member added.');
    }

    public function edit(TeamMember $teamMember)
    {
        return view('admin.team-members.form', compact('teamMember'));
    }

    public function update(Request $request, TeamMember $teamMember)
    {
        $teamMember->update($this->dataWithProfileImage($request, $teamMember));

        return redirect()->route('admin.team-members.index')->with('success', 'Team member updated.');
    }

    public function destroy(TeamMember $teamMember)
    {
        $this->deleteStoredProfileImage($teamMember);
        $teamMember->delete();

        return back()->with('success', 'Team member removed.');
    }

    /** @return array<string, mixed> */
    private function dataWithProfileImage(Request $request, ?TeamMember $teamMember = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'role' => ['required', 'string', 'max:120'],
            'bio' => ['required', 'string', 'max:1500'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'instagram_url' => ['nullable', 'url', 'max:2048'],
            'linkedin_url' => ['nullable', 'url', 'max:2048'],
            'display_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'status' => ['required', 'in:draft,published'],
        ]);

        $data['display_order'] ??= 0;
        unset($data['image']);

        if ($request->hasFile('image')) {
            if ($teamMember) {
                $this->deleteStoredProfileImage($teamMember);
            }

            $data['image_url'] = $request->file('image')->store('team-members', 'public');
        }

        return $data;
    }

    private function deleteStoredProfileImage(TeamMember $teamMember): void
    {
        $imagePath = $teamMember->getRawOriginal('image_url');

        if ($imagePath && ! str_starts_with($imagePath, 'http://') && ! str_starts_with($imagePath, 'https://')) {
            Storage::disk('public')->delete($imagePath);
        }
    }
}
