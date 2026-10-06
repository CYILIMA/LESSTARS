<x-admin.layout title="Cacahuète">
    <div class="admin-heading"><div><p class="eyebrow blue">GIFT EXCHANGE</p><h1>Cacahuète</h1></div><a class="button button-blue" href="{{ route('admin.cacahuete.create') }}">Create game <span>→</span></a></div>
    <section class="admin-panel">
        <h2>Gift-draw games</h2>
        @forelse($games as $game)
            <div class="admin-row"><div><b>{{ $game->title }}</b><span>{{ $game->participants_count }} players · {{ $game->is_active ? 'Open for picks' : 'Closed' }}</span></div><div class="row-actions"><form method="POST" action="{{ route('admin.cacahuete.toggle', $game) }}">@csrf @method('PATCH')<button>{{ $game->is_active ? 'Close game' : 'Open game' }}</button></form><form method="POST" action="{{ route('admin.cacahuete.destroy', $game) }}">@csrf @method('DELETE')<button onclick="return confirm('Delete this game and every private assignment?')">Delete</button></form></div></div>
        @empty
            <p>No Cacahuète game has been created yet.</p>
        @endforelse
    </section>
</x-admin.layout>
