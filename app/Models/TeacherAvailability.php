<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherAvailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'weekday',
        'starts_at',
        'ends_at',
        'is_active',
    ];

    protected $casts = [
        'weekday' => 'integer',
        'is_active' => 'boolean',
        'starts_at' => 'datetime:H:i:s',
        'ends_at' => 'datetime:H:i:s',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
