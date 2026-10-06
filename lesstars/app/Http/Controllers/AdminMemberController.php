<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminMemberController extends Controller
{
    public function index(): View
    {
        return view('admin.members.index', [
            'members' => User::query()->whereNotNull('registration_number')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.members.form', ['member' => new User]);
    }

    public function store(Request $request): RedirectResponse
    {
        User::create($this->validatedMember($request) + ['password' => Str::random(40)]);

        return redirect()->route('admin.members.index')->with('success', 'Member Star added. Approve them when their membership is current.');
    }

    public function edit(User $member): View
    {
        return view('admin.members.form', compact('member'));
    }

    public function update(Request $request, User $member): RedirectResponse
    {
        $member->update($this->validatedMember($request, $member));

        return redirect()->route('admin.members.index')->with('success', 'Member Star updated.');
    }

    public function destroy(User $member): RedirectResponse
    {
        $member->delete();

        return redirect()->route('admin.members.index')->with('success', 'Member Star removed.');
    }

    /** @return array<string, mixed> */
    private function validatedMember(Request $request, ?User $member = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'.($member ? ','.$member->id : '')],
            'registration_number' => ['required', 'string', 'max:255', 'unique:users,registration_number'.($member ? ','.$member->id : '')],
            'is_member_approved' => ['nullable', 'boolean'],
        ]) + ['is_member_approved' => $request->boolean('is_member_approved')];
    }
}
