<?php

use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\User;

test('a member sees their attended and absent activities on the calendar', function (): void {
    $member = User::factory()->create([
        'registration_number' => 'STAR-100',
        'is_member_approved' => true,
    ]);
    $attendedSession = AttendanceSession::create(['title' => 'Weekly rehearsal', 'attendance_date' => now()->startOfMonth()->addDays(2)]);
    $absentSession = AttendanceSession::create(['title' => 'Costume workshop', 'attendance_date' => now()->startOfMonth()->addDays(5)]);
    Attendance::create(['attendance_session_id' => $attendedSession->id, 'user_id' => $member->id, 'attended' => true]);
    Attendance::create(['attendance_session_id' => $absentSession->id, 'user_id' => $member->id, 'attended' => false]);

    $this->actingAs($member)->get(route('members.dashboard'))
        ->assertOk()
        ->assertSee('Weekly rehearsal')
        ->assertSee('Costume workshop')
        ->assertSee('50%');
});
