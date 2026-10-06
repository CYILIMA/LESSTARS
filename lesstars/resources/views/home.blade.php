<x-site title="Les Stars Du Theatre — Stories that stay with you">
    <section class="foundation-hero">
        <div class="foundation-hero-copy">
            <p class="foundation-kicker">IMPANO NTIZIKAZIME</p>
            <h1>Empowering<br>voices through<br>theaters.</h1>
            <p class="foundation-intro">Les Stars Du Theatre creates radio, stage and community experiences that give every story a chance to be heard.</p>
            <div class="foundation-actions">
                <a class="foundation-button foundation-button-primary" href="{{ route('auth') }}">sign up/in</a>
                <a class="foundation-button foundation-button-secondary" href="{{ route('about') }}">Learn About Us</a>
            </div>
        </div>

        <div class="foundation-slider" data-homepage-slider>
            @forelse($homepagePhotos as $photo)
                <figure class="foundation-slide @if($loop->first) is-active @endif" data-homepage-slide aria-hidden="{{ $loop->first ? 'false' : 'true' }}">
                    <img src="{{ $photo->image_url }}" alt="{{ $photo->description }}">
                    <figcaption>{{ $photo->description }}</figcaption>
                </figure>
            @empty
                <figure class="foundation-slide is-active" data-homepage-slide aria-hidden="false">
                    <img src="https://images.unsplash.com/photo-1507924538820-ede94a04019d?auto=format&amp;fit=crop&amp;w=1200&amp;q=85" alt="Performer illuminated on a theatre stage">
                    <figcaption>Stories that connect our communities.</figcaption>
                </figure>
            @endforelse
            <button class="slider-control slider-previous" type="button" data-slider-previous aria-label="Previous photo">‹</button>
            <button class="slider-control slider-next" type="button" data-slider-next aria-label="Next photo">›</button>
            <div class="slider-dots" aria-label="Homepage photos"></div>
        </div>
    </section>

    <section class="home-events">
        <header class="home-events-title" data-scroll-reveal><h2>What's Going On In Theatres</h2></header>
        @php($featured = $events->shift())
        @if($featured)
            <div class="home-event-gallery">
                <a class="home-event-featured" href="{{ route('events.show', $featured) }}">
                    <span class="home-event-image-wrap">
                        @if($featured->thumbnail_url || $featured->image_url)
                            <img src="{{ $featured->thumbnail_url ?: $featured->image_url }}" alt="Image for {{ $featured->title }}">
                        @else
                            <span class="home-event-placeholder" aria-hidden="true">✦</span>
                        @endif
                    </span>
                    <span class="home-event-featured-content">
                        <span class="home-event-kicker">Latest event</span>
                        <span class="home-event-date">{{ $featured->starts_at->format('d M Y · H:i') }}</span>
                        <strong>{{ $featured->title }}</strong>
                        <span class="home-event-featured-meta">{{ $featured->venue }} <b class="home-event-view">View Event</b></span>
                    </span>
                </a>
                <div class="home-event-side-grid">
                    @foreach($events->take(6) as $event)
                        <a class="home-event-card" href="{{ route('events.show', $event) }}">
                            <span class="home-event-image-wrap">
                                @if($event->thumbnail_url || $event->image_url)
                                    <img src="{{ $event->thumbnail_url ?: $event->image_url }}" alt="Image for {{ $event->title }}">
                                @else
                                    <span class="home-event-placeholder" aria-hidden="true">✦</span>
                                @endif
                            </span>
                            <span class="home-event-content">
                                <span class="home-event-date">{{ $event->starts_at->format('d M Y · H:i') }}</span>
                                <strong>{{ $event->title }}</strong>
                                <span>{{ $event->venue }} <b class="home-event-view">View Event</b></span>
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
            @if($events->count() > 6)
                <a class="home-events-more" href="{{ route('events') }}">View More Events <span aria-hidden="true">→</span></a>
            @endif
        @else
            <p class="home-events-empty">No events have been published yet.</p>
        @endif
    </section>
    <section class="numbers"><div><b>30+</b><span>Years creating<br>with purpose</span></div><div><b>42</b><span>Original radio<br>productions</span></div><div><b>15K+</b><span>Audience members<br>reached yearly</span></div><div><b>27</b><span>Emerging artists<br>mentored</span></div></section>
</x-site>
