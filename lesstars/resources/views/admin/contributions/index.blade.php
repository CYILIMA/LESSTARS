<x-admin.layout title="Member Wallet">
    <div class="admin-heading"><div><p class="eyebrow blue">MEMBER PAYMENTS</p><h1>Member wallet</h1></div><a class="button button-blue" href="{{ route('admin.contributions.create') }}">Add payment purpose <span>→</span></a></div>
    <section class="admin-panel">
        <h2>Contribution records</h2>
        @forelse ($contributions as $contribution)
            <div class="admin-row"><div><b>{{ $contribution->purpose }}</b><span>{{ number_format((float) $contribution->amount, 2) }} {{ $contribution->currency }} · {{ $contribution->paid_count }} members paid</span></div><div class="row-actions"><a href="{{ route('admin.contributions.edit', $contribution) }}">Manage payments</a><form method="POST" action="{{ route('admin.contributions.destroy', $contribution) }}">@csrf @method('DELETE')<button onclick="return confirm('Remove this payment purpose?')">Remove</button></form></div></div>
        @empty
            <p>No member payment purposes have been created.</p>
        @endforelse
    </section>
</x-admin.layout>
