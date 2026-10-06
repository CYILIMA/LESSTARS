<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MembershipApplicationQuestion extends Model
{
    protected $fillable = ['membership_application_form_id', 'question', 'type', 'options', 'is_required', 'sort_order'];

    protected function casts(): array
    {
        return ['options' => 'array', 'is_required' => 'boolean'];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(MembershipApplicationForm::class, 'membership_application_form_id');
    }
}
