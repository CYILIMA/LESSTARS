@php
    $aboutContent = $settings['about_content'] ?? 'Les Stars Du Theatre began in 2016 with a simple question: what if thoughtful, moving theatre could travel beyond the theatre building? We started making radio plays with local artists and soon found listeners everywhere.';
    $mission = $settings['mission_statement'] ?? 'We use the intimacy of radio and the electricity of the stage to tell stories that belong to all of us.';
    $vision = $settings['vision_statement'] ?? 'A creative ecosystem where bold African storytelling reaches audiences wherever they are.';
@endphp

<x-site title="About — Les Stars Du Theatre">
    <section class="about-sections">
        <article class="about-panel about-panel-white">
            <p class="eyebrow blue">ABOUT LES STARS DU THEATRE</p>
            <h2>Our story</h2>
            <p>{!! nl2br(e($aboutContent)) !!}</p>
        </article>
        <article class="about-panel about-panel-green">
            <p class="eyebrow">OUR MISSION</p>
            <h2>Our mission</h2>
            <p>{!! nl2br(e($mission)) !!}</p>
        </article>
        <article class="about-panel about-panel-black">
            <p class="eyebrow">OUR VISION</p>
            <h2>Our vision</h2>
            <p>{!! nl2br(e($vision)) !!}</p>
        </article>
    </section>
</x-site>
