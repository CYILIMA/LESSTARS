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
    $socialUrls = App\Models\Setting::whereIn('key', ['facebook_url', 'instagram_url', 'twitter_url', 'youtube_url', 'whatsapp_url'])->pluck('value', 'key');
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
            <a class="{{ request()->routeIs('events') ? 'active' : '' }}" href="{{ route('events') }}">Events</a>
            <a class="{{ request()->routeIs('members.*') ? 'active' : '' }}" href="{{ route('members.login') }}">Members</a>
            <a class="nav-donate" href="{{ route('auth') }}">sign up/in <span>↗</span></a>
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
            <div class="socials">
                @if (!empty($socialUrls['facebook_url']))
                    <a href="{{ $socialUrls['facebook_url'] }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                @endif
                @if (!empty($socialUrls['instagram_url']))
                    <a href="{{ $socialUrls['instagram_url'] }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    </a>
                @endif
                @if (!empty($socialUrls['twitter_url']))
                    <a href="{{ $socialUrls['twitter_url'] }}" target="_blank" rel="noopener noreferrer" aria-label="X (Twitter)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>
                @endif
                @if (!empty($socialUrls['youtube_url']))
                    <a href="{{ $socialUrls['youtube_url'] }}" target="_blank" rel="noopener noreferrer" aria-label="YouTube">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                    </a>
                @endif
                @if (!empty($socialUrls['whatsapp_url']))
                    <a href="{{ $socialUrls['whatsapp_url'] }}" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    </a>
                @endif
            </div>
        </div>
        <div class="footer-bottom"><span>© 2026 LES STARS DU THÉÂTRE</span><a href="mailto:hello@lesstars.org">hello@lesstars.org ↗</a></div>
    </footer>
</body>
</html>
