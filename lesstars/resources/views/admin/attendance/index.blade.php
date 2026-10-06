<x-admin.layout title="Attendance">
    <div class="admin-heading"><div><p class="eyebrow blue">MEMBER STAR PORTAL</p><h1>Attendance</h1></div><a class="button button-blue" href="{{ route('admin.attendance.create') }}">Record attendance <span>→</span></a></div>
    <section class="admin-panel">
        <h2>Activity attendance records</h2>
        @forelse ($sessions as $session)
            <div class="admin-row"><div><b>{{ $session->title }}</b><span>{{ $session->attendance_date->format('d M Y') }} · {{ $session->attended_count }} attended</span></div><form method="POST" action="{{ route('admin.attendance.destroy', $session) }}">@csrf @method('DELETE')<button onclick="return confirm('Remove this attendance record?')">Remove</button></form></div>
        @empty
            <p>No attendance has been recorded yet.</p>
        @endforelse
    </section>
</x-admin.layout>
