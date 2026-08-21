<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Theatre extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'thumbnail_path', 'audio_path', 'status'];

    protected static function booted(): void
    {
        static::creating(function (self $theatre): void {
            $theatre->slug = static::uniqueSlug($theatre->title);
        });

        static::updating(function (self $theatre): void {
            if ($theatre->isDirty('title')) {
                $theatre->slug = static::uniqueSlug($theatre->title, $theatre->id);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TheatreComment::class)->whereNull('parent_id');
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->thumbnail_path ? Storage::disk('public')->url($this->thumbnail_path) : null;
    }

    private static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'theatre';
        $slug = $base;
        $suffix = 2;

        while (static::query()->where('slug', $slug)->when($ignoreId, fn (Builder $query) => $query->whereKeyNot($ignoreId))->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
