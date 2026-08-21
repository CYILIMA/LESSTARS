<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $title ?? 'Admin' }} · Les Stars</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-body">
    <aside class="admin-side">
        @php($logoPath = App\Models\Setting::where('key', 'organisation_logo_path')->value('value'))
        <a class="brand" href="{{ route('admin.dashboard') }}">
            @if ($logoPath && Storage::disk('public')->exists($logoPath))
                <img class="official-logo" src="{{ Storage::url($logoPath) }}" alt="Les Stars Du Theatre logo">
            @elseif (file_exists(public_path('images/les-stars-logo.png')))
                <img class="official-logo" src="{{ asset('images/les-stars-logo.png') }}" alt="Les Stars Du Theatre logo">
            @else
                <span class="brand-mark">✦</span>
            @endif
            <span>LES STARS<br><i>ADMIN</i></span>
        </a>
        <nav>
            <a href="{{ route('admin.dashboard') }}">Overview</a>
            <a href="{{ route('admin.events.index') }}">Events</a>
            <a href="{{ route('admin.theatres.index') }}">Theatre audio</a>
            <a href="{{ route('admin.theatre-comments.index') }}">Theatre comments</a>
            <a href="{{ route('admin.updates.index') }}">Updates</a>
            <a href="{{ route('admin.team-members.index') }}">Team members</a>
            <a href="{{ route('admin.homepage-photos.index') }}">Homepage photos</a>
            <a href="{{ route('admin.settings') }}">Organisation settings</a>
            <a href="{{ route('home') }}" target="_blank">View public site ↗</a>
        </nav>
        <form method="POST" action="{{ route('admin.logout') }}">@csrf<button>Sign out</button></form>
    </aside>
    <section class="admin-main">
        @if(session('success'))<div class="notice">{{ session('success') }}</div>@endif
        {{ $slot }}
    </section>
</body>
</html>
