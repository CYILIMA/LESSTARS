<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Les Stars Du Theatre' }}</title>
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $socialTitle ?? $title ?? 'Les Stars Du Theatre' }}">
    @if (! empty($socialDescription))<meta property="og:description" content="{{ $socialDescription }}">@endif
    @if (! empty($socialImage))<meta property="og:image" content="{{ $socialImage }}">@endif
    <meta name="twitter:card" content="{{ ! empty($socialImage) ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $socialTitle ?? $title ?? 'Les Stars Du Theatre' }}">
    @if (! empty($socialDescription))<meta name="twitter:description" content="{{ $socialDescription }}">@endif
    @if (! empty($socialImage))<meta name="twitter:image" content="{{ $socialImage }}">@endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
@php
    $logoPath = App\Models\Setting::where('key', 'organisation_logo_path')->value('value');
    $backgroundPath = App\Models\Setting::where('key', 'site_background_path')->value('value');
    $backgroundImage = $backgroundPath && Storage::disk('public')->exists($backgroundPath) ? "url('".Storage::url($backgroundPath)."')" : 'none';
@endphp
<body class="site-body" style="--site-background-image: {{ $backgroundImage }}">
    <header class="site-header">
        <a class="brand" href="{{ route('home') }}" aria-label="Les Stars Du Theatre home">
            @if ($logoPath && Storage::disk('public')->exists($logoPath))
                <img class="official-logo" src="{{ Storage::url($logoPath) }}" alt="Organisation logo">
            @elseif (file_exists(public_path('images/les-stars-logo.png')))
                <img class="official-logo" src="{{ asset('images/les-stars-logo.png') }}" alt="Les Stars Du Theatre logo">
            @else
                <span class="brand-mark">✦</span>
            @endif
            <span>LES STARS<br><i>DU THÉÂTRE</i></span>
        </a>
        <button class="menu-button" aria-label="Open menu" aria-expanded="false"><span></span><span></span></button>
        <nav class="nav-links">
            <a class="{{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
            <a class="{{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">About</a>
            <div class="nav-dropdown {{ request()->routeIs('team*') ? 'active' : '' }}">
                <button class="nav-dropdown-trigger" type="button" aria-haspopup="true" aria-expanded="false" aria-controls="team-menu">Our Team <span aria-hidden="true">⌄</span></button>
                <div class="nav-dropdown-menu" id="team-menu">
                    @foreach (App\Models\TeamMember::CATEGORIES as $category => $label)
                        <a class="{{ request()->routeIs('team.category') && request()->route('category') === $category ? 'active' : '' }}" href="{{ route('team.category', $category) }}">{{ $label }}</a>
                    @endforeach
                </div>
            </div>
            <a class="{{ request()->routeIs('activities') ? 'active' : '' }}" href="{{ route('activities') }}">Activities</a>
            <a class="{{ request()->routeIs('updates') ? 'active' : '' }}" href="{{ route('updates') }}">Updates</a>
            <a class="nav-donate" href="{{ route('donate') }}">Donate <span>↗</span></a>
        </nav>
    </header>
    <main>{{ $slot }}</main>
    <footer>
        <div class="footer-top">
            <a class="brand footer-brand" href="{{ route('home') }}">
                @if ($logoPath && Storage::disk('public')->exists($logoPath))
                    <img class="official-logo" src="{{ Storage::url($logoPath) }}" alt="Organisation logo">
                @elseif (file_exists(public_path('images/les-stars-logo.png')))
                    <img class="official-logo" src="{{ asset('images/les-stars-logo.png') }}" alt="Les Stars Du Theatre logo">
                @else
                    <span class="brand-mark">✦</span>
                @endif
                <span>LES STARS<br><i>DU THÉÂTRE</i></span>
            </a>
            <p>Stories in the air.<br>People in the room.</p>
            <div class="socials"><a href="#">Instagram</a><a href="#">Facebook</a><a href="#">YouTube</a></div>
        </div>
        <div class="footer-bottom"><span>© 2026 LES STARS DU THÉÂTRE</span><span>MADE WITH PURPOSE IN SOUTH AFRICA</span><a href="mailto:hello@lesstars.org">hello@lesstars.org ↗</a></div>
    </footer>
</body>
</html>
