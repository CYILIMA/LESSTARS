<?php

use App\Models\User;

test('only approved members can enter the member dashboard', function () {
    $approvedMember = User::factory()->create([
        'email' => 'star@example.com',
        'registration_number' => 'STAR-001',
        'is_member_approved' => true,
    ]);

    $this->post(route('members.login.store'), [
        'member_email' => $approvedMember->email,
        'registration_number' => 'STAR-001',
    ])->assertRedirect(route('members.dashboard'));

    $this->get(route('members.dashboard'))->assertOk()->assertSee($approvedMember->name);
});

test('unapproved members cannot enter the member dashboard', function () {
    $member = User::factory()->create([
        'email' => 'pending@example.com',
        'registration_number' => 'STAR-002',
        'is_member_approved' => false,
    ]);

    $this->post(route('members.login.store'), [
        'member_email' => $member->email,
        'registration_number' => 'STAR-002',
    ])->assertSessionHasErrors('member_email');
});

test('member accounts are sent to the administrator login instead of receiving a forbidden error', function () {
    $member = User::factory()->create([
        'registration_number' => 'STAR-003',
        'is_member_approved' => true,
        'is_admin' => false,
    ]);

    $this->actingAs($member)->get(route('admin.dashboard'))
        ->assertRedirect(route('admin.login'));
});

test('an administrator remains signed in when returning to the dashboard', function () {
    $admin = User::factory()->create(['password' => 'Password123!']);

    $this->post(route('admin.login.store'), [
        'email' => $admin->email,
        'password' => 'Password123!',
    ])->assertRedirect(route('admin.dashboard'));

    $this->get(route('admin.dashboard'))->assertOk();
});

test('an administrator can see normal website sign-ups', function () {
    $admin = User::factory()->create();
    $websiteUser = User::factory()->create([
        'name' => 'Website User',
        'email' => 'website-user@example.com',
        'is_admin' => false,
        'registration_number' => null,
    ]);

    $this->actingAs($admin)->get(route('admin.registered-users.index'))
        ->assertOk()
        ->assertSee($websiteUser->name)
        ->assertSee($websiteUser->email);
});
