<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Category;
use App\Models\CourseEnrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;

class CourseController extends Controller
{
    use AuthorizesRequests, ValidatesRequests, DispatchesJobs;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            $courses = Course::with('enrollments')
                ->withCount('enrollments')
                ->latest()
                ->paginate(9);
        } else {
            // For students, show all active courses and indicate which ones they're enrolled in
            $courses = Course::where('is_active', true)
                ->with(['enrollments' => function($query) use ($user) {
                    $query->where('user_id', $user->id);
                }])
                ->withCount('enrollments')
                ->latest()
                ->paginate(9);
                
            // Add a flag to each course indicating if the user is enrolled
            $courses->through(function($course) use ($user) {
                $course->is_enrolled = $course->isEnrolledBy($user);
                return $course;
            });
        }

        return Inertia::render('Courses/Index', [
            'courses' => $courses
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Course::class);

        $categories = Category::all();
        
        return Inertia::render('Courses/Create', [
            'categories' => $categories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Course::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'is_active' => 'boolean'
        ]);

        // Add the current user as the creator
        $validated['created_by'] = Auth::id();
        
        // Create the course
        $course = Course::create($validated);

        return redirect()->route('courses.index')
            ->with('success', 'Course created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        $user = Auth::user();
        $progress = null;

        // Load the course with its modules and chapters
        $course->load(['modules.chapters.exercises']);
        
        if (!$user->isAdmin()) {
            $enrollment = $course->enrollments()->where('user_id', $user->id)->first();
            if ($enrollment) {
                // Update the progress and use the progress_percentage field
                $enrollment->updateProgress();
                $progress = $enrollment->progress_percentage;
                
                // Mark chapters as completed for the user
                $completedChapterIds = $user->completedChapters()
                    ->wherePivot('completed_at', '!=', null)
                    ->pluck('chapters.id')
                    ->toArray();
                
                // Add completion status to each chapter
                foreach ($course->modules as $module) {
                    foreach ($module->chapters as $chapter) {
                        $chapter->is_completed = in_array($chapter->id, $completedChapterIds);
                    }
                }
            }
        }

        return Inertia::render('Courses/Show', [
            'course' => $course,
            'progress' => $progress
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        $this->authorize('update', $course);

        return Inertia::render('Courses/Edit', [
            'course' => $course
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        $this->authorize('update', $course);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $course->update($validated);

        return redirect()->route('courses.index')
            ->with('message', 'Course updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        $this->authorize('delete', $course);

        $course->delete();

        return redirect()->route('courses.index')
            ->with('message', 'Course deleted successfully.');
    }

    /**
     * Enroll a student in a course (admin only).
     *
     * @param Course $course
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function enroll(Course $course, Request $request)
    {
        $this->authorize('enroll', $course);

        // If a user_id is provided, use that (for admin enrolling a student)
        // Otherwise, use the authenticated user's ID (this path is now blocked by policy)
        $userId = $request->input('user_id', Auth::id());
        
        // Check if the user exists and is a student
        $user = User::findOrFail($userId);
        if (!$user->isStudent()) {
            return back()->with('error', 'Only students can be enrolled in courses.');
        }

        if ($course->isEnrolledBy($user)) {
            return back()->with('error', 'Student is already enrolled in this course.');
        }

        $course->enrollments()->create([
            'user_id' => $userId,
            'enrolled_at' => now(),
            'last_accessed_at' => now(),
            'progress_percentage' => 0,
            'status' => 'active'
        ]);

        return back()->with('success', 'Student successfully enrolled in the course.');
    }

    /**
     * Unenroll a student from a course (admin only).
     *
     * @param Course $course
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unenroll(Course $course, Request $request)
    {
        $this->authorize('enroll', $course); // Using same policy as enroll (admin only)
        
        // If a user_id is provided, use that (for admin unenrolling a student)
        // Otherwise, use the authenticated user's ID (this path is now blocked by policy)
        $userId = $request->input('user_id', Auth::id());
        
        $enrollment = $course->enrollments()
            ->where('user_id', $userId)
            ->first();

        if (!$enrollment) {
            return back()->with('error', 'Student is not enrolled in this course.');
        }

        // Delete the enrollment record instead of just marking it as dropped
        $enrollment->delete();

        return back()->with('success', 'Student successfully unenrolled from the course.');
    }
}
