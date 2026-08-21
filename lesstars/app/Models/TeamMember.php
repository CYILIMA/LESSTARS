<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TeamMember extends Model
{
    /** @use HasFactory<\Database\Factories\TeamMemberFactory> */
    use HasFactory;

    public const CATEGORIES = [
        'current_leadership' => 'Current Leadership',
        'current_stars' => 'Current Stars',
        'former_leadership' => 'Former Leadership',
        'former_stars' => 'Former Stars',
    ];

    protected $fillable = ['name', 'role', 'bio', 'email', 'phone', 'image_url', 'instagram_url', 'linkedin_url', 'display_order', 'status', 'category'];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeInCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? self::CATEGORIES['current_stars'];
    }

    public function getProfileImageUrlAttribute(): ?string
    {
        if (blank($this->image_url)) {
            return null;
        }

        if (str_starts_with($this->image_url, 'http://') || str_starts_with($this->image_url, 'https://')) {
            return $this->image_url;
        }

        return Storage::disk('public')->url($this->image_url);
    }
}
