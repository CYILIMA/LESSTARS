<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MembershipApplication extends Model
{
    protected $fillable = ['membership_application_form_id', 'applicant_name', 'applicant_email', 'status', 'decision_notes', 'reviewed_by', 'reviewed_at'];

    protected function casts(): array
    {
        return ['reviewed_at' => 'datetime'];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(MembershipApplicationForm::class, 'membership_application_form_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(MembershipApplicationAnswer::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
