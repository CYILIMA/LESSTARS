<x-admin.layout title="Donations">
    <div class="admin-heading"><div><p class="eyebrow blue">SUPPORTERS</p><h1>Donations</h1></div></div>
    <div class="admin-panel">
        @forelse ($donations as $donation)
            <article class="donation-record">
                <div>
                    <b>{{ $donation->first_name }} {{ $donation->last_name }}</b>
                    <span>{{ number_format((float) $donation->amount, 2) }} {{ $donation->currency }} · {{ str_replace('_', ' ', $donation->payment_method) }}</span>
                    <span>{{ $donation->email }} · {{ $donation->phone }}@if($donation->country) · {{ $donation->country }}@endif</span>
                    @if($donation->message)<p>{{ $donation->message }}</p>@endif
                    <small>Submitted {{ $donation->created_at->format('d M Y · H:i') }}</small>
                </div>
                <form method="POST" action="{{ route('admin.donations.status', $donation) }}">
                    @csrf @method('PATCH')
                    <select name="status" onchange="this.form.submit()">
                        @foreach (['pending' => 'Pending', 'contacted' => 'Contacted', 'received' => 'Received', 'cancelled' => 'Cancelled'] as $value => $label)
                            <option value="{{ $value }}" @selected($donation->status === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </form>
            </article>
        @empty
            <p>No donation requests have been received yet.</p>
        @endforelse
    </div>
    {{ $donations->links() }}
</x-admin.layout>
