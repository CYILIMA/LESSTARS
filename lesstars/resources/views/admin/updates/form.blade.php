<x-admin.layout title="{{ $update->exists ? 'Edit update' : 'New update' }}">
    <div class="admin-heading"><div><p class="eyebrow blue">PUBLISH</p><h1>{{ $update->exists ? 'Edit update' : 'Create update' }}</h1></div></div>
    <form class="admin-form" method="POST" enctype="multipart/form-data" action="{{ $update->exists ? route('admin.updates.update', $update) : route('admin.updates.store') }}">
        @csrf @if($update->exists) @method('PUT') @endif
        <label>Title<input type="text" name="title" value="{{ old('title', $update->title) }}" required></label>
        <label>Summary<input type="text" name="summary" value="{{ old('summary', $update->summary) }}" required></label>
        <label>Body<textarea name="body" rows="6" required>{{ old('body', $update->body) }}</textarea></label>
        <div class="form-grid"><label>Author name<input name="author_name" value="{{ old('author_name', $update->author_name ?? auth()->user()?->name) }}" placeholder="Defaults to your admin name"></label><label>Publication date<input type="datetime-local" name="published_at" value="{{ old('published_at', $update->published_at?->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i')) }}"></label></div>
        <label>Author bio<textarea name="author_bio" rows="4" placeholder="Shown when readers click the author's name">{{ old('author_bio', $update->author_bio) }}</textarea></label>
        <label>Update thumbnail<input type="file" name="thumbnail" accept="image/jpeg,image/png,image/webp"><small>JPEG, PNG or WebP; maximum 4 MB.</small>@if($update->thumbnail_url)<small>Current thumbnail: <a href="{{ $update->thumbnail_url }}" target="_blank" rel="noopener">view image</a></small>@endif</label>
        <label>Visibility<select name="status"><option value="draft" @selected(old('status', $update->status) === 'draft')>Draft</option><option value="published" @selected(old('status', $update->status) === 'published')>Published</option></select></label>
        <button class="button button-blue" type="submit">Save update <span>→</span></button>
    </form>
</x-admin.layout>
