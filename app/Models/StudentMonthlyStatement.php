<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentMonthlyStatement extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'year',
        'month',
        'lessons_count',
        'amount_due',
        'amount_paid',
        'closed_at',
    ];

    protected $casts = [
        'lessons_count' => 'integer',
        'amount_due' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'closed_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function scopeForMonth($query, int $year, int $month)
    {
        return $query->where('year', $year)->where('month', $month);
    }
}
