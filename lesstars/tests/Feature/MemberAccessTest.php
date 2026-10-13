<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('only approved members can enter the member dashboard', function () {
    $approvedMember = User::factory()->create([
        'email' => 'star@example.com',
        'registration_number' => 'STAR-001',
        'is_member_approved' => true,
    ]);

    $this->actingAs($approvedMember)
        ->get(route('members.dashboard'))
        ->assertOk()
        ->assertSee($approvedMember->name);
});

test('an approved Member Star can open their dashboard after normal sign-in', function () {
    $member = User::factory()->create([
        'password' => 'Password123!',
        'registration_number' => 'STAR-004',
        'is_member_approved' => true,
        'is_admin' => false,
    ]);

    $this->post(route('login'), [
        'login_email' => $member->email,
        'password' => 'Password123!',
    ])->assertRedirect(route('home'));

    $this->get(route('members.login'))
        ->assertRedirect(route('members.dashboard'));

    $this->get(route('members.dashboard'))
        ->assertOk()
        ->assertSee($member->name);
});

test('unapproved members cannot enter the member dashboard', function () {
    $member = User::factory()->create([
        'email' => 'pending@example.com',
        'registration_number' => 'STAR-002',
        'is_member_approved' => false,
    ]);

    $this->actingAs($member)
        ->get(route('members.dashboard'))
        ->assertRedirect(route('members.login'));

    $this->get(route('members.login'))
        ->assertOk()
        ->assertSee('Approval needed');
});

test('members must sign in before opening member access', function () {
    $this->get(route('members.login'))
        ->assertRedirect(route('auth.login'));
});

test('an admin can upgrade an existing site user to a Member Star without creating another account', function () {
    $admin = User::factory()->create();
    $user = User::factory()->create([
        'name' => 'Global Breakings',
        'email' => 'globalbreakings@gmail.com',
        'password' => 'Password123!',
        'is_admin' => false,
        'registration_number' => null,
        'is_member_approved' => false,
    ]);

    $this->actingAs($admin)->post(route('admin.members.store'), [
        'name' => 'Global Breakings',
        'email' => 'GLOBALBREAKINGS@gmail.com',
        'registration_number' => 'UG123456',
        'member_category' => 'current',
    ])->assertRedirect(route('admin.members.index'));

    expect(User::query()->where('email', 'globalbreakings@gmail.com')->count())->toBe(1);

    $user->refresh();
    expect($user->registration_number)->toBe('UG123456')
        ->and($user->is_member_approved)->toBeTrue()
        ->and($user->member_category)->toBe('current')
        ->and(Hash::check('Password123!', $user->password))->toBeTrue();
});

test('a Former Member Star can enter the member dashboard after approval', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $member = User::factory()->create([
        'email' => 'former@example.com',
        'registration_number' => 'STAR-007',
        'is_member_approved' => false,
    ]);

    $this->actingAs($admin)->put(route('admin.members.update', $member), [
        'name' => $member->name,
        'email' => $member->email,
        'registration_number' => $member->registration_number,
        'member_category' => 'former',
    ])->assertRedirect(route('admin.members.index'));

    $member->refresh();
    expect($member->member_category)->toBe('former')
        ->and($member->is_member_approved)->toBeTrue();

    $this->actingAs($member)
        ->post(route('members.login.store'), [
            'member_email' => $member->email,
            'registration_number' => 'STAR-007',
        ])->assertRedirect(route('members.dashboard'));

    $this->get(route('members.dashboard'))->assertOk();
});

test('admin can leave a Member Star unapproved', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $member = User::factory()->create([
        'registration_number' => 'STAR-008',
        'is_member_approved' => true,
        'member_category' => 'current',
    ]);

    $this->actingAs($admin)->put(route('admin.members.update', $member), [
        'name' => $member->name,
        'email' => $member->email,
        'registration_number' => $member->registration_number,
        'member_category' => '',
    ])->assertRedirect(route('admin.members.index'));

    expect($member->fresh()->is_member_approved)->toBeFalse()
        ->and($member->fresh()->member_category)->toBeNull();
});

test('a Member Star can register a normal account using the same email and keep their membership', function () {
    $member = User::factory()->create([
        'name' => 'Member Name',
        'email' => 'member@example.com',
        'password' => 'UnclaimedMemberPassword123!',
        'registration_number' => 'STAR-005',
        'is_member_approved' => true,
    ]);

    $this->post(route('register'), [
        'name' => 'Updated Member Name',
        'register_email' => 'MEMBER@example.com',
        'registration_number' => 'STAR-005',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ])->assertRedirect(route('home'));

    expect(User::query()->whereRaw('LOWER(email) = ?', ['member@example.com'])->count())->toBe(1);
    $member->refresh();
    expect($member->name)->toBe('Updated Member Name')
        ->and($member->registration_number)->toBe('STAR-005')
        ->and($member->is_member_approved)->toBeTrue()
        ->and(Hash::check('Password123!', $member->password))->toBeTrue();
});

test('a Member Star must provide the matching registration number to claim their account', function () {
    $member = User::factory()->create([
        'email' => 'member@example.com',
        'password' => 'UnclaimedMemberPassword123!',
        'registration_number' => 'STAR-006',
    ]);

    $this->from(route('auth'))->post(route('register'), [
        'name' => 'Member Name',
        'register_email' => 'member@example.com',
        'registration_number' => 'WRONG-NUMBER',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ])->assertRedirect(route('auth'))
        ->assertSessionHasErrors('registration_number');

    expect(Hash::check('UnclaimedMemberPassword123!', $member->fresh()->password))->toBeTrue();
});

test('normal account signup cannot replace an existing normal account', function () {
    User::factory()->create(['email' => 'existing@example.com']);

    $this->post(route('register'), [
        'name' => 'Another User',
        'register_email' => 'existing@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ])->assertSessionHasErrors('register_email');

    expect(User::query()->where('email', 'existing@example.com')->count())->toBe(1);
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
