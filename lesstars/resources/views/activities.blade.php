<x-site title="Activities — Les Stars Du Theatre">
    <section class="page-hero compact"><p class="eyebrow blue">OUR ACTIVITIES</p>
    <h1>Where stories<br><em>come alive.</em></h1>
    <p>From a quiet pair of headphones to a full room at curtain-up, our work is made to travel.</p>
</section><section class="activity-list">
<article><span>01</span>
    <div><p class="eyebrow blue">ON AIR</p><h2>Radio Theatre

    </h2><p>Original audio dramas, created for the intimacy of listening. Each season pairs distinctive writers with a cast and sound world designed to linger.
    </p>
</div><a href="{{ route('donate') }}">Listen in ↗</a></article><article><span>02</span>
<div><p class="eyebrow blue">IN THE ROOM</p><h2>Live Shows</h2>
<p>Vivid, close-up performances staged in theatres and unexpected places.</p>
</div><a href="{{ route('donate') }}">Support us ↗</a>
</article>
</section>
<section class="upcoming"><p class="eyebrow blue">UPCOMING EVENTS</p><h2>See us next.</h2>@forelse($events as $event)
<article><time datetime="{{ $event->starts_at->toDateString() }}">{{ $event->starts_at->format('d M Y · H:i') }}</time>
<div><h3>{{ $event->title }}</h3><p>{{ $event->venue }}</p></div>
<a href="{{ route('donate') }}">Details ↗</a></article>@empty<p class="empty-events">New events will be announced here soon. Follow us on social media to be first in the room.</p>@endforelse</section>
</x-site>
