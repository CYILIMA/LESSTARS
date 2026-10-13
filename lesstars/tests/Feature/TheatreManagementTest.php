<?php

use App\Models\Theatre;
use App\Models\TheatreComment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('allows an admin to upload published theatre audio', function (): void {
    Storage::fake('public');
    Storage::fake('local');
    $admin = User::factory()->create();

    $this->actingAs($admin)->post(route('admin.theatres.store'), [
        'title' => 'The River Speaks',
        'description' => 'An original audio theatre production.',
        'thumbnail' => UploadedFile::fake()->createWithContent('river.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9WlH1f8AAAAASUVORK5CYII=')),
        'audio' => UploadedFile::fake()->create('river.mp3', 100, 'audio/mpeg'),
        'status' => 'published',
    ])->assertRedirect(route('admin.theatres.index'));

    $this->assertDatabaseHas('theatres', ['title' => 'The River Speaks', 'status' => 'published']);
    $theatre = Theatre::firstOrFail();
    Storage::disk('local')->assertExists($theatre->audio_path);
    Storage::disk('public')->assertExists($theatre->thumbnail_path);
    Storage::disk('public')->assertMissing($theatre->audio_path);
});

it('keeps public comments pending until an admin approves them', function (): void {
    $theatre = Theatre::create(['title' => 'The River Speaks', 'description' => 'An original audio theatre production.', 'audio_path' => 'theatres/river.mp3', 'status' => 'published']);

    $user = User::factory()->create(['is_admin' => false]);
    $this->actingAs($user)->post(route('theatres.comments.store', $theatre), ['name' => 'Amahle', 'email' => 'amahle@example.test', 'body' => 'A moving performance.'])->assertSessionHas('success');
    $this->assertDatabaseHas('theatre_comments', ['name' => 'Amahle', 'status' => 'pending']);

    $admin = User::factory()->create();
    $comment = TheatreComment::firstOrFail();
    $this->actingAs($admin)->patch(route('admin.theatre-comments.approve', $comment));

    $this->get(route('theatres.show', $theatre))->assertSee('A moving performance.');
});

it('uses a theatre title slug and exposes its thumbnail for sharing', function (): void {
    $theatre = Theatre::create([
        'title' => 'The River Speaks',
        'description' => 'An original audio theatre production.',
        'thumbnail_path' => 'theatres/river.jpg',
        'audio_path' => 'theatres/river.mp3',
        'status' => 'published',
    ]);

    expect($theatre->slug)->toBe('the-river-speaks');

    $user = User::factory()->create(['is_admin' => false]);

    $this->actingAs($user)->get('/activities/the-river-speaks')
        ->assertOk()
        ->assertSee('property="og:title" content="The River Speaks — Les Stars Du Theatre"', false)
        ->assertSee('property="og:image" content="'.url(Storage::url('theatres/river.jpg')).'"', false);

    $this->actingAs($user)->get('/activities/'.$theatre->id)->assertRedirect('/activities/the-river-speaks');
});

it('requires sign-in to view activities', function (): void {
    $this->get(route('activities'))
        ->assertRedirect(route('auth.login'));
});

it('requires sign-in to play uploaded theatre audio', function (): void {
    Storage::fake('local');
    $theatre = Theatre::create([
        'title' => 'The River Speaks',
        'description' => 'An original audio theatre production.',
        'audio_path' => 'theatres/river.mp3',
        'status' => 'published',
    ]);
    Storage::disk('local')->put($theatre->audio_path, 'audio content');

    $this->get(route('theatres.audio', $theatre))
        ->assertRedirect(route('auth.login'));

    $this->actingAs(User::factory()->create(['is_admin' => false]))
        ->get(route('theatres.audio', $theatre))
        ->assertOk();
});
