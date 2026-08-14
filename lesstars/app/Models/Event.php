<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
class Event extends Model {
    protected $fillable = ['title','summary','venue','starts_at','ends_at','image_url','status'];
    protected function casts(): array { return ['starts_at' => 'datetime', 'ends_at' => 'datetime']; }
    public function scopePublished(Builder $query): Builder { return $query->where('status', 'published'); }
    public function scopeUpcoming(Builder $query): Builder { return $query->where('starts_at', '>=', now()); }
}
