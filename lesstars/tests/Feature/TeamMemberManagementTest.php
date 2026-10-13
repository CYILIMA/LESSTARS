<?php

use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('shows only published team members on their public category page', function (): void {
    TeamMember::factory()->create([
        'name' => 'Naledi Mokoena',
        'role' => 'Creative Director',
        'status' => 'published',
        'category' => 'current_stars',
    ]);
    TeamMember::factory()->create([
        'name' => 'Private Draft',
        'status' => 'draft',
        'category' => 'current_stars',
    ]);

    $this->get(route('team.category', 'current_stars'))
        ->assertOk()
        ->assertSee('Naledi Mokoena')
        ->assertDontSee('Private Draft');
});

it('renders an initials fallback for team members whose image cannot load', function (): void {
    TeamMember::factory()->create([
        'name' => 'Naledi Mokoena',
        'status' => 'published',
        'category' => 'current_stars',
        'image_url' => 'team-members/missing.jpg',
    ]);

    $this->get(route('team.category', 'current_stars'))
        ->assertOk()
        ->assertSee('data-team-member-image', false)
        ->assertSee('data-team-member-fallback', false)
        ->assertSee('>NM</div>', false);
});

it('allows an admin to add a team member', function (): void {
    $admin = User::factory()->create();
    Storage::fake('public');

    $response = $this->actingAs($admin)->post(route('admin.team-members.store'), [
        'name' => 'Naledi Mokoena',
        'role' => 'Creative Director',
        'bio' => 'Naledi shapes the artistic direction and creative collaborations.',
        'email' => 'naledi@example.test',
        'phone' => '+27 11 555 0100',
        'image' => UploadedFile::fake()->createWithContent(
            'naledi.png',
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9WlH1f8AAAAASUVORK5CYII='),
        ),
        'status' => 'published',
        'category' => 'current_leadership',
    ]);

    $response->assertRedirect(route('admin.team-members.index'));
    $this->assertDatabaseHas('team_members', [
        'name' => 'Naledi Mokoena',
        'email' => 'naledi@example.test',
        'display_order' => 0,
        'status' => 'published',
        'category' => 'current_leadership',
    ]);

    $teamMember = TeamMember::where('email', 'naledi@example.test')->firstOrFail();
    Storage::disk('public')->assertExists($teamMember->image_url);
});

it('shows members only in their selected team category', function (): void {
    TeamMember::factory()->create(['name' => 'Current Leader', 'status' => 'published', 'category' => 'current_leadership']);
    TeamMember::factory()->create(['name' => 'Historic Member', 'status' => 'published', 'category' => 'former_stars']);

    $this->get(route('team.category', 'current_leadership'))
        ->assertOk()
        ->assertSee('Current Leadership')
        ->assertSee('Current Leader')
        ->assertDontSee('Historic Member');
});
