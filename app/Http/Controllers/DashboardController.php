<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\Submission;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseEnrollment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use App\Models\Module;
use App\Models\Chapter;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        \Log::info('User: ' . ($user ? $user->id : 'null'));
        \Log::info('User role: ' . ($user && $user->role ? $user->role->id : 'null'));
        
        if ($user && $user->role && $user->role->name === 'admin') {
            $dashboardData = $this->adminDashboard($user);
            return Inertia::render('Dashboard', [
                'adminStats' => [
                    'totalStudents' => $dashboardData['totalStudents'] ?? 0,
                    'totalCourses' => $dashboardData['totalCourses'] ?? 0,
                    'totalModules' => $dashboardData['totalModules'] ?? 0,
                    'totalChapters' => $dashboardData['totalChapters'] ?? 0,
                    'activeStudents' => $dashboardData['activeStudents'] ?? 0,
                    'completionRate' => $dashboardData['completionRate'] ?? 0
                ],
                'enrollmentChart' => $dashboardData['monthlyEnrollments'] ?? null,
                'completionChart' => $dashboardData['monthlyCompletions'] ?? null,
                'recentEnrollments' => $dashboardData['recentEnrollments'],
                'recentCompletions' => $dashboardData['recentCompletions'],
                'inactiveStudents' => $dashboardData['inactiveStudents'] ?? [],
                'coursePerformance' => $dashboardData['coursePerformance'],
                'studentEngagement' => $dashboardData['studentEngagement']
            ]);
        } else {
            // Redirect students to the dedicated student dashboard
            return redirect()->route('student.dashboard');
        }
    }

    private function studentDashboard(User $user)
    {
        // Get student progress data
        $completedChapters = $user->completedChapters()->count();
        
        // Calculate average comprehension rating
        $averageComprehension = $user->completedChapters()
            ->whereNotNull('chapter_completions.comprehension_rating')
            ->avg('chapter_completions.comprehension_rating') ?? 0;
        $averageComprehension = round($averageComprehension, 1);
        
        // Calculate total learning time (in hours)
        $totalLearningTime = $user->completedChapters()->sum('chapter_completions.time_spent') ?? 0;
        $totalLearningHours = round($totalLearningTime / 60, 1); // Convert minutes to hours
        
        // Calculate streak (placeholder logic - would be more complex in production)
        $streak = 5; // Placeholder for demo
        
        // Get enrolled courses with progress
        $enrollments = $user->enrollments()->with('course')->get();
        $courses = [];
        
        foreach ($enrollments as $enrollment) {
            $course = $enrollment->course;
            
            // Calculate course progress based on chapters
            $totalChapters = $course->modules()
                ->withCount('chapters')
                ->get()
                ->sum('chapters_count');
                
            $completedCourseChapters = $user->completedChapters()
                ->whereHas('module.course', function($query) use ($course) {
                    $query->where('courses.id', $course->id);
                })
                ->count();
            
            $progress = $totalChapters > 0 ? round(($completedCourseChapters / $totalChapters) * 100) : 0;
            
            $courses[] = [
                'id' => $course->id,
                'title' => $course->title,
                'description' => $course->description,
                'progress' => $progress,
                'total_chapters' => $totalChapters,
                'completed_chapters' => $completedCourseChapters,
                'image' => $course->image,
                'created_at' => $course->created_at,
            ];
        }
        
        // Get recently accessed chapters
        $recentlyAccessedChapters = $user->completedChapters()
            ->orderBy('chapter_completions.last_accessed_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($chapter) {
                return [
                    'id' => $chapter->id,
                    'title' => $chapter->title,
                    'module' => [
                        'id' => $chapter->module->id,
                        'title' => $chapter->module->title,
                        'course' => [
                            'id' => $chapter->module->course->id,
                            'title' => $chapter->module->course->title,
                        ]
                    ],
                    'last_accessed_at' => $chapter->pivot->last_accessed_at->diffForHumans(),
                    'time_spent' => $chapter->pivot->time_spent,
                    'comprehension_rating' => $chapter->pivot->comprehension_rating,
                ];
            });
            
        // Weekly progress data for chart
        $weeklyProgress = $this->getWeeklyProgressData($user);
        
        // Topic progress data for chart
        $topicProgress = $this->getTopicProgressData($user);
        
        return [
            'studentProgress' => [
                'completedChapters' => $completedChapters,
                'averageComprehension' => $averageComprehension,
                'totalLearningHours' => $totalLearningHours,
                'streak' => $streak,
            ],
            'courses' => $courses,
            'recentlyAccessedChapters' => $recentlyAccessedChapters,
            'weeklyProgress' => $weeklyProgress,
            'topicProgress' => $topicProgress
        ];
    }
    
    /**
     * Get weekly progress data for chart
     */
    private function getWeeklyProgressData(User $user) {
        $days = [];
        $completedCounts = [];
        
        // Get last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $days[] = $date->format('D');
            
            // Count completed chapters for this day
            $count = $user->completedChapters()
                ->whereDate('chapter_completions.completed_at', $date->format('Y-m-d'))
                ->count();
                
            $completedCounts[] = $count;
        }
        
        // Add some sample data if no completions exist (for testing/demo purposes)
        if (array_sum($completedCounts) === 0) {
            $completedCounts = [2, 4, 3, 5, 7, 4, 6];
        }
        
        return [
            'labels' => $days,
            'data' => $completedCounts,
        ];
    }
    
    /**
     * Get topic progress data for chart
     */
    private function getTopicProgressData(User $user) {
        // Get real topic data if available
        $enrolledCourseIds = $user->enrollments()->pluck('course_id')->toArray();
        
        // Get categories/topics from enrolled courses
        $categories = \DB::table('courses')
            ->select('category', \DB::raw('COUNT(id) as total_courses'))
            ->whereIn('id', $enrolledCourseIds)
            ->groupBy('category')
            ->get();
            
        $topics = [];
        
        if ($categories->count() > 0) {
            foreach ($categories as $category) {
                // Calculate progress for this category
                $categoryProgress = $this->calculateCategoryProgress($user, $category->category);
                
                $topics[] = [
                    'name' => $category->category,
                    'progress' => $categoryProgress
                ];
            }
        } else {
            // Sample data for demo/testing purposes
            $topics = [
                ['name' => 'Programming', 'progress' => 65],
                ['name' => 'Design', 'progress' => 40],
                ['name' => 'Mathematics', 'progress' => 75],
                ['name' => 'Business', 'progress' => 30],
            ];
        }
        
        return $topics;
    }
    
    /**
     * Calculate progress percentage for a specific category
     */
    private function calculateCategoryProgress(User $user, $category) {
        // Get all chapters from courses in this category
        $totalChapters = \DB::table('chapters')
            ->join('modules', 'chapters.module_id', '=', 'modules.id')
            ->join('courses', 'modules.course_id', '=', 'courses.id')
            ->where('courses.category', $category)
            ->count();
            
        // Get completed chapters in this category
        $completedChapters = $user->completedChapters()
            ->join('modules', 'chapters.module_id', '=', 'modules.id')
            ->join('courses', 'modules.course_id', '=', 'courses.id')
            ->where('courses.category', $category)
            ->count();
            
        // Calculate percentage
        $progress = $totalChapters > 0 ? round(($completedChapters / $totalChapters) * 100) : 0;
        
        return $progress;
    }

    private function adminDashboard(User $user)
    {
        // Basic stats - ensure we're getting real-time counts
        $totalStudents = User::whereHas('role', function($query) {
            $query->where('name', 'student');
        })->count();
        
        // Count only active courses
        $totalCourses = Course::where('is_active', true)->count();
        
        // Get modules and chapters counts - only count those from active courses
        $moduleCount = 0;
        $chapterCount = 0;
        
        // Get active courses with their modules and chapters
        $activeCourses = Course::where('is_active', true)->with('modules.chapters')->get();
        
        foreach ($activeCourses as $course) {
            $moduleCount += $course->modules->count();
            
            foreach ($course->modules as $module) {
                $chapterCount += $module->chapters->count();
            }
        }
        
        $totalModules = $moduleCount;
        $totalChapters = $chapterCount;
        
        // Active students (based on recent chapter completions)
        $activeStudents = User::whereHas('role', function($query) {
            $query->where('name', 'student');
        })
        ->whereHas('completedChapters', function($query) {
            $query->where('chapter_completions.created_at', '>=', now()->subDays(30));
        })
        ->count();
        
        // Calculate completion rate
        $totalEnrollments = CourseEnrollment::count();
        $completedEnrollments = CourseEnrollment::where('status', 'completed')->count();
        $completionRate = $totalEnrollments > 0 ? round(($completedEnrollments / $totalEnrollments) * 100) : 0;
        
        // Get recent enrollments (last 5)
        $recentEnrollments = CourseEnrollment::with(['student', 'course'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($enrollment) {
                return [
                    'id' => $enrollment->id,
                    'student' => $enrollment->student ? [
                        'id' => $enrollment->student->id,
                        'name' => $enrollment->student->name ?? 'Student #' . $enrollment->student->id
                    ] : ['id' => null, 'name' => 'Deleted Student'],
                    'course' => $enrollment->course ? [
                        'id' => $enrollment->course->id,
                        'title' => $enrollment->course->title ?? 'Course #' . $enrollment->course->id
                    ] : ['id' => null, 'title' => 'Deleted Course'],
                    'created_at' => $enrollment->created_at->format('M d, Y')
                ];
            });
        
        // Get recent chapter completions (last 5)
        $recentCompletions = DB::table('chapter_completions')
            ->join('users', 'chapter_completions.user_id', '=', 'users.id')
            ->join('chapters', 'chapter_completions.chapter_id', '=', 'chapters.id')
            ->join('modules', 'chapters.module_id', '=', 'modules.id')
            ->join('courses', 'modules.course_id', '=', 'courses.id')
            ->select(
                'chapter_completions.id',
                'chapter_completions.completed_at',
                'chapter_completions.time_spent',
                'chapter_completions.comprehension_rating',
                'users.id as user_id',
                'users.name as user_name',
                'chapters.id as chapter_id',
                'chapters.title as chapter_title',
                'modules.id as module_id',
                'modules.title as module_title',
                'courses.id as course_id',
                'courses.title as course_title'
            )
            ->orderBy('chapter_completions.completed_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($completion) {
                return [
                    'id' => $completion->id,
                    'student' => [
                        'id' => $completion->user_id,
                        'name' => $completion->user_name ?? 'Student #' . $completion->user_id
                    ],
                    'chapter' => [
                        'id' => $completion->chapter_id,
                        'title' => $completion->chapter_title ?? 'Chapter #' . $completion->chapter_id,
                        'module' => [
                            'id' => $completion->module_id,
                            'title' => $completion->module_title ?? 'Module #' . $completion->module_id,
                            'course' => [
                                'id' => $completion->course_id,
                                'title' => $completion->course_title ?? 'Course #' . $completion->course_id
                            ]
                        ]
                    ],
                    'completed_at' => Carbon::parse($completion->completed_at)->format('M d, Y'),
                    'time_spent' => $completion->time_spent ?? 0,
                    'comprehension_rating' => $completion->comprehension_rating ?? 0
                ];
            });
            
        // Calculate monthly enrollments for the chart
        $monthlyEnrollments = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $count = CourseEnrollment::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            $monthlyEnrollments[] = [
                'month' => $month->format('M'),
                'count' => $count
            ];
        }
        
        // Calculate monthly chapter completions for the chart
        $monthlyCompletions = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $count = DB::table('chapter_completions')
                ->whereYear('completed_at', $month->year)
                ->whereMonth('completed_at', $month->month)
                ->count();
            $monthlyCompletions[] = [
                'month' => $month->format('M'),
                'count' => $count
            ];
        }
        
        // Course performance data
        $coursePerformance = Course::withCount(['enrollments'])
            ->with(['modules.chapters'])
            ->get()
            ->map(function ($course) {
                $totalChapters = $course->modules->sum(function ($module) {
                    return $module->chapters->count();
                });
                
                $totalCompletions = DB::table('chapter_completions')
                    ->join('chapters', 'chapter_completions.chapter_id', '=', 'chapters.id')
                    ->join('modules', 'chapters.module_id', '=', 'modules.id')
                    ->where('modules.course_id', $course->id)
                    ->count();
                
                $avgComprehension = DB::table('chapter_completions')
                    ->join('chapters', 'chapter_completions.chapter_id', '=', 'chapters.id')
                    ->join('modules', 'chapters.module_id', '=', 'modules.id')
                    ->where('modules.course_id', $course->id)
                    ->whereNotNull('comprehension_rating')
                    ->avg('comprehension_rating') ?? 0;
                
                $avgTimeSpent = DB::table('chapter_completions')
                    ->join('chapters', 'chapter_completions.chapter_id', '=', 'chapters.id')
                    ->join('modules', 'chapters.module_id', '=', 'modules.id')
                    ->where('modules.course_id', $course->id)
                    ->avg('time_spent') ?? 0;
                    
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'enrollments' => $course->enrollments_count,
                    'totalChapters' => $totalChapters,
                    'completedChapters' => $totalCompletions,
                    'avgComprehension' => round($avgComprehension, 1),
                    'avgTimeSpent' => round($avgTimeSpent, 1),
                    'completionRate' => $totalChapters > 0 && $course->enrollments_count > 0
                        ? round(($totalCompletions / ($totalChapters * $course->enrollments_count)) * 100, 1)
                        : 0
                ];
            });
            
        // Student engagement data
        $studentEngagement = User::whereHas('role', function($query) {
                $query->where('name', 'student');
            })
            ->withCount(['completedChapters'])
            ->orderBy('completed_chapters_count', 'desc')
            ->take(5)
            ->get()
            ->map(function ($student) {
                $avgComprehension = DB::table('chapter_completions')
                    ->where('user_id', $student->id)
                    ->whereNotNull('comprehension_rating')
                    ->avg('comprehension_rating') ?? 0;
                
                $totalTimeSpent = DB::table('chapter_completions')
                    ->where('user_id', $student->id)
                    ->sum('time_spent') ?? 0;
                
                return [
                    'id' => $student->id,
                    'name' => $student->name ?? 'Unknown Student',
                    'completedChapters' => $student->completed_chapters_count ?? 0,
                    'avgComprehension' => round($avgComprehension, 1),
                    'totalTimeSpent' => round($totalTimeSpent / 60, 1) // Convert to hours
                ];
            });
            
        return [
            'totalStudents' => $totalStudents,
            'totalCourses' => $totalCourses,
            'totalModules' => $totalModules,
            'totalChapters' => $totalChapters,
            'activeStudents' => $activeStudents,
            'completionRate' => $completionRate,
            'recentEnrollments' => $recentEnrollments,
            'recentCompletions' => $recentCompletions,
            'monthlyEnrollments' => $monthlyEnrollments,
            'monthlyCompletions' => $monthlyCompletions,
            'coursePerformance' => $coursePerformance,
            'studentEngagement' => $studentEngagement
        ];
    }
}