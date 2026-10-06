<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContributionPayment extends Model
{
    protected $fillable = ['member_contribution_id', 'user_id', 'is_paid', 'paid_at', 'is_refunded', 'refunded_at', 'refund_reason'];

    protected function casts(): array
    {
        return ['is_paid' => 'boolean', 'paid_at' => 'datetime', 'is_refunded' => 'boolean', 'refunded_at' => 'datetime'];
    }

    public function contribution(): BelongsTo
    {
        return $this->belongsTo(MemberContribution::class, 'member_contribution_id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
