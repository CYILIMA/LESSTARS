<x-site title="Les Stars Du Theatre — Stories that stay with you">
    <section class="foundation-hero">
        <div class="foundation-hero-copy">
            <p class="foundation-kicker">IMPANO NTIZIKAZIME</p>
            <h1>Empowering<br>voices through<br>theaters.</h1>
            <p class="foundation-intro">Les Stars Du Theatre creates radio, stage and community experiences that give every story a chance to be heard.</p>
            <div class="foundation-actions">
                <a class="foundation-button foundation-button-primary" href="{{ route('donate') }}">Donate Now</a>
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

    <section class="statement"><p class="eyebrow blue">WHO WE ARE</p><div><h2>We believe every voice deserves <em>a stage.</em></h2>
    <p>Les Stars Du Theatre is a creative home for brave stories. We bring together artists, listeners and audiences through original radio plays, intimate live shows and workshops.</p><a class="arrow-link" href="{{ route('about') }}">Our story, in full <span>→</span></a></div>
    </section>
    <section class="numbers"><div><b>08</b><span>Years creating<br>with purpose</span></div><div><b>42</b><span>Original radio<br>productions</span></div><div><b>15K+</b><span>Audience members<br>reached yearly</span></div><div><b>27</b><span>Emerging artists<br>mentored</span></div></section>
</x-site>
