<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\CacahueteParticipant;
use App\Models\ContributionPayment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function create(): View
    {
        return view('members.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'member_email' => ['required', 'email'],
            'registration_number' => ['required', 'string', 'max:255'],
        ]);

        $member = User::query()
            ->where('email', $credentials['member_email'])
            ->where('registration_number', $credentials['registration_number'])
            ->where('is_member_approved', true)
            ->first();

        if (! $member) {
            return back()
                ->withErrors(['member_email' => 'We could not verify an approved Star membership with those details.'])
                ->onlyInput('member_email');
        }

        Auth::login($member);
        $request->session()->regenerate();

        return redirect()->route('members.dashboard');
    }

    public function dashboard(Request $request): View
    {
        $monthInput = $request->string('month')->value();
        $month = preg_match('/^\d{4}-\d{2}$/', $monthInput)
            ? Carbon::createFromFormat('Y-m', $monthInput)->startOfMonth()
            : now()->startOfMonth();

        $attendance = Attendance::query()
            ->with('session')
            ->where('user_id', $request->user()->id)
            ->whereHas('session', fn ($query) => $query->whereBetween('attendance_date', [$month->toDateString(), $month->copy()->endOfMonth()->toDateString()]))
            ->get();

        $attendanceByDay = $attendance->groupBy(fn (Attendance $record): string => $record->session->attendance_date->format('Y-m-d'));
        $payments = ContributionPayment::query()
            ->with('contribution')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();
        $cacahueteEntries = CacahueteParticipant::query()
            ->with(['game' => fn ($query) => $query->withCount('participants'), 'recipient'])
            ->whereRaw('LOWER(email) = ?', [strtolower($request->user()->email)])
            ->whereHas('game', fn ($query) => $query->where('is_active', true))
            ->get();

        return view('members.dashboard', [
            'member' => $request->user(),
            'month' => $month,
            'attendanceByDay' => $attendanceByDay,
            'attendedCount' => $attendance->where('attended', true)->count(),
            'absentCount' => $attendance->where('attended', false)->count(),
            'payments' => $payments,
            'paidAmount' => $payments->where('is_paid', true)->sum(fn (ContributionPayment $payment): float => (float) $payment->contribution->amount),
            'outstandingAmount' => $payments->filter(fn (ContributionPayment $payment): bool => ! $payment->is_paid && ! $payment->is_refunded)->sum(fn (ContributionPayment $payment): float => (float) $payment->contribution->amount),
            'refundedAmount' => $payments->where('is_refunded', true)->sum(fn (ContributionPayment $payment): float => (float) $payment->contribution->amount),
            'cacahueteEntries' => $cacahueteEntries,
        ]);
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('members.login');
    }
}
