<?php

use App\Models\Update;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('shows published updates on the frontend', function (): void {
    Update::create([
        'title' => 'Community rehearsal is live',
        'summary' => 'A new production update is live.',
        'body' => 'The team is preparing a fresh rehearsal schedule for the coming season.',
        'published_at' => now(),
        'status' => 'published',
    ]);

    $response = $this->get(route('updates'));

    $response->assertOk();
    $response->assertSee('Community rehearsal is live');
    $response->assertSee('A new production update is live.');
});

it('allows an admin to create and publish an update', function (): void {
    $admin = User::factory()->create();
    Storage::fake('public');

    $response = $this->actingAs($admin)->post(route('admin.updates.store'), [
        'title' => 'New season launch',
        'summary' => 'Our next season is about to begin.',
        'body' => 'We are excited to share the first announcement for the new season.',
        'author_name' => 'Naledi Mokoena',
        'author_bio' => 'Naledi is the creative director of Les Stars Du Theatre.',
        'thumbnail' => UploadedFile::fake()->createWithContent(
            'season.png',
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9WlH1f8AAAAASUVORK5CYII='),
        ),
        'published_at' => now()->format('Y-m-d\TH:i'),
        'status' => 'published',
    ]);

    $response->assertRedirect(route('admin.updates.index'));
    $this->assertDatabaseHas('updates', [
        'title' => 'New season launch',
        'author_name' => 'Naledi Mokoena',
        'status' => 'published',
    ]);

    $update = Update::where('title', 'New season launch')->firstOrFail();
    Storage::disk('public')->assertExists($update->thumbnail_path);

    $this->get(route('updates'))
        ->assertSee('New season launch')
        ->assertSee('Written by Naledi Mokoena')
        ->assertSee('Naledi is the creative director');
});

it('publishes an update immediately when no publication date is provided', function (): void {
    $admin = User::factory()->create();

    $this->actingAs($admin)->post(route('admin.updates.store'), [
        'title' => 'Immediate update',
        'summary' => 'This announcement should appear immediately.',
        'body' => 'The publication date is assigned by the application.',
        'status' => 'published',
    ])->assertRedirect(route('admin.updates.index'));

    $this->get(route('updates'))->assertSee('Immediate update');
});
