<x-site title="Donate — Les Stars Du Theatre">
    <section class="donate-page donation-page">
        <div>
            <p class="eyebrow">SUPPORT LES STARS</p>
            <h1>Help a story<br><em>travel further.</em></h1>
            <p>Your contribution supports original theatre, radio productions, and opportunities for emerging artists.</p>
        </div>

        <form class="donation-form" method="POST" action="{{ route('donations.store') }}">
            @csrf
            <h2>Donation details</h2>
            @if (session('success'))<p class="donation-success">{{ session('success') }}</p>@endif
            <div class="form-grid">
                <label>First name<input name="first_name" value="{{ old('first_name') }}" autocomplete="given-name" required></label>
                <label>Last name<input name="last_name" value="{{ old('last_name') }}" autocomplete="family-name" required></label>
            </div>
            <div class="form-grid">
                <label>Email address<input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required></label>
                <label>Phone number<input type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel" required></label>
            </div>
            <div class="form-grid">
                <label>Amount<input type="number" name="amount" value="{{ old('amount') }}" min="1" step="0.01" inputmode="decimal" required></label>
                <label>Currency<select name="currency" required><option value="RWF" @selected(old('currency') === 'RWF')>Rwandan franc (RWF)</option><option value="USD" @selected(old('currency') === 'USD')>US dollar (USD)</option></select></label>
            </div>
            <div class="form-grid">
                <label>Preferred payment method<select name="payment_method" required><option value="">Choose a method</option><option value="mobile_money" @selected(old('payment_method') === 'mobile_money')>Mobile money</option><option value="bank_transfer" @selected(old('payment_method') === 'bank_transfer')>Bank transfer</option><option value="card" @selected(old('payment_method') === 'card')>Card</option><option value="other" @selected(old('payment_method') === 'other')>Other</option></select></label>
                <label>Country <small>(optional)</small><input name="country" value="{{ old('country') }}" autocomplete="country-name"></label>
            </div>
            <label>Message <small>(optional)</small><textarea name="message" rows="3" maxlength="1000">{{ old('message') }}</textarea></label>
            <label class="donation-consent"><input type="checkbox" name="privacy_consent" value="1" @checked(old('privacy_consent')) required> I agree that Les Stars Du Theatre may use these details to contact me about this donation.</label>
            @if ($errors->any())<p class="form-error">Please check the form and correct the highlighted details.</p>@endif
            <button class="button button-blue" type="submit">Send donation request <span>→</span></button>
        </form>
    </section>
</x-site>
