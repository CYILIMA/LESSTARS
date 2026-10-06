<x-admin.layout title="Create Cacahuète Game">
    <div class="admin-heading"><div><p class="eyebrow blue">GIFT EXCHANGE</p><h1>Create Cacahuète</h1></div></div>
    <form class="admin-form" method="POST" action="{{ route('admin.cacahuete.store') }}">
        @csrf
        <label>Game title<input name="title" value="{{ old('title') }}" placeholder="e.g. End-of-year Cacahuète" required></label>
        <label>Message for players<textarea name="message" placeholder="Optional welcome message or gift instructions.">{{ old('message') }}</textarea></label>
        <label>Allowed players<textarea name="players" placeholder="Amina Uwase, amina@example.com&#10;Jean Habimana, jean@example.com" required>{{ old('players') }}</textarea></label>
        <p class="admin-help">Add one player per line as: <strong>Full name, email@example.com</strong>. At least two unique email addresses are required. Every participant receives a different person—never themselves.</p>
        @if($errors->any())<p class="form-error">{{ $errors->first() }}</p>@endif
        <button class="button button-blue" type="submit">Create secure draw <span>→</span></button>
    </form>
</x-admin.layout>
