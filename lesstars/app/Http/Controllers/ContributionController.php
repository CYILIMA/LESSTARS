<?php

namespace App\Http\Controllers;

use App\Models\ContributionPayment;
use App\Models\MemberContribution;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ContributionController extends Controller
{
    public function index(): View
    {
        return view('admin.contributions.index', [
            'contributions' => MemberContribution::query()->latest()->withCount([
                'payments as paid_count' => fn ($query) => $query->where('is_paid', true),
            ])->get(),
        ]);
    }

    public function create(): View
    {
        return $this->form(new MemberContribution);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $paidMemberIds = collect($data['paid_member_ids'] ?? []);
        $refundedMemberIds = collect($data['refunded_member_ids'] ?? []);
        $refundReason = $data['refund_reason'] ?? null;
        unset($data['paid_member_ids'], $data['refunded_member_ids'], $data['refund_reason']);

        DB::transaction(function () use ($data, $paidMemberIds, $refundedMemberIds, $refundReason): void {
            $contribution = MemberContribution::create($data);
            $this->syncPayments($contribution, $paidMemberIds, $refundedMemberIds, $refundReason);
        });

        return redirect()->route('admin.contributions.index')->with('success', 'Member contribution created.');
    }

    public function edit(MemberContribution $contribution): View
    {
        return $this->form($contribution);
    }

    public function update(Request $request, MemberContribution $contribution): RedirectResponse
    {
        $data = $this->validatedData($request);
        $paidMemberIds = collect($data['paid_member_ids'] ?? []);
        $refundedMemberIds = collect($data['refunded_member_ids'] ?? []);
        $refundReason = $data['refund_reason'] ?? null;
        unset($data['paid_member_ids'], $data['refunded_member_ids'], $data['refund_reason']);

        DB::transaction(function () use ($contribution, $data, $paidMemberIds, $refundedMemberIds, $refundReason): void {
            $contribution->update($data);
            $this->syncPayments($contribution, $paidMemberIds, $refundedMemberIds, $refundReason);
        });

        return redirect()->route('admin.contributions.index')->with('success', 'Payment statuses updated.');
    }

    public function destroy(MemberContribution $contribution): RedirectResponse
    {
        $contribution->delete();

        return back()->with('success', 'Member contribution removed.');
    }

    private function form(MemberContribution $contribution): View
    {
        return view('admin.contributions.form', [
            'contribution' => $contribution,
            'members' => User::query()->where('is_member_approved', true)->orderBy('name')->get(),
            'paidMemberIds' => $contribution->exists
                ? $contribution->payments()->where('is_paid', true)->pluck('user_id')->all()
                : [],
            'refundedMemberIds' => $contribution->exists
                ? $contribution->payments()->where('is_refunded', true)->pluck('user_id')->all()
                : [],
        ]);
    }

    /** @return array<string, mixed> */
    private function validatedData(Request $request): array
    {
        return $request->validate([
            'purpose' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['required', 'string', 'max:8'],
            'mobile_money_number' => ['nullable', 'string', 'max:100'],
            'mobile_money_code' => ['nullable', 'string', 'max:100'],
            'payment_instructions' => ['nullable', 'string', 'max:1000'],
            'paid_member_ids' => ['nullable', 'array'],
            'paid_member_ids.*' => ['integer', 'exists:users,id'],
            'refunded_member_ids' => ['nullable', 'array'],
            'refunded_member_ids.*' => ['integer', 'exists:users,id'],
            'refund_reason' => ['nullable', 'string', 'max:1000'],
        ]);
    }

    /** @param Collection<int, int|string> $paidMemberIds */
    private function syncPayments(MemberContribution $contribution, Collection $paidMemberIds, Collection $refundedMemberIds, ?string $refundReason): void
    {
        User::query()->where('is_member_approved', true)->pluck('id')->each(function (int $memberId) use ($contribution, $paidMemberIds, $refundedMemberIds, $refundReason): void {
            $isPaid = $paidMemberIds->contains($memberId);
            $isRefunded = $refundedMemberIds->contains($memberId);

            ContributionPayment::updateOrCreate(
                ['member_contribution_id' => $contribution->id, 'user_id' => $memberId],
                [
                    'is_paid' => $isPaid && ! $isRefunded,
                    'paid_at' => $isPaid ? now() : null,
                    'is_refunded' => $isRefunded,
                    'refunded_at' => $isRefunded ? now() : null,
                    'refund_reason' => $isRefunded ? $refundReason : null,
                ],
            );
        });
    }
}
