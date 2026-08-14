<?php

use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('shows only published team members on the public team page', function (): void {
    TeamMember::factory()->create([
        'name' => 'Naledi Mokoena',
        'role' => 'Creative Director',
        'status' => 'published',
    ]);
    TeamMember::factory()->create([
        'name' => 'Private Draft',
        'status' => 'draft',
    ]);

    $this->get(route('team'))
        ->assertOk()
        ->assertSee('Naledi Mokoena')
        ->assertDontSee('Private Draft');
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
    ]);

    $response->assertRedirect(route('admin.team-members.index'));
    $this->assertDatabaseHas('team_members', [
        'name' => 'Naledi Mokoena',
        'email' => 'naledi@example.test',
        'display_order' => 0,
        'status' => 'published',
    ]);

    $teamMember = TeamMember::where('email', 'naledi@example.test')->firstOrFail();
    Storage::disk('public')->assertExists($teamMember->image_url);
});
