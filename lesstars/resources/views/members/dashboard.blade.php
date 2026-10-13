<x-site title="Member Dashboard — Les Stars Du Theatre">
    <section class="member-dashboard">
        <p class="eyebrow">MEMBER STAR PORTAL</p>
        <div class="member-dashboard-heading">
            <div><h1>Hello, <em>{{ $member->name }}</em>.</h1><p>Keep track of your Les Stars activity attendance. Green days are recorded as attended; red days are recorded as absent.</p></div>
            <form method="POST" action="{{ route('members.logout') }}">@csrf<button class="button button-light">Sign out <span>→</span></button></form>
        </div>
        <section class="attendance-overview">
            <div><span>ATTENDANCE THIS MONTH</span><b>{{ $attendedCount + $absentCount ? round(($attendedCount / ($attendedCount + $absentCount)) * 100) : 0 }}%</b></div>
            <div><span>ATTENDED</span><b>{{ $attendedCount }}</b></div>
            <div><span>ABSENT</span><b>{{ $absentCount }}</b></div>
        </section>
        @foreach($cacahueteEntries as $entry)
            <section class="cacahuete-game {{ $entry->picked_at ? 'is-revealed' : '' }}">
                <div class="cacahuete-heading"><div><p class="eyebrow">CACAHUÈTE GIFT EXCHANGE</p><h2>{{ $entry->game->title }}</h2></div><span>{{ $entry->game->participants_count }} gifts in the draw</span></div>
                @if($entry->game->message)<p class="cacahuete-message">{{ $entry->game->message }}</p>@endif
                @if($entry->picked_at)
                    <div class="cacahuete-reveal"><span>YOUR GIFT RECIPIENT</span><h3>{{ $entry->recipient->name }}</h3><a href="mailto:{{ $entry->recipient->email }}">{{ $entry->recipient->email }}</a><p>This is your one private Cacahuète draw. Please keep the surprise until gift day.</p></div>
                @else
                    <p class="cacahuete-instruction">Choose one gift. It will reveal the person you are giving a gift to. Your choice is final.</p>
                    <div class="gift-grid">@for($gift = 1; $gift <= $entry->game->participants_count; $gift++)<form method="POST" action="{{ route('members.cacahuete.pick', $entry) }}">@csrf<button type="submit" class="gift-button" aria-label="Open a gift"><span>🎁</span></button></form>@endfor</div>
                @endif
            </section>
        @endforeach
        <section class="member-wallet">
            <div class="wallet-heading"><div><p class="eyebrow blue">MEMBER WALLET</p><h2>Your activity payments</h2></div><p>Payment status is confirmed by the Les Stars administrator after your MoMo or Mobile Money payment is received.</p></div>
            <div class="wallet-summary"><div><span>TOTAL CONFIRMED PAID</span><b>{{ number_format($paidAmount, 2) }}</b></div><div><span>STILL OUTSTANDING</span><b>{{ number_format($outstandingAmount, 2) }}</b></div><div><span>TOTAL REFUNDED</span><b>{{ number_format($refundedAmount, 2) }}</b></div></div>
            <div class="wallet-payment-list">
                @forelse($payments as $payment)
                    <article class="wallet-payment {{ $payment->is_refunded ? 'is-refunded' : ($payment->is_paid ? 'is-paid' : 'is-outstanding') }}"><div><span class="payment-status">{{ $payment->is_refunded ? 'REFUNDED' : ($payment->is_paid ? 'PAID' : 'NOT PAID') }}</span><h3>{{ $payment->contribution->purpose }}</h3><p>{{ number_format((float) $payment->contribution->amount, 2) }} {{ $payment->contribution->currency }}</p>@if($payment->is_refunded)<div class="payment-details"><span>Refunded {{ $payment->refunded_at?->format('d M Y') }}</span>@if($payment->refund_reason)<small>{{ $payment->refund_reason }}</small>@endif</div>@elseif(! $payment->is_paid)<div class="payment-details">@if($payment->contribution->mobile_money_number)<span>MoMo: {{ $payment->contribution->mobile_money_number }}</span>@endif @if($payment->contribution->mobile_money_code)<span>Code: {{ $payment->contribution->mobile_money_code }}</span>@endif @if($payment->contribution->payment_instructions)<small>{{ $payment->contribution->payment_instructions }}</small>@endif</div>@endif</div><b class="payment-amount">{{ number_format((float) $payment->contribution->amount, 2) }} <small>{{ $payment->contribution->currency }}</small></b></article>
                @empty
                    <p class="wallet-empty">There are no activity payments assigned to you yet.</p>
                @endforelse
            </div>
        </section>
        <section class="attendance-calendar">
            <div class="calendar-heading"><div><p class="eyebrow blue">ACTIVITY ATTENDANCE</p><h2>{{ $month->format('F Y') }}</h2></div><div class="calendar-navigation"><a href="{{ route('members.dashboard', ['month' => $month->copy()->subMonth()->format('Y-m')]) }}" aria-label="Previous month">←</a><a href="{{ route('members.dashboard', ['month' => $month->copy()->addMonth()->format('Y-m')]) }}" aria-label="Next month">→</a></div></div>
            <div class="calendar-legend"><span><i class="attended"></i> Attended</span><span><i class="absent"></i> Absent</span><span><i></i> No activity recorded</span></div>
            <div class="calendar-grid">
                @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $dayName)<b>{{ $dayName }}</b>@endforeach
                @for ($blank = 0; $blank < $month->dayOfWeek; $blank++)<span class="calendar-day is-empty"></span>@endfor
                @for ($day = 1; $day <= $month->daysInMonth; $day++)
                    @php($date = $month->copy()->day($day)->format('Y-m-d'))
                    @php($records = $attendanceByDay->get($date, collect()))
                    @php($hasAttended = $records->contains('attended', true))
                    @php($hasAbsent = $records->contains('attended', false))
                    <div class="calendar-day {{ $hasAttended ? 'has-attended' : ($hasAbsent ? 'has-absent' : '') }}"><time datetime="{{ $date }}">{{ $day }}</time>@foreach($records as $record)<small>{{ $record->session->title }}</small>@endforeach</div>
                @endfor
            </div>
        </section>
    </section>
</x-site>
