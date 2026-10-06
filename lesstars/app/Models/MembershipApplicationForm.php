<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MembershipApplicationForm extends Model
{
    protected $fillable = ['title', 'introduction', 'public_token', 'is_open'];

    protected function casts(): array
    {
        return ['is_open' => 'boolean'];
    }

    public function questions(): HasMany
    {
        return $this->hasMany(MembershipApplicationQuestion::class)->orderBy('sort_order');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(MembershipApplication::class);
    }

    public function getRouteKeyName(): string
    {
        return 'public_token';
    }
}
