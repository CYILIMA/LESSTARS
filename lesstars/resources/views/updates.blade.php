<x-site title="Updates — Les Stars Du Theatre">
    <section class="updates-list">
        @forelse($updates as $update)
            <article class="update-card">
                <h1>{{ $update->title }}</h1>
                <p class="update-meta">LES STARS · {{ $update->published_at->format('d M Y · H:i') }}</p>
                @if($update->thumbnail_url)<figure class="update-thumbnail"><img src="{{ $update->thumbnail_url }}" alt="Thumbnail for {{ $update->title }}"></figure>@else<div class="update-thumbnail-placeholder" aria-hidden="true">✦</div>@endif
                <details class="update-author"><summary><span class="author-avatar">{{ Str::of($update->author_name ?: 'Les Stars')->explode(' ')->map(fn ($part) => Str::substr($part, 0, 1))->join('') }}</span><b>{{ $update->author_name ?: 'Les Stars Du Theatre' }}</b></summary><p>{{ $update->author_bio ?: 'A contributor to Les Stars Du Theatre.' }}</p></details>
                <div class="update-introduction"><p>{{ $update->summary }}</p></div>
                <div class="update-body"><p>{{ $update->body }}</p></div>
            </article>
        @empty
            <p class="empty-events">No updates have been published yet. Check back soon for fresh announcements.</p>
        @endforelse
    </section>
</x-site>
