<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Carbon\Carbon;

class StudentController extends Controller
{
    /**
     * Display a listing of students.
     */
    public function index()
    {
        // Get the student role
        $studentRole = Role::where('name', 'student')->first();
        
        if (!$studentRole) {
            return redirect()->back()->with('error', 'Student role not found');
        }
        
        // Get all students with their enrollments count and last activity
        $students = User::where('role_id', $studentRole->id)
            ->withCount('enrollments')
            ->withCount('submissions')
            ->orderBy('name')
            ->get()
            ->map(function($student) {
                $lastSubmission = $student->submissions()->latest()->first();
                
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'email' => $student->email,
                    'enrollments_count' => $student->enrollments_count,
                    'submissions_count' => $student->submissions_count,
                    'last_activity' => $lastSubmission ? Carbon::parse($lastSubmission->created_at)->diffForHumans() : 'Never',
                    'created_at' => Carbon::parse($student->created_at)->format('M d, Y')
                ];
            });
        
        return Inertia::render('Students/Index', [
            'students' => $students
        ]);
    }

    /**
     * Show the form for creating a new student.
     */
    public function create()
    {
        return Inertia::render('Students/Create');
    }

    /**
     * Store a newly created student in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Get student role
        $studentRole = Role::where('name', 'student')->first();
        
        if (!$studentRole) {
            return redirect()->back()->with('error', 'Student role not found');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $studentRole->id,
        ]);

        return redirect()->route('students.index')
            ->with('success', 'Student created successfully');
    }

    /**
     * Display the specified student.
     */
    public function show(User $student)
    {
        // Get student's enrolled courses with progress
        $enrolledCourses = $student->enrolledCourses()
            ->with(['modules.chapters.exercises'])
            ->get()
            ->map(function($course) use ($student) {
                $totalExercises = $course->modules->flatMap(function($module) {
                    return $module->chapters->flatMap(function($chapter) {
                        return $chapter->exercises;
                    });
                })->count();

                $completedExercises = Submission::where('user_id', $student->id)
                    ->whereIn('exercise_id', $course->modules->flatMap(function($module) {
                        return $module->chapters->flatMap(function($chapter) {
                            return $chapter->exercises->pluck('id');
                        });
                    }))
                    ->where('status', 'completed')
                    ->count();

                $progress = $totalExercises > 0 ? round(($completedExercises / $totalExercises) * 100) : 0;

                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'description' => $course->description,
                    'progress' => $progress,
                    'total_exercises' => $totalExercises,
                    'completed_exercises' => $completedExercises,
                    'enrolled_at' => Carbon::parse($course->pivot->created_at)->format('M d, Y')
                ];
            });

        // Get recent submissions
        $recentSubmissions = $student->submissions()
            ->with(['exercise.chapter.module.course'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function($submission) {
                return [
                    'id' => $submission->id,
                    'exercise' => [
                        'id' => $submission->exercise->id,
                        'title' => $submission->exercise->title
                    ],
                    'course' => [
                        'id' => $submission->exercise->chapter->module->course->id,
                        'title' => $submission->exercise->chapter->module->course->title
                    ],
                    'status' => $submission->status,
                    'score' => $submission->score,
                    'submitted_at' => Carbon::parse($submission->created_at)->diffForHumans()
                ];
            });

        // Get activity statistics
        $submissionsCount = $student->submissions()->count();
        $completedCount = $student->submissions()->where('status', 'completed')->count();
        $averageScore = $student->submissions()->where('status', 'completed')->avg('score') ?? 0;
        
        // Calculate weekly activity for the last 6 weeks
        $weeklyActivity = [];
        for ($i = 5; $i >= 0; $i--) {
            $startDate = Carbon::now()->subWeeks($i)->startOfWeek();
            $endDate = Carbon::now()->subWeeks($i)->endOfWeek();
            
            $submissionsCount = $student->submissions()
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
                
            $weeklyActivity[] = [
                'week' => $startDate->format('M d') . ' - ' . $endDate->format('M d'),
                'submissions' => $submissionsCount
            ];
        }

        // Get available courses for enrollment
        $availableCourses = Course::whereDoesntHave('enrollments', function($query) use ($student) {
            $query->where('user_id', $student->id);
        })->get();

        return Inertia::render('Students/Show', [
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
                'created_at' => Carbon::parse($student->created_at)->format('M d, Y')
            ],
            'enrolledCourses' => $enrolledCourses,
            'recentSubmissions' => $recentSubmissions,
            'activityStats' => [
                'submissionsCount' => $submissionsCount,
                'completedCount' => $completedCount,
                'averageScore' => round($averageScore, 1),
                'enrollmentsCount' => $student->enrollments()->count()
            ],
            'weeklyActivity' => $weeklyActivity,
            'availableCourses' => $availableCourses->map(function($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title
                ];
            })
        ]);
    }

    /**
     * Show the form for editing the specified student.
     */
    public function edit(User $student)
    {
        return Inertia::render('Students/Edit', [
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email
            ]
        ]);
    }

    /**
     * Update the specified student in storage.
     */
    public function update(Request $request, User $student)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $student->id,
            'password' => $request->password ? ['confirmed', Rules\Password::defaults()] : '',
        ]);

        $student->name = $request->name;
        $student->email = $request->email;
        
        if ($request->password) {
            $student->password = Hash::make($request->password);
        }
        
        $student->save();

        return redirect()->route('students.show', $student)
            ->with('success', 'Student updated successfully');
    }

    /**
     * Remove the specified student from storage.
     */
    public function destroy(User $student)
    {
        // Delete related records
        $student->enrollments()->delete();
        $student->submissions()->delete();
        $student->sentMessages()->delete();
        $student->receivedMessages()->delete();
        
        // Delete the student
        $student->delete();

        return redirect()->route('students.index')
            ->with('success', 'Student deleted successfully');
    }

    /**
     * Enroll a student in a course.
     */
    public function enroll(Request $request, User $student)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id'
        ]);

        // Check if student is already enrolled
        $existingEnrollment = CourseEnrollment::where('user_id', $student->id)
            ->where('course_id', $request->course_id)
            ->first();

        if ($existingEnrollment) {
            return redirect()->back()->with('error', 'Student is already enrolled in this course');
        }

        // Create enrollment
        CourseEnrollment::create([
            'user_id' => $student->id,
            'course_id' => $request->course_id,
            'status' => 'active'
        ]);

        return redirect()->back()->with('success', 'Student enrolled successfully');
    }

    /**
     * Remove a student from a course.
     */
    public function unenroll(Request $request, User $student)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id'
        ]);

        // Find and delete enrollment
        $enrollment = CourseEnrollment::where('user_id', $student->id)
            ->where('course_id', $request->course_id)
            ->first();

        if (!$enrollment) {
            return redirect()->back()->with('error', 'Student is not enrolled in this course');
        }

        $enrollment->delete();

        return redirect()->back()->with('success', 'Student unenrolled successfully');
    }
}
