<?php

namespace App\Models;

use Database\Factories\HomepagePhotoFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class HomepagePhoto extends Model
{
    /** @use HasFactory<HomepagePhotoFactory> */
    use HasFactory;

    protected $fillable = ['image_path', 'description', 'display_order', 'status'];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function getImageUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->image_path);
    }
}
