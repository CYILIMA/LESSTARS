<?php

use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('allows an admin to upload a website background image', function (): void {
    $admin = User::factory()->create();
    Storage::fake('public');

    $response = $this->actingAs($admin)->put(route('admin.settings.update'), [
        'site_background' => UploadedFile::fake()->createWithContent(
            'background.png',
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9WlH1f8AAAAASUVORK5CYII='),
        ),
    ]);

    $response->assertSessionHas('success');

    $backgroundPath = Setting::where('key', 'site_background_path')->value('value');

    expect($backgroundPath)->not->toBeNull();
    Storage::disk('public')->assertExists($backgroundPath);
});
