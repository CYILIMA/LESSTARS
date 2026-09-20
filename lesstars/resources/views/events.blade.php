<x-site title="Events — Les Stars Du Theatre">
    <section class="events-list">
        @forelse($events as $event)
            <article class="event-card {{ $loop->even ? 'is-alternate' : 'is-featured' }}">
                <div class="event-card-copy">
                    <a class="event-card-link" href="{{ route('events.show', $event) }}"><h1>{{ $event->title }}</h1></a>
                    <p class="event-meta">{{ $event->venue }} · {{ $event->starts_at->format('d M Y · H:i') }}@if($event->ends_at) - {{ $event->ends_at->format('d M Y · H:i') }}@endif</p>
                    <details class="event-author"><summary><span class="author-avatar">{{ Str::of($event->author_name ?: 'Les Stars')->explode(' ')->map(fn ($part) => Str::substr($part, 0, 1))->join('') }}</span><b>{{ $event->author_name ?: 'Les Stars Du Theatre' }}</b></summary><p>{{ $event->author_bio ?: 'A contributor to Les Stars Du Theatre.' }}</p></details>
                    <div class="event-introduction"><p>{{ $event->summary }}</p></div>
                    @if($event->body)<div class="event-body"><p>{{ $event->body }}</p></div>@endif
                </div>
                @if($event->thumbnail_url || $event->image_url)<a class="event-image-link" href="{{ route('events.show', $event) }}"><figure class="event-thumbnail"><img src="{{ $event->thumbnail_url ?: $event->image_url }}" alt="Image for {{ $event->title }}"></figure></a>@else<a class="event-image-link" href="{{ route('events.show', $event) }}"><div class="event-thumbnail-placeholder" aria-hidden="true">✦</div></a>@endif
            </article>
        @empty
            <p class="empty-events">No events have been published yet. Check back soon for upcoming stories and activities.</p>
        @endforelse
    </section>
</x-site>
