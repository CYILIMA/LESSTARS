<x-admin.layout title="Website Sign-ups">
    <div class="admin-heading"><div><p class="eyebrow blue">PUBLIC ACCOUNTS</p><h1>Website sign-ups</h1></div></div>
    <section class="admin-panel">
        <h2>Registered users</h2>
        @forelse($users as $user)
            <div class="admin-row"><div><b>{{ $user->name }}</b><span>{{ $user->email }} · Joined {{ $user->created_at->format('d M Y') }}</span></div></div>
        @empty
            <p>No normal website users have signed up yet.</p>
        @endforelse
    </section>
    {{ $users->links() }}
</x-admin.layout>
