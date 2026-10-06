<x-admin.layout title="{{ $contribution->exists ? 'Manage Member Payment' : 'Add Member Payment' }}">
    <div class="admin-heading"><div><p class="eyebrow blue">MEMBER PAYMENTS</p><h1>{{ $contribution->exists ? 'Manage payment' : 'Add payment purpose' }}</h1></div></div>
    <form class="admin-form" method="POST" action="{{ $contribution->exists ? route('admin.contributions.update', $contribution) : route('admin.contributions.store') }}">
        @csrf @if($contribution->exists) @method('PUT') @endif
        <label>Purpose / activity<input name="purpose" value="{{ old('purpose', $contribution->purpose) }}" placeholder="e.g. Costume contribution" required></label>
        <div class="form-grid"><label>Amount<input type="number" name="amount" step="0.01" min="0.01" value="{{ old('amount', $contribution->amount) }}" placeholder="0.00" required></label><label>Currency<input name="currency" value="{{ old('currency', $contribution->currency ?: 'RWF') }}" placeholder="RWF" required></label></div>
        <label>Mobile Money number<input name="mobile_money_number" value="{{ old('mobile_money_number', $contribution->mobile_money_number) }}" placeholder="e.g. 078 000 0000"></label>
        <label>MoMo code / reference<input name="mobile_money_code" value="{{ old('mobile_money_code', $contribution->mobile_money_code) }}" placeholder="e.g. *182*8*1# or merchant code"></label>
        <label>Payment instructions<textarea name="payment_instructions" placeholder="Explain how members should pay and what reference to use.">{{ old('payment_instructions', $contribution->payment_instructions) }}</textarea></label>
        <fieldset class="attendance-member-list contribution-member-list"><legend>Payment and refund status</legend><p>Tick Paid when a payment is confirmed. Tick Refunded if you have returned the money; refunded status takes priority over paid.</p>
            @forelse($members as $member)<div class="contribution-member"><span><b>{{ $member->name }}</b><small>{{ $member->registration_number }}</small></span><label><input type="checkbox" name="paid_member_ids[]" value="{{ $member->id }}" @checked(in_array($member->id, old('paid_member_ids', $paidMemberIds)))> Paid</label><label><input type="checkbox" name="refunded_member_ids[]" value="{{ $member->id }}" @checked(in_array($member->id, old('refunded_member_ids', $refundedMemberIds)))> Refunded</label></div>@empty<p>No approved Member Stars are available yet.</p>@endforelse
        </fieldset>
        <label>Refund reason <textarea name="refund_reason" placeholder="Optional: explain why the payment was refunded.">{{ old('refund_reason') }}</textarea></label>
        @if($errors->any())<p class="form-error">{{ $errors->first() }}</p>@endif
        <button class="button button-blue" type="submit">{{ $contribution->exists ? 'Save payment status' : 'Create payment purpose' }} <span>→</span></button>
    </form>
</x-admin.layout>
