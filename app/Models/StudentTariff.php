<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentTariff extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'price_per_lesson',
        'currency',
        'starts_on',
        'ends_on',
    ];

    protected $casts = [
        'price_per_lesson' => 'decimal:2',
        'starts_on' => 'date',
        'ends_on' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function scopeActive($query, $date = null)
    {
        $date = $date ?? now()->toDateString();

        return $query
            ->where('starts_on', '<=', $date)
            ->where(function ($query) use ($date) {
                $query->whereNull('ends_on')->orWhere('ends_on', '>=', $date);
            });
    }
}
