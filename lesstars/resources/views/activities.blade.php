<x-site title="Activities — Les Stars Du Theatre">
    <section class="theatre-library">
        <h1>LISTEN OUR THEATRES</h1>

        <div class="theatre-list">
            @forelse ($theatres as $theatre)
                <a class="theatre-list-item" href="{{ route('theatres.show', $theatre) }}">
                    @if ($theatre->thumbnail_url)
                        <img src="{{ $theatre->thumbnail_url }}" alt="{{ $theatre->title }} thumbnail">
                    @else
                        <div class="theatre-placeholder" aria-hidden="true">♫</div>
                    @endif
                    <div>
                        <span>Theatre audio</span>
                        <h3>{{ $theatre->title }}</h3>
                    </div>
                    <b aria-hidden="true">→</b>
                </a>
            @empty
                <p class="empty-events">Theatre productions will be added here soon.</p>
            @endforelse
        </div>
    </section>
</x-site>
