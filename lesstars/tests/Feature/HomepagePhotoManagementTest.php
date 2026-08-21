<?php

use App\Models\HomepagePhoto;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('shows published homepage photos and hides drafts', function (): void {
    HomepagePhoto::factory()->create([
        'description' => 'A live performance on stage',
        'status' => 'published',
    ]);
    HomepagePhoto::factory()->create([
        'description' => 'A private draft photo',
        'status' => 'draft',
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('A live performance on stage')
        ->assertDontSee('A private draft photo');
});

it('allows an admin to add a homepage photo', function (): void {
    $admin = User::factory()->create();
    Storage::fake('public');

    $response = $this->actingAs($admin)->post(route('admin.homepage-photos.store'), [
        'image' => UploadedFile::fake()->createWithContent(
            'stage.png',
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9WlH1f8AAAAASUVORK5CYII='),
        ),
        'description' => 'Young performers share a story.',
        'display_order' => 2,
        'status' => 'published',
    ]);

    $response->assertRedirect(route('admin.homepage-photos.index'));
    $this->assertDatabaseHas('homepage_photos', [
        'description' => 'Young performers share a story.',
        'display_order' => 2,
        'status' => 'published',
    ]);

    $photo = HomepagePhoto::where('description', 'Young performers share a story.')->firstOrFail();
    Storage::disk('public')->assertExists($photo->image_path);
});
