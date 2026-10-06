<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MemberContribution extends Model
{
    protected $fillable = ['purpose', 'amount', 'currency', 'mobile_money_number', 'mobile_money_code', 'payment_instructions'];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2'];
    }

    public function payments(): HasMany
    {
        return $this->hasMany(ContributionPayment::class);
    }
}
