<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CacahueteParticipant extends Model
{
    protected $fillable = ['cacahuete_game_id', 'name', 'email', 'recipient_id', 'picked_at'];

    protected function casts(): array
    {
        return ['picked_at' => 'datetime'];
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(CacahueteGame::class, 'cacahuete_game_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(self::class, 'recipient_id');
    }
}
