<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\LessonEnrollment;
use App\Models\TeacherMessage;
use App\Models\TestResult;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $cacheTtl   = now()->addMinutes(10);
        $monthStart = Carbon::now()->startOfMonth();
        $monthKey   = $monthStart->format('Y-m');

        $totals = Cache::remember('dashboard.admin.totals', $cacheTtl, function () {
            $roleCounts = User::query()
                ->selectRaw('role, COUNT(*) as total')
                ->whereIn('role', ['student', 'teacher'])
                ->groupBy('role')
                ->pluck('total', 'role');

            return [
                'students' => (int) $roleCounts->get('student', 0),
                'teachers' => (int) $roleCounts->get('teacher', 0),
                'lessons'  => Lesson::count(),
                'tests'    => TestResult::count(),
            ];
        });

        $monthlyStats = Cache::remember("dashboard.admin.monthly.{$monthKey}", $cacheTtl, function () use ($monthStart) {
            return [
                'monthlyLessons'     => Lesson::where('created_at', '>=', $monthStart)->count(),
                'monthlyEnrollments' => LessonEnrollment::where('created_at', '>=', $monthStart)->count(),
                'completionRate'     => TestResult::where('status', 'completed')->count(),
            ];
        });

        $upcomingLessons = Lesson::query()
            ->with('teacher')
            ->whereNotNull('starts_at')
            ->where('starts_at', '>=', Carbon::now())
            ->orderBy('starts_at')
            ->take(5)
            ->get();

        $recentEnrollments = LessonEnrollment::query()
            ->with(['student', 'lesson'])
            ->latest()
            ->take(5)
            ->get();

        $messages = TeacherMessage::query()
            ->with(['sender', 'recipient'])
            ->latest('sent_at')
            ->take(5)
            ->get();

        return view('dashboards.admin', [
            'totals'            => $totals,
            'upcomingLessons'   => $upcomingLessons,
            'recentEnrollments' => $recentEnrollments,
            'recentMessages'    => $messages,
            'monthlyLessons'    => $monthlyStats['monthlyLessons'],
            'monthlyEnrollments'=> $monthlyStats['monthlyEnrollments'],
            'completionRate'    => $monthlyStats['completionRate'],
        ]);
    }
}
