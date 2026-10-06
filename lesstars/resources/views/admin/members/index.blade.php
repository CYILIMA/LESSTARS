<x-admin.layout title="Member Stars">
    <div class="admin-heading"><div><p class="eyebrow blue">MEMBER ACCESS</p><h1>Member Stars</h1></div><a class="button button-blue" href="{{ route('admin.members.create') }}">Add a member <span>→</span></a></div>
    <section class="admin-panel">
        <h2>Membership approvals</h2>
        @forelse ($members as $member)
            <div class="admin-row"><div><b>{{ $member->name }}</b><span>{{ $member->email }} · Reg. {{ $member->registration_number }} · {{ $member->is_member_approved ? 'Approved' : 'Not approved' }}</span></div><div class="row-actions"><a href="{{ route('admin.members.edit', $member) }}">Edit</a><form method="POST" action="{{ route('admin.members.destroy', $member) }}">@csrf @method('DELETE')<button onclick="return confirm('Remove this member record?')">Remove</button></form></div></div>
        @empty
            <p>No Member Stars have been added yet.</p>
        @endforelse
    </section>
</x-admin.layout>
