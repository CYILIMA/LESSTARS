<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendanceSession extends Model
{
    protected $fillable = ['title', 'attendance_date'];

    protected function casts(): array
    {
        return ['attendance_date' => 'date'];
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
