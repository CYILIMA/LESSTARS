<?php

use App\Models\Event;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('shows published events on the frontend', function (): void {
    Event::create([
        'title' => 'Community rehearsal is live',
        'summary' => 'A new event is live.',
        'body' => 'The team is preparing a fresh rehearsal schedule for the coming season.',
        'venue' => 'Main theatre',
        'starts_at' => now()->addDay(),
        'published_at' => now(),
        'status' => 'published',
    ]);

    $response = $this->get(route('events'));

    $response->assertOk();
    $response->assertSee('Community rehearsal is live');
    $response->assertSee('A new event is live.');
});

it('opens a published event on its own shareable page', function (): void {
    $event = Event::create([
        'title' => 'A shareable event',
        'summary' => 'The event summary.',
        'body' => 'The full event story.',
        'venue' => 'Main theatre',
        'starts_at' => now()->addDay(),
        'published_at' => now(),
        'status' => 'published',
    ]);

    $this->get(route('events.show', $event))
        ->assertOk()
        ->assertSee('A shareable event')
        ->assertSee('The full event story.')
        ->assertSee('/events/a-shareable-event', false)
        ->assertDontSee('SHARE THIS EVENT');
});

it('redirects old numeric Event links to the readable slug URL', function (): void {
    $event = Event::create([
        'title' => 'Legacy event link',
        'summary' => 'The event summary.',
        'venue' => 'Main theatre',
        'starts_at' => now()->addDay(),
        'published_at' => now(),
        'status' => 'published',
    ]);

    $this->get('/events/'.$event->id)
        ->assertRedirect(route('events.show', $event));
});

it('does not expose draft events publicly', function (): void {
    $event = Event::create([
        'title' => 'Private event',
        'summary' => 'Draft summary.',
        'venue' => 'Main theatre',
        'starts_at' => now()->addDay(),
        'status' => 'draft',
    ]);

    $this->get(route('events.show', $event))->assertNotFound();
});

it('allows an admin to create and publish an event', function (): void {
    $admin = User::factory()->create();
    Storage::fake('public');

    $response = $this->actingAs($admin)->post(route('admin.events.store'), [
        'title' => 'New season launch',
        'summary' => 'Our next season is about to begin.',
        'body' => 'We are excited to share the first event for the new season.',
        'venue' => 'Les Stars Hall',
        'starts_at' => now()->addWeek()->format('Y-m-d\\TH:i'),
        'author_name' => 'Naledi Mokoena',
        'author_bio' => 'Naledi is the creative director of Les Stars Du Theatre.',
        'thumbnail' => UploadedFile::fake()->createWithContent(
            'season.png',
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9WlH1f8AAAAASUVORK5CYII='),
        ),
        'published_at' => now()->format('Y-m-d\\TH:i'),
        'status' => 'published',
    ]);

    $response->assertRedirect(route('admin.events.index'));
    $this->assertDatabaseHas('events', [
        'title' => 'New season launch',
        'author_name' => 'Naledi Mokoena',
        'status' => 'published',
    ]);

    $event = Event::where('title', 'New season launch')->firstOrFail();
    expect(Storage::disk('public')->exists($event->thumbnail_path))->toBeTrue();

    $this->get(route('events'))
        ->assertSee('New season launch')
        ->assertSee('Naledi Mokoena')
        ->assertSee('Naledi is the creative director');
});

it('publishes an event immediately when no publication date is provided', function (): void {
    $admin = User::factory()->create();

    $this->actingAs($admin)->post(route('admin.events.store'), [
        'title' => 'Immediate event',
        'summary' => 'This event should appear immediately.',
        'venue' => 'Main theatre',
        'starts_at' => now()->addDay()->format('Y-m-d\\TH:i'),
        'status' => 'published',
    ])->assertRedirect(route('admin.events.index'));

    $this->get(route('events'))->assertSee('Immediate event');
});
