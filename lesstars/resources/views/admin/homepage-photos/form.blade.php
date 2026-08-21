<x-admin.layout title="{{ $homepagePhoto->exists ? 'Edit homepage photo' : 'Add homepage photo' }}">
    <div class="admin-heading"><div><p class="eyebrow blue">HOMEPAGE</p><h1>{{ $homepagePhoto->exists ? 'Edit photo' : 'Add photo' }}</h1></div></div>
    <form class="admin-form" method="POST" enctype="multipart/form-data" action="{{ $homepagePhoto->exists ? route('admin.homepage-photos.update', $homepagePhoto) : route('admin.homepage-photos.store') }}">
        @csrf
        @if($homepagePhoto->exists) @method('PUT') @endif
        <label>Photo<input type="file" name="image" accept="image/jpeg,image/png,image/webp" @if(! $homepagePhoto->exists) required @endif><small>JPEG, PNG or WebP; maximum 4 MB.</small>@if($homepagePhoto->exists)<small>Leave blank to keep the current photo.</small>@endif</label>
        <label>Short description<textarea name="description" maxlength="180" required>{{ old('description', $homepagePhoto->description) }}</textarea></label>
        <div class="form-grid"><label>Display order<input type="number" min="0" name="display_order" value="{{ old('display_order', $homepagePhoto->display_order ?? 0) }}"></label><label>Visibility<select name="status"><option value="draft" @selected(old('status', $homepagePhoto->status) === 'draft')>Draft — hidden from the website</option><option value="published" @selected(old('status', $homepagePhoto->status) === 'published')>Published — visible on the website</option></select></label></div>
        <button class="button button-blue" type="submit">{{ $homepagePhoto->exists ? 'Save changes' : 'Add homepage photo' }} <span>→</span></button>
    </form>
</x-admin.layout>
