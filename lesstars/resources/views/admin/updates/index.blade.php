<x-admin.layout title="Updates">
    <div class="admin-heading">
        <div>
            <p class="eyebrow blue">CONTENT</p>
            <h1>Updates</h1>
        </div>
        <a class="button button-blue" href="{{ route('admin.updates.create') }}">Create update <span>→</span></a>
    </div>

    <div class="admin-panel">
        @forelse($updates as $update)
            <div class="admin-row">
                <div>
                    <b>{{ $update->title }}</b>
                    <span>{{ $update->published_at?->format('d M Y · H:i') ?? 'Draft' }} · {{ ucfirst($update->status) }}</span>
                </div>
                <div class="admin-actions">
                    <a href="{{ route('admin.updates.edit', $update) }}">Edit →</a>
                    <form method="POST" action="{{ route('admin.updates.destroy', $update) }}" onsubmit="return confirm('Remove this update?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <p>No updates yet. Publish your first announcement here.</p>
        @endforelse
    </div>
</x-admin.layout>
