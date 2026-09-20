<x-admin.layout title="{{ $event->exists ? 'Edit event' : 'New event' }}">
	<div class="admin-heading"><div><p class="eyebrow blue">EVENTS</p><h1>{{ $event->exists ? 'Edit event' : 'Create event' }}</h1></div></div>
	<form class="admin-form" method="POST" enctype="multipart/form-data" action="{{ $event->exists ? route('admin.events.update', $event) : route('admin.events.store') }}">
		@csrf @if($event->exists) @method('PUT') @endif
		<label>Event title<input name="title" value="{{ old('title', $event->title) }}" required></label>
		<label>Short description<textarea name="summary" required>{{ old('summary', $event->summary) }}</textarea></label>
		<label>Full story<textarea name="body" rows="6">{{ old('body', $event->body) }}</textarea></label>
		<div class="form-grid"><label>Venue<input name="venue" value="{{ old('venue', $event->venue) }}" required></label><label>Author name<input name="author_name" value="{{ old('author_name', $event->author_name ?? auth()->user()?->name) }}"></label><label>Starts at<input type="datetime-local" name="starts_at" value="{{ old('starts_at', optional($event->starts_at)->format('Y-m-d\TH:i')) }}" required></label><label>Ends at<input type="datetime-local" name="ends_at" value="{{ old('ends_at', optional($event->ends_at)->format('Y-m-d\TH:i')) }}"></label></div>
		<label>Author bio<textarea name="author_bio" rows="4">{{ old('author_bio', $event->author_bio) }}</textarea></label>
		<div class="thumbnail-editor" data-thumbnail-editor>
			<span class="thumbnail-editor-label">Event thumbnail</span>
			<div class="thumbnail-preview-wrap">
				@if($event->thumbnail_url)
					<img class="thumbnail-preview" data-thumbnail-preview src="{{ $event->thumbnail_url }}" alt="Current thumbnail for {{ $event->title }}">
				@else
					<div class="thumbnail-preview thumbnail-preview-empty" data-thumbnail-preview aria-hidden="true">No image selected</div>
				@endif
			</div>
			<label class="thumbnail-picker" for="event-thumbnail">Choose a new thumbnail</label>
			<input id="event-thumbnail" class="thumbnail-input" data-thumbnail-input type="file" name="thumbnail" accept="image/jpeg,image/png,image/webp">
			<p class="thumbnail-selected" data-thumbnail-selected>No new image selected</p>
			<small>Choose a JPEG, PNG or WebP image up to 4 MB. Saving the event will replace the current thumbnail.</small>
			@if($event->thumbnail_url)<small>Current image: <a href="{{ $event->thumbnail_url }}" target="_blank" rel="noopener">open full image</a></small>@endif
			@error('thumbnail')<p class="form-error">{{ $message }}</p>@enderror
		</div>
		<label>Image URL (optional)<input type="url" name="image_url" value="{{ old('image_url', $event->image_url) }}"></label>
		<div class="form-grid"><label>Publication date<input type="datetime-local" name="published_at" value="{{ old('published_at', $event->published_at?->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i')) }}"></label><label>Visibility<select name="status"><option value="draft" @selected(old('status', $event->status) === 'draft')>Draft</option><option value="published" @selected(old('status', $event->status) === 'published')>Published</option></select></label></div>
		<button class="button button-blue" type="submit">{{ $event->exists ? 'Save changes' : 'Publish event' }} <span>→</span></button>
	</form>
</x-admin.layout>
