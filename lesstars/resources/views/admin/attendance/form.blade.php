<x-admin.layout title="Record Attendance">
    <div class="admin-heading"><div><p class="eyebrow blue">MEMBER STAR PORTAL</p><h1>Record attendance</h1></div></div>
    <form class="admin-form" method="POST" action="{{ route('admin.attendance.store') }}">
        @csrf
        <label>Activity name<input name="title" value="{{ old('title') }}" placeholder="e.g. Weekly rehearsal" required></label>
        <label>Attendance date<input type="date" name="attendance_date" value="{{ old('attendance_date', now()->toDateString()) }}" required></label>
        <fieldset class="attendance-member-list"><legend>Mark the members who attended</legend><p>Anyone left unchecked is recorded as absent for this activity.</p>
            @forelse($members as $member)<label><input type="checkbox" name="attended_member_ids[]" value="{{ $member->id }}" @checked(in_array($member->id, old('attended_member_ids', [])))><span><b>{{ $member->name }}</b><small>{{ $member->registration_number }}</small></span></label>@empty<p>No approved Member Stars are available. Add and approve members first.</p>@endforelse
        </fieldset>
        @if($errors->any())<p class="form-error">{{ $errors->first() }}</p>@endif
        <button class="button button-blue" type="submit">Save attendance <span>→</span></button>
    </form>
</x-admin.layout>
