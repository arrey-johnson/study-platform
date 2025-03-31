<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class ChapterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!$request->user() || !$request->user()->isAdmin()) {
                abort(403, 'Unauthorized action.');
            }
            return $next($request);
        })->except(['index', 'show', 'viewPdf', 'markAsCompleted', 'markAsIncomplete']);
    }

    public function index(Module $module)
    {
        $chapters = $module->chapters()->with(['exercises'])->get();
        
        return Inertia::render('Chapters/Index', [
            'module' => $module,
            'chapters' => $chapters
        ]);
    }

    public function create(Module $module)
    {
        // Ensure module is loaded with its chapters
        $module->load(['chapters']);
        
        return Inertia::render('Chapters/Create', [
            'module' => $module
        ]);
    }

    public function store(Request $request, Module $module)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'order' => 'nullable|integer',
            'pdf_notes' => 'nullable|file|mimes:pdf|max:10240', // Max 10MB PDF file
            'is_active' => 'boolean'
        ]);

        // Handle PDF upload if provided
        if ($request->hasFile('pdf_notes')) {
            $path = $request->file('pdf_notes')->store('chapter-notes', 'public');
            $validated['pdf_notes'] = $path;
        }

        $chapter = $module->chapters()->create($validated);

        return redirect()->route('modules.show', $module);
    }

    public function show(Chapter $chapter)
    {
        $user = auth()->user();
        
        // Check if the chapter is completed by the user
        $isCompleted = $chapter->isCompletedByUser($user);
        
        // Track chapter access if user is a student
        if (!$user->isAdmin()) {
            // Get or create chapter completion record
            $completion = $user->completedChapters()
                ->wherePivot('chapter_id', $chapter->id)
                ->first();
                
            if ($completion) {
                // Update existing record
                $user->completedChapters()->updateExistingPivot($chapter->id, [
                    'last_accessed_at' => now(),
                    'access_count' => \DB::raw('access_count + 1')
                ]);
            } else {
                // Create new record
                $user->completedChapters()->attach($chapter->id, [
                    'last_accessed_at' => now(),
                    'access_count' => 1
                ]);
            }
        }
        
        // Calculate the course progress
        $courseProgress = $user->calculateCourseProgress($chapter->module->course->id);
        
        return Inertia::render('Chapters/Show', [
            'chapter' => $chapter->load(['module.course']),
            'isCompleted' => $isCompleted,
            'courseProgress' => $courseProgress
        ]);
    }

    public function edit(Chapter $chapter)
    {
        return Inertia::render('Chapters/Edit', [
            'chapter' => $chapter
        ]);
    }

    public function update(Request $request, Chapter $chapter)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'order' => 'nullable|integer',
            'pdf_notes' => 'nullable|file|mimes:pdf|max:10240', // Max 10MB PDF file
            'is_active' => 'boolean'
        ]);

        // Handle PDF upload if provided
        if ($request->hasFile('pdf_notes')) {
            // Delete old PDF if exists
            if ($chapter->pdf_notes) {
                Storage::disk('public')->delete($chapter->pdf_notes);
            }
            
            $path = $request->file('pdf_notes')->store('chapter-notes', 'public');
            $validated['pdf_notes'] = $path;
        }

        $chapter->update($validated);

        return redirect()->route('chapters.show', $chapter);
    }

    public function destroy(Chapter $chapter)
    {
        $module = $chapter->module;
        $chapter->delete();
        return redirect()->route('modules.show', $module);
    }

    public function reorder(Request $request, Chapter $chapter)
    {
        $validated = $request->validate([
            'order' => 'required|integer'
        ]);

        $chapter->update($validated);

        return back();
    }

    public function viewPdf(Chapter $chapter)
    {
        // Check if the user is authorized to view this chapter
        $user = auth()->user();
        
        // If user is not admin, check if they're enrolled in the course
        if (!$user->isAdmin()) {
            $course = $chapter->module->course;
            $isEnrolled = $user->enrolledCourses()->where('course_id', $course->id)->exists();
            
            if (!$isEnrolled) {
                abort(403, 'You are not enrolled in this course.');
            }
            
            // Track PDF access
            $completion = $user->completedChapters()
                ->wherePivot('chapter_id', $chapter->id)
                ->first();
                
            if ($completion) {
                // Update existing record
                $user->completedChapters()->updateExistingPivot($chapter->id, [
                    'last_accessed_at' => now(),
                    'access_count' => \DB::raw('access_count + 1')
                ]);
            } else {
                // Create new record
                $user->completedChapters()->attach($chapter->id, [
                    'last_accessed_at' => now(),
                    'access_count' => 1
                ]);
            }
        }
        
        // Check if PDF exists
        if (!$chapter->pdf_notes || !Storage::disk('public')->exists($chapter->pdf_notes)) {
            abort(404, 'PDF not found');
        }
        
        // Create a signed URL with an expiration time (5 minutes)
        $pdfUrl = url('/storage/' . $chapter->pdf_notes);
        
        // Return the PDF viewer blade template
        return view('pdf-viewer', ['pdfUrl' => $pdfUrl]);
    }

    public function markAsCompleted(Chapter $chapter)
    {
        $user = auth()->user();
        
        // Get time spent data from request
        $timeSpent = request('time_spent', 0);
        $comprehensionRating = request('comprehension_rating');
        $notes = request('notes');
        
        // Add the chapter to the user's completed chapters with learning stats
        $user->completedChapters()->syncWithoutDetaching([
            $chapter->id => [
                'completed_at' => now(),
                'time_spent' => $timeSpent,
                'comprehension_rating' => $comprehensionRating,
                'notes' => $notes,
                'last_accessed_at' => now(),
                'access_count' => \DB::raw('COALESCE(access_count, 0) + 1')
            ]
        ]);
        
        return back()->with('success', 'Chapter marked as completed!');
    }
    
    public function markAsIncomplete(Chapter $chapter)
    {
        $user = auth()->user();
        
        // Remove the chapter from the user's completed chapters
        $user->completedChapters()->detach($chapter->id);
        
        return back();
    }
    
    /**
     * Update PDF reading progress for a chapter
     *
     * @param Request $request
     * @param Chapter $chapter
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePdfProgress(Request $request, Chapter $chapter)
    {
        $validated = $request->validate([
            'current_page' => 'required|integer|min:1',
            'total_pages' => 'required|integer|min:1',
            'viewed_pages' => 'required|array',
            'viewed_pages.*' => 'integer|min:1',
        ]);
        
        $user = auth()->user();
        
        // If user is not admin, check if they're enrolled in the course
        if (!$user->isAdmin()) {
            $course = $chapter->module->course;
            $isEnrolled = $user->enrolledCourses()->where('course_id', $course->id)->exists();
            
            if (!$isEnrolled) {
                return response()->json(['error' => 'You are not enrolled in this course.'], 403);
            }
        }
        
        // Calculate progress percentage based on unique pages viewed
        $uniqueViewedPages = array_unique($validated['viewed_pages']);
        $totalUniqueViewed = count($uniqueViewedPages);
        $totalPages = $validated['total_pages'];
        $progressPercentage = min(100, round(($totalUniqueViewed / $totalPages) * 100));
        
        // Update the chapter completion record with reading progress
        $completion = $user->completedChapters()
            ->wherePivot('chapter_id', $chapter->id)
            ->first();
            
        if ($completion) {
            // Get existing viewed pages if any
            $existingData = json_decode($completion->pivot->reading_progress_data ?? '{}', true);
            $existingViewedPages = $existingData['viewed_pages'] ?? [];
            
            // Merge with new viewed pages
            $allViewedPages = array_unique(array_merge($existingViewedPages, $uniqueViewedPages));
            
            // Calculate new progress
            $newProgressPercentage = min(100, round((count($allViewedPages) / $totalPages) * 100));
            
            // Update existing record
            $user->completedChapters()->updateExistingPivot($chapter->id, [
                'last_accessed_at' => now(),
                'reading_progress' => $newProgressPercentage,
                'reading_progress_data' => json_encode([
                    'viewed_pages' => $allViewedPages,
                    'total_pages' => $totalPages,
                    'last_page' => $validated['current_page']
                ])
            ]);
            
            $progressPercentage = $newProgressPercentage;
        } else {
            // Create new record
            $user->completedChapters()->attach($chapter->id, [
                'last_accessed_at' => now(),
                'access_count' => 1,
                'reading_progress' => $progressPercentage,
                'reading_progress_data' => json_encode([
                    'viewed_pages' => $uniqueViewedPages,
                    'total_pages' => $totalPages,
                    'last_page' => $validated['current_page']
                ])
            ]);
        }
        
        // Calculate the course progress
        $courseProgress = $user->calculateCourseProgress($chapter->module->course->id);
        
        return response()->json([
            'success' => true,
            'reading_progress' => $progressPercentage,
            'course_progress' => $courseProgress
        ]);
    }
}
