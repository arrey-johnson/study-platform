<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Submission;
use App\Models\Exercise;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display the reports dashboard.
     */
    public function index()
    {
        // Get all students for the student filter
        $students = User::whereHas('role', function($query) {
            $query->where('name', 'student');
        })->orderBy('name')->get(['id', 'name', 'email']);

        // Get all courses for the course filter
        $courses = Course::orderBy('title')->get(['id', 'title']);

        return Inertia::render('Reports/Index', [
            'students' => $students,
            'courses' => $courses
        ]);
    }

    /**
     * Generate student performance report.
     */
    public function student(Request $request)
    {
        $dateRange = $this->getDateRangeFromRequest($request);
        $studentId = $request->input('student_id');
        $courseId = $request->input('course_id');

        // Base query for submissions
        $query = Submission::query()
            ->with(['exercise', 'exercise.chapter'])
            ->when($dateRange, function($query, $dateRange) {
                return $query->whereBetween('created_at', $dateRange);
            });

        // Filter by student if provided
        if ($studentId) {
            $query->where('user_id', $studentId);
        }

        // Filter by course if provided
        if ($courseId) {
            $query->whereHas('exercise.chapter.module.course', function($query) use ($courseId) {
                $query->where('id', $courseId);
            });
        }

        // Get submissions
        $submissions = $query->orderBy('created_at', 'desc')->get();

        // Calculate statistics
        $totalSubmissions = $submissions->count();
        $completedExercises = $submissions->unique('exercise_id')->count();
        
        // Calculate average score
        $averageScore = 0;
        if ($totalSubmissions > 0) {
            $averageScore = round($submissions->avg('score'));
        }

        // Calculate completion rate
        $completionRate = 0;
        $totalExercises = Exercise::count();
        if ($totalExercises > 0) {
            $completionRate = round(($completedExercises / $totalExercises) * 100);
        }

        // Format recent submissions for display
        $recentSubmissions = $submissions->take(10)->map(function($submission) {
            return [
                'exercise' => $submission->exercise->title,
                'date' => Carbon::parse($submission->created_at)->format('M d, Y'),
                'score' => round($submission->score),
                'status' => $submission->score >= 70 ? 'Passed' : 'Failed'
            ];
        });

        return response()->json([
            'averageScore' => $averageScore,
            'completedExercises' => $completedExercises,
            'completionRate' => $completionRate,
            'recentSubmissions' => $recentSubmissions
        ]);
    }

    /**
     * Generate course performance report.
     */
    public function course(Request $request)
    {
        $dateRange = $this->getDateRangeFromRequest($request);
        $courseId = $request->input('course_id');

        // Base query for enrollments
        $enrollmentsQuery = Enrollment::query()
            ->when($dateRange, function($query, $dateRange) {
                return $query->whereBetween('created_at', $dateRange);
            });

        // Filter by course if provided
        if ($courseId) {
            $enrollmentsQuery->where('course_id', $courseId);
        }

        // Get enrollments
        $enrollments = $enrollmentsQuery->get();
        $enrolledStudents = $enrollments->count();

        // Calculate average completion
        $averageCompletion = 0;
        if ($enrolledStudents > 0) {
            $averageCompletion = round($enrollments->avg('progress'));
        }

        // Calculate average score from submissions
        $submissionsQuery = Submission::query()
            ->when($dateRange, function($query, $dateRange) {
                return $query->whereBetween('created_at', $dateRange);
            });

        if ($courseId) {
            $submissionsQuery->whereHas('exercise.chapter.module.course', function($query) use ($courseId) {
                $query->where('id', $courseId);
            });
        }

        $submissions = $submissionsQuery->get();
        $averageScore = 0;
        if ($submissions->count() > 0) {
            $averageScore = round($submissions->avg('score'));
        }

        // Calculate active students (students with submissions in the last 7 days)
        $activeStudents = Submission::whereHas('user', function($query) {
                $query->whereHas('role', function($q) {
                    $q->where('name', 'student');
                });
            })
            ->when($courseId, function($query, $courseId) {
                return $query->whereHas('exercise.chapter.module.course', function($q) use ($courseId) {
                    $q->where('id', $courseId);
                });
            })
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->distinct('user_id')
            ->count('user_id');

        // Get top performing students
        $topStudents = User::whereHas('role', function($query) {
                $query->where('name', 'student');
            })
            ->whereHas('enrollments', function($query) use ($courseId) {
                if ($courseId) {
                    $query->where('course_id', $courseId);
                }
            })
            ->withCount(['submissions' => function($query) use ($dateRange, $courseId) {
                $query->when($dateRange, function($q, $dateRange) {
                    return $q->whereBetween('created_at', $dateRange);
                });
                if ($courseId) {
                    $query->whereHas('exercise.chapter.module.course', function($q) use ($courseId) {
                        $q->where('id', $courseId);
                    });
                }
            }])
            ->withAvg(['submissions' => function($query) use ($dateRange, $courseId) {
                $query->when($dateRange, function($q, $dateRange) {
                    return $q->whereBetween('created_at', $dateRange);
                });
                if ($courseId) {
                    $query->whereHas('exercise.chapter.module.course', function($q) use ($courseId) {
                        $q->where('id', $courseId);
                    });
                }
            }], 'score')
            ->orderByDesc('submissions_avg_score')
            ->take(5)
            ->get()
            ->map(function($student) {
                return [
                    'name' => $student->name,
                    'email' => $student->email,
                    'completion' => rand(60, 100), // This would be calculated from actual progress data
                    'averageScore' => round($student->submissions_avg_score ?? 0),
                    'lastActivity' => $student->submissions->sortByDesc('created_at')->first() 
                        ? Carbon::parse($student->submissions->sortByDesc('created_at')->first()->created_at)->format('M d, Y')
                        : 'Never'
                ];
            });

        return response()->json([
            'enrolledStudents' => $enrolledStudents,
            'averageCompletion' => $averageCompletion,
            'averageScore' => $averageScore,
            'activeStudents' => $activeStudents,
            'topStudents' => $topStudents
        ]);
    }

    /**
     * Generate activity report.
     */
    public function activity(Request $request)
    {
        $dateRange = $this->getDateRangeFromRequest($request);
        $activityType = $request->input('activity_type');

        // Calculate login statistics (simulated for this example)
        $totalLogins = rand(500, 2000);
        
        // Calculate submission statistics
        $submissionsQuery = Submission::query()
            ->when($dateRange, function($query, $dateRange) {
                return $query->whereBetween('created_at', $dateRange);
            });
        $totalSubmissions = $submissionsQuery->count();
        
        // Calculate enrollment statistics
        $enrollmentsQuery = Enrollment::query()
            ->when($dateRange, function($query, $dateRange) {
                return $query->whereBetween('created_at', $dateRange);
            });
        $newEnrollments = $enrollmentsQuery->count();
        
        // Calculate content view statistics (simulated for this example)
        $contentViews = rand(1000, 5000);
        
        // Generate recent activity data
        $recentActivity = collect();
        
        // Add login activities (simulated)
        for ($i = 0; $i < 5; $i++) {
            $user = User::inRandomOrder()->first();
            $recentActivity->push([
                'user' => $user->name,
                'type' => 'login',
                'details' => 'Logged in to the platform',
                'datetime' => Carbon::now()->subHours(rand(1, 48))->format('M d, Y H:i')
            ]);
        }
        
        // Add submission activities
        $submissions = Submission::with(['user', 'exercise'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        foreach ($submissions as $submission) {
            $recentActivity->push([
                'user' => $submission->user->name,
                'type' => 'submission',
                'details' => "Submitted answer for {$submission->exercise->title}",
                'datetime' => Carbon::parse($submission->created_at)->format('M d, Y H:i')
            ]);
        }
        
        // Add enrollment activities
        $enrollments = Enrollment::with(['user', 'course'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        foreach ($enrollments as $enrollment) {
            $recentActivity->push([
                'user' => $enrollment->user->name,
                'type' => 'enrollment',
                'details' => "Enrolled in {$enrollment->course->title}",
                'datetime' => Carbon::parse($enrollment->created_at)->format('M d, Y H:i')
            ]);
        }
        
        // Add content view activities (simulated)
        for ($i = 0; $i < 5; $i++) {
            $user = User::inRandomOrder()->first();
            $course = Course::inRandomOrder()->first();
            $recentActivity->push([
                'user' => $user->name,
                'type' => 'content_view',
                'details' => "Viewed content in {$course->title}",
                'datetime' => Carbon::now()->subHours(rand(1, 48))->format('M d, Y H:i')
            ]);
        }
        
        // Filter by activity type if provided
        if ($activityType) {
            $recentActivity = $recentActivity->filter(function($activity) use ($activityType) {
                return $activity['type'] === $activityType;
            })->values();
        }
        
        // Sort by datetime (most recent first)
        $recentActivity = $recentActivity->sortByDesc('datetime')->take(10)->values();
        
        return response()->json([
            'totalLogins' => $totalLogins,
            'totalSubmissions' => $totalSubmissions,
            'newEnrollments' => $newEnrollments,
            'contentViews' => $contentViews,
            'recentActivity' => $recentActivity
        ]);
    }

    /**
     * Export report data.
     */
    public function export($type)
    {
        // This would be implemented to export data in CSV or other formats
        return response()->json(['message' => "Export of {$type} report is not implemented yet."]);
    }

    /**
     * Get date range from request.
     */
    private function getDateRangeFromRequest(Request $request)
    {
        $dateRange = $request->input('date_range');
        
        if (!$dateRange || $dateRange === 'all') {
            return null;
        }
        
        $days = (int) $dateRange;
        return [Carbon::now()->subDays($days), Carbon::now()];
    }
}
