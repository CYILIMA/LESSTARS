<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MembershipApplicationAnswer extends Model
{
    protected $fillable = ['membership_application_id', 'membership_application_question_id', 'answer'];

    public function application(): BelongsTo
    {
        return $this->belongsTo(MembershipApplication::class, 'membership_application_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(MembershipApplicationQuestion::class, 'membership_application_question_id');
    }
}
