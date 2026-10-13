<x-site title="Members — Les Stars Du Theatre">
    <section class="auth-page member-login-page">
        <div class="auth-intro">
            <p class="eyebrow">MEMBER STAR PORTAL</p>
            <h1>Welcome,<br><em>Star.</em></h1>
            <p>Confirm your approved Member Star credentials to open your dashboard.</p>
        </div>
        <form class="auth-form" method="POST" action="{{ route('members.login.store') }}">
            @csrf
            <p class="eyebrow blue">MEMBER ACCESS</p>
            <h2>Enter dashboard</h2>
            <label><span class="field-label">Email address</span><input type="email" name="member_email" value="{{ old('member_email', auth()->user()->email) }}" autocomplete="email" required autofocus></label>
            <label><span class="field-label">Registration number</span><input type="text" name="registration_number" value="{{ old('registration_number') }}" placeholder="e.g. LST-2026-001" autocomplete="off" required></label>
            @error('member_email')<p class="form-error">{{ $message }}</p>@enderror
            @error('registration_number')<p class="form-error">{{ $message }}</p>@enderror
            <button class="button button-blue" type="submit">Enter member dashboard <span>→</span></button>
        </form>
    </section>
</x-site>
