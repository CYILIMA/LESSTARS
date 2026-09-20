<x-site title="{{ $mode === 'login' ? 'Sign in' : 'Sign up' }} — Les Stars Du Theatre">
    <section class="auth-page">
        <div class="auth-intro">
            <p class="eyebrow">JOIN LES STARS</p>
            <h1>
                @if ($mode === 'login')
                    Welcome<br><em>back.</em>
                @else
                    Join our big community!
                @endif
            </h1>
            <p>{{ $mode === 'login' ? 'Sign in to continue your connection with Les Stars Du Theatre.' : 'Create your account to stay close to Les Stars Du Theatre, our activities, and our community.' }}</p>
        </div>

        @if ($mode === 'login')
            <form class="auth-form" method="POST" action="{{ route('login') }}">
                @csrf
                <p class="eyebrow blue">WELCOME BACK</p>
                <h2>Sign in</h2>
                <label>Email address<input type="email" name="login_email" value="{{ old('login_email') }}" autocomplete="email" required autofocus></label>
                <label>Password<input type="password" name="password" autocomplete="current-password" required></label>
                <label class="remember"><input type="checkbox" name="remember"> Remember me</label>
                @error('login_email')<p class="form-error">{{ $message }}</p>@enderror
                <button class="button button-blue" type="submit">Sign in <span>→</span></button>
                <p class="auth-switch">New to Les Stars? <a href="{{ route('auth') }}">Create an account</a></p>
            </form>
        @else
            <form class="auth-form" method="POST" action="{{ route('register') }}">
                @csrf

                <h2>Sign up</h2>
                <label>Full name<input name="name" value="{{ old('name') }}" autocomplete="name" required autofocus></label>
                <label>Email address<input type="email" name="register_email" value="{{ old('register_email') }}" autocomplete="email" required></label>
                <label>Password<input type="password" name="password" autocomplete="new-password" required></label>
                <label>Confirm password<input type="password" name="password_confirmation" autocomplete="new-password" required></label>
                @if ($errors->has('name') || $errors->has('register_email') || $errors->has('password'))
                    <p class="form-error">{{ $errors->first('name') ?: $errors->first('register_email') ?: $errors->first('password') }}</p>
                @endif
                <button class="button button-blue" type="submit">Create account <span>→</span></button>
                <p class="auth-switch">Already have an account? <a href="{{ route('auth.login') }}">Sign in</a></p>
            </form>
        @endif
    </section>
</x-site>
