<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ModuleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!$request->user() || !$request->user()->isAdmin()) {
                abort(403, 'Unauthorized action.');
            }
            return $next($request);
        })->except(['index', 'show']);
    }

    public function index(Course $course)
    {
        $this->authorize('view', $course);

        $modules = $course->modules()->with(['chapters'])->get();

        if (Auth::user()->isStudent()) {
            $modules->each(function ($module) {
                $module->progress = $module->getProgressForUser(Auth::user());
            });
        }

        return Inertia::render('Modules/Index', [
            'course' => $course,
            'modules' => $modules
        ]);
    }

    public function create(Course $course)
    {
        return Inertia::render('Modules/Create', [
            'course' => $course
        ]);
    }

    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer'
        ]);

        $module = $course->modules()->create($validated);

        return redirect()->route('courses.show', $course);
    }

    public function show(Module $module)
    {
        // Instead of using policy, check if user is admin or enrolled in the course
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->enrolledCourses->contains($module->course)) {
            abort(403, 'Unauthorized action.');
        }

        // Load relationships
        $module->load(['course', 'chapters']);
        
        // Calculate progress
        $progress = 0;
        $totalChapters = count($module->chapters);
        
        if ($totalChapters > 0) {
            $completedCount = 0;
            
            foreach ($module->chapters as $chapter) {
                // Check if chapter is completed
                $isCompleted = $chapter->isCompletedByUser($user);
                $chapter->is_completed = $isCompleted;
                
                // Explicitly set the has_pdf attribute
                $chapter->append('has_pdf');
                
                if ($isCompleted) {
                    $completedCount++;
                }
            }
            
            // Calculate percentage
            $progress = $totalChapters > 0 ? round(($completedCount / $totalChapters) * 100) : 0;
        }
        
        // Return the view with data
        return Inertia::render('Modules/Show', [
            'module' => $module,
            'progress' => $progress
        ]);
    }

    public function edit(Module $module)
    {
        return Inertia::render('Modules/Edit', [
            'module' => $module
        ]);
    }

    public function update(Request $request, Module $module)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer'
        ]);

        $module->update($validated);

        return redirect()->route('modules.show', $module);
    }

    public function destroy(Module $module)
    {
        $course = $module->course;
        $module->delete();
        return redirect()->route('courses.show', $course);
    }

    public function reorder(Request $request, Module $module)
    {
        $validated = $request->validate([
            'order' => 'required|integer'
        ]);

        $module->update($validated);

        return back();
    }

    /**
     * Mark all chapters in a module as completed for the current user.
     */
    public function markAsCompleted(Module $module)
    {
        $user = auth()->user();
        
        // Only students can mark modules as completed
        if (!$user->isStudent()) {
            abort(403, 'Only students can mark modules as completed.');
        }
        
        // Get all chapters in the module
        $chapters = $module->chapters;
        
        // Mark each chapter as completed
        foreach ($chapters as $chapter) {
            $completion = $user->completedChapters()
                ->wherePivot('chapter_id', $chapter->id)
                ->first();
                
            if ($completion) {
                // Update existing record if not already completed
                if (!$completion->pivot->completed_at) {
                    $user->completedChapters()->updateExistingPivot($chapter->id, [
                        'completed_at' => now(),
                        'last_accessed_at' => now()
                    ]);
                }
            } else {
                // Create new completion record
                $user->completedChapters()->attach($chapter->id, [
                    'completed_at' => now(),
                    'last_accessed_at' => now(),
                    'access_count' => 1
                ]);
            }
        }
        
        // Update course enrollment progress
        $courseEnrollment = $user->enrolledCourses()
            ->where('course_id', $module->course_id)
            ->first();
            
        if ($courseEnrollment) {
            $courseEnrollment->pivot->updateProgress();
        }
        
        return back()->with('success', 'Module marked as completed.');
    }
}
