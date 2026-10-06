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
        <nav>
            <a href="{{ route('admin.dashboard') }}">Overview</a>
            <a href="{{ route('admin.events.index') }}">Events</a>
            <a href="{{ route('admin.theatres.index') }}">Theatre audio</a>
            <a href="{{ route('admin.theatre-comments.index') }}">Theatre comments</a>
            <a href="{{ route('admin.team-members.index') }}">Team members</a>
            <a href="{{ route('admin.homepage-photos.index') }}">Homepage photos</a>
            <a href="{{ route('admin.members.index') }}">Member Stars</a>
            <a href="{{ route('admin.registered-users.index') }}">Website sign-ups</a>
            <a href="{{ route('admin.membership-applications.index') }}">Member applications</a>
            <a href="{{ route('admin.attendance.index') }}">Attendance</a>
            <a href="{{ route('admin.contributions.index') }}">Member wallet</a>
            <a href="{{ route('admin.cacahuete.index') }}">Cacahuète</a>
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
