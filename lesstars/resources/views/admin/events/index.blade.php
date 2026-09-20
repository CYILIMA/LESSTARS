<x-admin.layout title="Events">
	<div class="admin-heading"><div><p class="eyebrow blue">CONTENT</p><h1>Events</h1></div><a class="button button-blue" href="{{ route('admin.events.create') }}">New event <span>→</span></a></div>
	<div class="admin-panel">
		@forelse($events as $event)
			<div class="admin-row"><div><b>{{ $event->title }}</b><span>{{ $event->published_at?->format('d M Y · H:i') ?? 'Draft' }} · {{ $event->venue }} · <em>{{ ucfirst($event->status) }}</em></span></div><div class="row-actions"><a href="{{ route('admin.events.edit', $event) }}">Edit</a><form method="POST" action="{{ route('admin.events.destroy', $event) }}">@csrf @method('DELETE')<button onclick="return confirm('Remove this event?')">Delete</button></form></div></div>
		@empty
			<p>No events have been created.</p>
		@endforelse
	</div>
	{{ $events->links() }}
</x-admin.layout>
