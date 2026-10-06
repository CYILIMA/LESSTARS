<?php

use App\Models\ContributionPayment;
use App\Models\MemberContribution;
use App\Models\User;

test('a member sees confirmed and outstanding activity payments', function (): void {
    $member = User::factory()->create(['registration_number' => 'STAR-200', 'is_member_approved' => true]);
    $paidContribution = MemberContribution::create(['purpose' => 'Costume fund', 'amount' => 5000, 'currency' => 'RWF']);
    $outstandingContribution = MemberContribution::create(['purpose' => 'Transport fund', 'amount' => 2500, 'currency' => 'RWF', 'mobile_money_number' => '0780000000']);
    ContributionPayment::create(['member_contribution_id' => $paidContribution->id, 'user_id' => $member->id, 'is_paid' => true, 'paid_at' => now()]);
    ContributionPayment::create(['member_contribution_id' => $outstandingContribution->id, 'user_id' => $member->id, 'is_paid' => false]);

    $this->actingAs($member)->get(route('members.dashboard'))
        ->assertOk()
        ->assertSee('Costume fund')
        ->assertSee('Transport fund')
        ->assertSee('PAID')
        ->assertSee('NOT PAID')
        ->assertSee('0780000000');
});

test('a member sees refunded payment status and reason', function (): void {
    $member = User::factory()->create(['registration_number' => 'STAR-201', 'is_member_approved' => true, 'is_admin' => false]);
    $contribution = MemberContribution::create(['purpose' => 'Cancelled workshop', 'amount' => 4000, 'currency' => 'RWF']);
    ContributionPayment::create(['member_contribution_id' => $contribution->id, 'user_id' => $member->id, 'is_refunded' => true, 'refunded_at' => now(), 'refund_reason' => 'The workshop was cancelled.']);

    $this->actingAs($member)->get(route('members.dashboard'))
        ->assertOk()
        ->assertSee('REFUNDED')
        ->assertSee('The workshop was cancelled.');
});

test('an admin can mark a member payment as refunded', function (): void {
    $admin = User::factory()->create();
    $member = User::factory()->create(['registration_number' => 'STAR-202', 'is_member_approved' => true, 'is_admin' => false]);
    $contribution = MemberContribution::create(['purpose' => 'Venue deposit', 'amount' => 3000, 'currency' => 'RWF']);

    $this->actingAs($admin)->put(route('admin.contributions.update', $contribution), [
        'purpose' => 'Venue deposit',
        'amount' => 3000,
        'currency' => 'RWF',
        'refunded_member_ids' => [$member->id],
        'refund_reason' => 'The venue booking was cancelled.',
    ])->assertRedirect(route('admin.contributions.index'));

    $this->assertDatabaseHas('contribution_payments', [
        'member_contribution_id' => $contribution->id,
        'user_id' => $member->id,
        'is_refunded' => true,
        'refund_reason' => 'The venue booking was cancelled.',
    ]);
});
