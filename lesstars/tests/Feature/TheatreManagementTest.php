<?php

use App\Models\Theatre;
use App\Models\TheatreComment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('allows an admin to upload published theatre audio', function (): void {
    Storage::fake('public');
    $admin = User::factory()->create();

    $this->actingAs($admin)->post(route('admin.theatres.store'), [
        'title' => 'The River Speaks',
        'description' => 'An original audio theatre production.',
        'thumbnail' => UploadedFile::fake()->createWithContent('river.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9WlH1f8AAAAASUVORK5CYII=')),
        'audio' => UploadedFile::fake()->create('river.mp3', 100, 'audio/mpeg'),
        'status' => 'published',
    ])->assertRedirect(route('admin.theatres.index'));

    $this->assertDatabaseHas('theatres', ['title' => 'The River Speaks', 'status' => 'published']);
});

it('keeps public comments pending until an admin approves them', function (): void {
    $theatre = Theatre::create(['title' => 'The River Speaks', 'description' => 'An original audio theatre production.', 'audio_path' => 'theatres/river.mp3', 'status' => 'published']);

    $this->post(route('theatres.comments.store', $theatre), ['name' => 'Amahle', 'email' => 'amahle@example.test', 'body' => 'A moving performance.'])->assertSessionHas('success');
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

    $this->get('/activities/the-river-speaks')
        ->assertOk()
        ->assertSee('property="og:title" content="The River Speaks — Les Stars Du Theatre"', false)
        ->assertSee('property="og:image" content="http://localhost:8000/storage/theatres/river.jpg"', false);

    $this->get('/activities/'.$theatre->id)->assertRedirect('/activities/the-river-speaks');
});
