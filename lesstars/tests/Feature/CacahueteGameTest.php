<?php

use App\Models\CacahueteParticipant;
use App\Models\User;

test('cacahuete assigns every player a different recipient and permits only one reveal', function (): void {
    $admin = User::factory()->create();
    $amina = User::factory()->create(['name' => 'Amina', 'email' => 'amina@example.com', 'registration_number' => 'STAR-301', 'is_member_approved' => true, 'is_admin' => false]);
    User::factory()->create(['name' => 'Jean', 'email' => 'jean@example.com', 'registration_number' => 'STAR-302', 'is_member_approved' => true, 'is_admin' => false]);

    $this->actingAs($admin)->post(route('admin.cacahuete.store'), [
        'title' => 'Holiday Cacahuète',
        'message' => 'Choose your gift.',
        'players' => "Amina, amina@example.com\nJean, jean@example.com",
    ])->assertRedirect(route('admin.cacahuete.index'));

    $aminaEntry = CacahueteParticipant::query()->where('email', 'amina@example.com')->firstOrFail();
    expect($aminaEntry->recipient_id)->not->toBe($aminaEntry->id);
    $gameParticipants = CacahueteParticipant::query()->where('cacahuete_game_id', $aminaEntry->cacahuete_game_id)->get();
    expect($gameParticipants->every(fn (CacahueteParticipant $participant): bool => $participant->recipient_id !== $participant->id))->toBeTrue();
    expect($gameParticipants->pluck('recipient_id')->unique()->count())->toBe($gameParticipants->count());

    $this->actingAs($amina)->get(route('members.dashboard'))
        ->assertOk()
        ->assertSee('Holiday Cacahuète')
        ->assertSee('2 gifts in the draw')
        ->assertDontSee('Gift 1')
        ->assertDontSee('Gift 2');

    $this->actingAs($amina)->post(route('members.cacahuete.pick', $aminaEntry))
        ->assertRedirect(route('members.dashboard'));

    $firstPick = $aminaEntry->fresh()->picked_at;
    expect($firstPick)->not->toBeNull();

    $this->actingAs($amina)->post(route('members.cacahuete.pick', $aminaEntry))
        ->assertRedirect(route('members.dashboard'));

    expect($aminaEntry->fresh()->picked_at->equalTo($firstPick))->toBeTrue();
});
