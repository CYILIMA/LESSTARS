<?php

use App\Models\MembershipApplicationForm;
use App\Models\User;

test('applicants can submit a public form and an admin can review it', function (): void {
    $form = MembershipApplicationForm::create([
        'title' => 'Join Les Stars',
        'public_token' => 'join-les-stars-test',
        'is_open' => true,
    ]);
    $essay = $form->questions()->create(['question' => 'Why do you want to join?', 'type' => 'essay', 'is_required' => true, 'sort_order' => 0]);
    $choice = $form->questions()->create(['question' => 'Your availability?', 'type' => 'choice', 'options' => ['Weekdays', 'Weekends'], 'is_required' => true, 'sort_order' => 1]);

    $this->get(route('membership-applications.create', $form))->assertOk()->assertSee('Join Les Stars');
    $this->post(route('membership-applications.store', $form), [
        'applicant_name' => 'Applicant Star',
        'applicant_email' => 'applicant@example.com',
        'answers' => [$essay->id => 'I love theatre.', $choice->id => 'Weekends'],
    ])->assertRedirect(route('membership-applications.thank-you'));

    $application = $form->applications()->firstOrFail();
    expect($application->answers)->toHaveCount(2);

    $admin = User::factory()->create();
    $this->actingAs($admin)->get(route('admin.member-applications.show', $application))
        ->assertOk()
        ->assertSee('I love theatre.')
        ->assertSee('Weekends');
    $this->actingAs($admin)->patch(route('admin.member-applications.review', $application), ['status' => 'accepted', 'decision_notes' => 'Welcome.'])
        ->assertSessionHas('success');

    expect($application->fresh()->status)->toBe('accepted');
});

test('an admin can create essay and multiple-choice application questions', function (): void {
    $admin = User::factory()->create();

    $this->actingAs($admin)->post(route('admin.membership-applications.store'), [
        'title' => 'New Stars Application',
        'introduction' => 'Tell us about yourself.',
        'questions' => [
            ['question' => 'Why should you join?', 'type' => 'essay', 'is_required' => true],
            ['question' => 'When are you available?', 'type' => 'choice', 'options' => "Weekdays\nWeekends", 'is_required' => true],
        ],
    ])->assertRedirect(route('admin.membership-applications.index'));

    $form = MembershipApplicationForm::query()->where('title', 'New Stars Application')->firstOrFail();
    expect($form->questions)->toHaveCount(2);
    expect($form->questions->last()->options)->toBe(['Weekdays', 'Weekends']);
});

test('an admin can download a csv of approved applicant names and emails', function (): void {
    $admin = User::factory()->create();
    $form = MembershipApplicationForm::create([
        'title' => 'Approved members export',
        'public_token' => 'approved-members-export',
        'is_open' => true,
    ]);

    $accepted = $form->applications()->create([
        'applicant_name' => 'Approved Person',
        'applicant_email' => 'approved@example.com',
        'status' => 'accepted',
    ]);
    $form->applications()->create([
        'applicant_name' => 'Rejected Person',
        'applicant_email' => 'rejected@example.com',
        'status' => 'rejected',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.membership-applications.export'));

    $response
        ->assertOk()
        ->assertHeader('Content-Type', 'text/csv; charset=UTF-8')
        ->assertHeader('Content-Disposition', 'attachment; filename=approved-member-applicants.csv');

    $content = $response->streamedContent();
    expect($content)->toContain('name,email')
        ->toContain('Approved Person')
        ->toContain('approved@example.com')
        ->not->toContain('Rejected Person');

    expect($accepted->fresh()->status)->toBe('accepted');
});
