<?php

namespace App\Filament\Admin\Pages;

use App\Models\Lesson;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Pages\Page;

class ScheduleCalendar extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Календар уроків';

    protected static ?string $navigationGroup = 'Розклад';

    protected static string $view = 'filament.admin.pages.schedule-calendar';

    public ?int $teacherId = null;

    public string $startsOn;

    public string $endsOn;

    public ?string $focusedDate = null;

    public bool $showDayModal = false;

    public array $focusedLessons = [];

    public int $modalPage = 1;

    public int $modalPerPage = 5;

    public function mount(): void
    {
        $this->startsOn = Carbon::now()->startOfWeek()->toDateString();
        $this->endsOn = Carbon::now()->endOfWeek()->toDateString();
    }

    public function previousWeek(): void
    {
        $this->startsOn = Carbon::parse($this->startsOn)->subWeek()->toDateString();
        $this->endsOn = Carbon::parse($this->endsOn)->subWeek()->toDateString();
    }

    public function nextWeek(): void
    {
        $this->startsOn = Carbon::parse($this->startsOn)->addWeek()->toDateString();
        $this->endsOn = Carbon::parse($this->endsOn)->addWeek()->toDateString();
    }

    protected function getViewData(): array
    {
        $lessons = Lesson::query()
            ->with(['teacher', 'students', 'group'])
            ->when($this->teacherId, fn ($query) => $query->where('teacher_id', $this->teacherId))
            ->whereBetween('scheduled_for', [$this->startsOn, $this->endsOn])
            ->orderBy('scheduled_for')
            ->orderBy('starts_at')
            ->get()
            ->groupBy(fn (Lesson $lesson) => optional($lesson->scheduled_for)->toDateString());

        $teachers = User::query()
            ->where('role', 'teacher')
            ->orderBy('last_name')
            ->get();

        $period = CarbonPeriod::create(Carbon::parse($this->startsOn), Carbon::parse($this->endsOn));

        $selectedTeacher = $teachers->firstWhere('id', $this->teacherId);

        if ($selectedTeacher) {
            $selectedTeacher->loadMissing('availabilities');
        }

        return [
            'lessonsByDay' => $lessons,
            'teachers' => $teachers,
            'period' => $period,
            'selectedTeacher' => $selectedTeacher,
        ];
    }

    public function openDay(string $date): void
    {
        $this->focusedDate = $date;
        $this->focusedLessons = Lesson::query()
            ->with(['teacher', 'students', 'group'])
            ->when($this->teacherId, fn ($query) => $query->where('teacher_id', $this->teacherId))
            ->whereDate('scheduled_for', $date)
            ->orderBy('starts_at')
            ->get()
            ->map(fn (Lesson $lesson) => [
                'id' => $lesson->id,
                'title' => $lesson->title,
                'time' => optional($lesson->starts_at)->format('H:i') ?? '—',
                'duration' => $lesson->duration_minutes,
                'teacher' => $lesson->teacher?->full_name ?: $lesson->teacher?->username ?: '—',
                'format' => $lesson->format === 'group' ? 'Групове' : 'Індивідуальне',
                'is_temporary' => (bool) $lesson->is_temporary,
                'is_series' => (bool) $lesson->series_id,
                'group' => $lesson->group?->name,
                'students' => $lesson->students->map(fn ($student) => [
                    'name' => $student->full_name ?: $student->username,
                    'id' => $student->id,
                ])->all(),
            ])
            ->all();

        $this->modalPage = 1;

        $this->showDayModal = true;
        $this->dispatch('open-modal', id: 'day-modal');
    }

    public function closeDay(): void
    {
        $this->focusedDate = null;
        $this->focusedLessons = [];
        $this->modalPage = 1;
        $this->showDayModal = false;
        $this->dispatch('close-modal', id: 'day-modal');
    }

    public function getModalPaginatedProperty()
    {
        $collection = collect($this->focusedLessons);

        $chunks = $collection->chunk($this->modalPerPage);

        return [
            'items' => $chunks->get($this->modalPage - 1, collect()),
            'pages' => max($chunks->count(), 1),
        ];
    }

    public function nextModalPage(): void
    {
        $pages = $this->getModalPaginatedProperty()['pages'];

        if ($this->modalPage < $pages) {
            $this->modalPage++;
        }
    }

    public function previousModalPage(): void
    {
        if ($this->modalPage > 1) {
            $this->modalPage--;
        }
    }
}
