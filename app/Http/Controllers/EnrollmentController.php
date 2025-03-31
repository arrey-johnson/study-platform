<?php

namespace App\Http\Controllers;

use App\Models\CourseEnrollment;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class EnrollmentController extends Controller
{
    /**
     * Display a listing of the enrollments.
     *
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', CourseEnrollment::class);
        
        $enrollments = CourseEnrollment::with(['student', 'course'])
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->through(function ($enrollment) {
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
            
        // Get enrollment statistics
        $totalEnrollments = CourseEnrollment::count();
        $totalStudents = User::whereHas('role', function($query) {
            $query->where('name', 'student');
        })->count();
        $totalCourses = Course::count();
        
        // Get most popular courses
        $popularCourses = DB::table('course_enrollments')
            ->join('courses', 'course_enrollments.course_id', '=', 'courses.id')
            ->select('courses.id', 'courses.title', DB::raw('count(*) as enrollment_count'))
            ->groupBy('courses.id', 'courses.title')
            ->orderBy('enrollment_count', 'desc')
            ->take(5)
            ->get();
            
        return Inertia::render('Enrollments/Index', [
            'enrollments' => $enrollments,
            'stats' => [
                'totalEnrollments' => $totalEnrollments,
                'totalStudents' => $totalStudents,
                'totalCourses' => $totalCourses
            ],
            'popularCourses' => $popularCourses
        ]);
    }

    /**
     * Show the form for creating a new enrollment.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        $this->authorize('create', CourseEnrollment::class);
        
        // Get all available courses
        $courses = Course::where('is_active', true)
            ->orderBy('title')
            ->get(['id', 'title', 'description']);
            
        // Get all students
        $students = User::whereHas('role', function($query) {
            $query->where('name', 'student');
        })
        ->orderBy('name')
        ->get(['id', 'name', 'email']);
        
        return Inertia::render('Enrollments/Create', [
            'courses' => $courses,
            'students' => $students
        ]);
    }
    
    /**
     * Store a newly created enrollment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->authorize('create', CourseEnrollment::class);
        
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'user_id' => 'required|exists:users,id',
            'send_notification' => 'boolean'
        ]);
        
        // Check if the user is a student
        $user = User::findOrFail($validated['user_id']);
        if (!$user->isStudent()) {
            return back()->withErrors(['user_id' => 'Selected user is not a student.']);
        }
        
        // Check if the student is already enrolled in this course
        $existingEnrollment = CourseEnrollment::where('user_id', $validated['user_id'])
            ->where('course_id', $validated['course_id'])
            ->first();
            
        if ($existingEnrollment) {
            return back()->withErrors(['user_id' => 'Student is already enrolled in this course.']);
        }
        
        // Create the enrollment
        $enrollment = CourseEnrollment::create([
            'course_id' => $validated['course_id'],
            'user_id' => $validated['user_id'],
            'enrolled_at' => now(),
            'last_accessed_at' => now(),
            'progress_percentage' => 0,
            'status' => 'active'
        ]);
        
        // Send notification email if requested
        if ($request->input('send_notification', false)) {
            $course = Course::findOrFail($validated['course_id']);
            
            // In a real application, you would use a proper mail template
            // For this example, we'll just log the notification
            \Log::info("Enrollment notification would be sent to {$user->email} for course: {$course->title}");
            
            // Uncomment this code to actually send emails in production
            /*
            Mail::send('emails.enrollment', [
                'user' => $user,
                'course' => $course
            ], function ($message) use ($user, $course) {
                $message->to($user->email, $user->name)
                    ->subject("You've been enrolled in {$course->title}");
            });
            */
        }
        
        return redirect()->route('enrollments.index')
            ->with('success', 'Student successfully enrolled in the course.');
    }
    
    /**
     * Show the form for batch enrolling students.
     *
     * @return \Inertia\Response
     */
    public function batchEnrollForm()
    {
        $this->authorize('create', CourseEnrollment::class);
        
        // Get all available courses
        $courses = Course::where('is_active', true)
            ->orderBy('title')
            ->get(['id', 'title', 'description']);
            
        // Get all students
        $students = User::whereHas('role', function($query) {
            $query->where('name', 'student');
        })
        ->orderBy('name')
        ->get(['id', 'name', 'email']);
        
        return Inertia::render('Enrollments/BatchEnroll', [
            'courses' => $courses,
            'students' => $students
        ]);
    }
    
    /**
     * Process batch enrollment of students.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function batchEnroll(Request $request)
    {
        $this->authorize('create', CourseEnrollment::class);
        
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'send_notification' => 'boolean'
        ]);
        
        $course = Course::findOrFail($validated['course_id']);
        $enrolledCount = 0;
        $alreadyEnrolledCount = 0;
        
        foreach ($validated['user_ids'] as $userId) {
            // Check if the user is a student
            $user = User::findOrFail($userId);
            if (!$user->isStudent()) {
                continue; // Skip non-students
            }
            
            // Check if already enrolled
            $existingEnrollment = CourseEnrollment::where('user_id', $userId)
                ->where('course_id', $validated['course_id'])
                ->first();
                
            if ($existingEnrollment) {
                $alreadyEnrolledCount++;
                continue; // Skip already enrolled students
            }
            
            // Create the enrollment
            CourseEnrollment::create([
                'course_id' => $validated['course_id'],
                'user_id' => $userId,
                'enrolled_at' => now(),
                'last_accessed_at' => now(),
                'progress_percentage' => 0,
                'status' => 'active'
            ]);
            
            $enrolledCount++;
            
            // Send notification email if requested
            if ($request->input('send_notification', false)) {
                // In a real application, you would use a proper mail template
                // For this example, we'll just log the notification
                \Log::info("Enrollment notification would be sent to {$user->email} for course: {$course->title}");
                
                // Uncomment this code to actually send emails in production
                /*
                Mail::send('emails.enrollment', [
                    'user' => $user,
                    'course' => $course
                ], function ($message) use ($user, $course) {
                    $message->to($user->email, $user->name)
                        ->subject("You've been enrolled in {$course->title}");
                });
                */
            }
        }
        
        $message = "{$enrolledCount} students successfully enrolled in the course.";
        if ($alreadyEnrolledCount > 0) {
            $message .= " {$alreadyEnrolledCount} students were already enrolled.";
        }
        
        return redirect()->route('enrollments.index')
            ->with('success', $message);
    }
    
    /**
     * Remove the specified enrollment from storage.
     *
     * @param  \App\Models\CourseEnrollment  $enrollment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(CourseEnrollment $enrollment)
    {
        $this->authorize('delete', $enrollment);
        
        $enrollment->delete();
        
        return redirect()->route('enrollments.index')
            ->with('success', 'Enrollment successfully removed.');
    }
}
