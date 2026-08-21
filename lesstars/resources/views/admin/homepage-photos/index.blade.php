<x-admin.layout title="Homepage photos">
    <div class="admin-heading"><div><p class="eyebrow blue">HOMEPAGE</p><h1>Homepage photos</h1></div><a class="button button-blue" href="{{ route('admin.homepage-photos.create') }}">Add photo <span>→</span></a></div>
    <div class="admin-panel">
        <p class="admin-panel-note">Published photos appear on the homepage one at a time and rotate from right to left every 3 seconds.</p>
        @forelse($photos as $photo)
            <div class="admin-row homepage-photo-row"><img src="{{ $photo->image_url }}" alt=""><div><b>{{ $photo->description }}</b><span>Position {{ $photo->display_order }} · {{ ucfirst($photo->status) }}</span></div><div class="row-actions"><a href="{{ route('admin.homepage-photos.edit', $photo) }}">Edit</a><form method="POST" action="{{ route('admin.homepage-photos.destroy', $photo) }}">@csrf @method('DELETE')<button onclick="return confirm('Remove this homepage photo?')">Delete</button></form></div></div>
        @empty
            <p>No homepage photos have been added yet.</p>
        @endforelse
    </div>
    {{ $photos->links() }}
</x-admin.layout>
