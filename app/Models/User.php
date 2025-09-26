<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\StudyGroup;
use App\Models\LessonChangeRequest;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'username',
        'email',
        'role',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'full_name',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $user): void {
            if ($user->first_name !== null || $user->last_name !== null) {
                $user->name = trim(collect([
                    $user->first_name,
                    $user->last_name,
                ])->filter()->join(' '));
            }
        });
    }

    public function getFullNameAttribute(): string
    {
        $full = trim(collect([
            $this->first_name,
            $this->last_name,
        ])->filter()->join(' '));

        return $full !== '' ? $full : (string) ($this->name ?? '');
    }

    public function taughtLessons(): HasMany
    {
        return $this->hasMany(Lesson::class, 'teacher_id');
    }

    public function lessonEnrollments(): HasMany
    {
        return $this->hasMany(LessonEnrollment::class, 'student_id');
    }

    public function studentLessons(): BelongsToMany
    {
        return $this->belongsToMany(Lesson::class, 'lesson_enrollments', 'student_id', 'lesson_id');
    }

    public function createdTests(): HasMany
    {
        return $this->hasMany(StudyTest::class, 'teacher_id');
    }

    public function testAttempts(): HasMany
    {
        return $this->hasMany(TestAttempt::class, 'student_id');
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(TeacherMessage::class, 'sender_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(TeacherMessage::class, 'recipient_id');
    }

    public function vocabularyEntries(): HasMany
    {
        return $this->hasMany(VocabularyEntry::class, 'student_id');
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(TeacherAvailability::class, 'teacher_id');
    }

    public function availabilityExceptions(): HasMany
    {
        return $this->hasMany(TeacherAvailabilityException::class, 'teacher_id');
    }

    public function lessonSeries(): HasMany
    {
        return $this->hasMany(LessonSeries::class, 'teacher_id');
    }

    public function teachingGroups(): BelongsToMany
    {
        return $this->belongsToMany(StudyGroup::class, 'study_group_teacher', 'teacher_id', 'group_id')
            ->withPivot(['is_primary'])
            ->withTimestamps();
    }

    public function studyGroups(): BelongsToMany
    {
        return $this->belongsToMany(StudyGroup::class, 'study_group_student', 'student_id', 'group_id')
            ->withPivot(['joined_at'])
            ->withTimestamps();
    }

    public function tariffs(): HasMany
    {
        return $this->hasMany(StudentTariff::class, 'student_id');
    }

    public function monthlyStatements(): HasMany
    {
        return $this->hasMany(StudentMonthlyStatement::class, 'student_id');
    }

    public function submittedLessonChangeRequests(): HasMany
    {
        return $this->hasMany(LessonChangeRequest::class, 'requested_by_id');
    }

    public function handledLessonChangeRequests(): HasMany
    {
        return $this->hasMany(LessonChangeRequest::class, 'handled_by_id');
    }

    public function currentTariff(?Carbon $date = null): ?StudentTariff
    {
        $date = $date ?? Carbon::now();

        return $this->tariffs()
            ->active($date->toDateString())
            ->orderByDesc('starts_on')
            ->first();
    }


    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            'admin' => $this->isAdmin(),
            'teacher' => $this->isTeacher(),
            'student' => $this->isStudent(),
            default => false,
        };
    }
}
