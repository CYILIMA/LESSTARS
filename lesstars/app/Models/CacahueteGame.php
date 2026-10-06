<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CacahueteGame extends Model
{
    protected $fillable = ['title', 'message', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function participants(): HasMany
    {
        return $this->hasMany(CacahueteParticipant::class);
    }
}
