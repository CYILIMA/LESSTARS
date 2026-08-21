<x-site title="Our Team — Les Stars Du Theatre">
    <section class="team-category-directory">
        <p class="eyebrow blue">OUR TEAM</p>
        <h1>Choose a<br><em>team.</em></h1>
        <p>Explore the people who lead, create, and have shaped Les Stars Du Theatre.</p>
        <div class="team-category-grid">
            @foreach (App\Models\TeamMember::CATEGORIES as $category => $label)
                <a href="{{ route('team.category', $category) }}">{{ $label }} <span>→</span></a>
            @endforeach
        </div>
    </section>
</x-site>
