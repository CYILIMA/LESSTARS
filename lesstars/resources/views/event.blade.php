<x-site title="{{ $event->title }} — Les Stars Du Theatre">
    <article class="event-detail">
        <a class="event-back-link" href="{{ route('events') }}">← All events</a>
        <p class="eyebrow blue">{{ $event->venue }} · {{ $event->starts_at->format('d M Y · H:i') }}</p>
        <h1>{{ $event->title }}</h1>
        <p class="event-detail-summary">{{ $event->summary }}</p>

        @if($event->thumbnail_url || $event->image_url)
            <figure class="event-detail-image"><img src="{{ $event->thumbnail_url ?: $event->image_url }}" alt="Image for {{ $event->title }}"></figure>
        @endif

        <div class="event-detail-grid">
            <div>
                @if($event->body)<div class="event-detail-body"><p>{{ $event->body }}</p></div>@endif
                @if($event->author_name)<p class="event-detail-author">Written by <strong>{{ $event->author_name }}</strong></p>@endif
            </div>
        </div>
    </article>
</x-site>
