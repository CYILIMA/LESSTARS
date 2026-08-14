<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Les Stars Du Theatre' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <header class="site-header">
        <a class="brand" href="{{ route('home') }}" aria-label="Les Stars Du Theatre home">
            @php
                $logoPath = optional(App\Models\Setting::where('key', 'organisation_logo_path')->first())->value;
            @endphp
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
            <a class="{{ request()->routeIs('team') ? 'active' : '' }}" href="{{ route('team') }}">Our Team</a>
            <a class="{{ request()->routeIs('activities') ? 'active' : '' }}" href="{{ route('activities') }}">Activities</a>
            <a class="{{ request()->routeIs('updates') ? 'active' : '' }}" href="{{ route('updates') }}">Updates</a>
            <a class="nav-donate" href="{{ route('donate') }}">Donate <span>↗</span></a>
        </nav>
    </header>
    <main>{{ $slot }}</main>
    <footer>
        <div class="footer-top">
            <a class="brand footer-brand" href="{{ route('home') }}">
                @php
                    $footerLogoPath = optional(App\Models\Setting::where('key', 'organisation_logo_path')->first())->value;
                @endphp
                @if ($footerLogoPath && Storage::disk('public')->exists($footerLogoPath))
                    <img class="official-logo" src="{{ Storage::url($footerLogoPath) }}" alt="Organisation logo">
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
