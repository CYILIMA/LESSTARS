<?php

namespace App\Http\Controllers;

use App\Models\AttendanceSession;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(): View
    {
        return view('admin.attendance.index', [
            'sessions' => AttendanceSession::query()->latest('attendance_date')->withCount([
                'attendances as attended_count' => fn ($query) => $query->where('attended', true),
            ])->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.attendance.form', [
            'members' => User::query()->where('is_member_approved', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'attendance_date' => ['required', 'date'],
            'attended_member_ids' => ['nullable', 'array'],
            'attended_member_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $approvedMembers = User::query()->where('is_member_approved', true)->pluck('id');
        $attendedMemberIds = collect($data['attended_member_ids'] ?? [])->intersect($approvedMembers);

        DB::transaction(function () use ($data, $approvedMembers, $attendedMemberIds): void {
            $session = AttendanceSession::create($data);

            $session->attendances()->createMany($approvedMembers->map(fn (int $memberId): array => [
                'user_id' => $memberId,
                'attended' => $attendedMemberIds->contains($memberId),
            ])->all());
        });

        return redirect()->route('admin.attendance.index')->with('success', 'Attendance has been recorded.');
    }

    public function destroy(AttendanceSession $attendance): RedirectResponse
    {
        $attendance->delete();

        return back()->with('success', 'Attendance record removed.');
    }
}
