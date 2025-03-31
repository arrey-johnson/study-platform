<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\Exercise;

class SubmissionController extends Controller
{
    /**
     * Display the specified submission.
     *
     * @param  \App\Models\Submission  $submission
     * @return \Inertia\Response
     */
    public function show(Submission $submission)
    {
        // Check if user is admin or the student who submitted
        $user = Auth::user();
        if (!$user->isAdmin() && $submission->student_id !== $user->id) {
            abort(403, 'You do not have permission to view this submission');
        }

        // Load the submission with related data
        $submission->load(['student', 'exercise', 'exercise.chapter', 'exercise.chapter.module', 'exercise.chapter.module.course']);
        
        return Inertia::render('Submissions/Show', [
            'submission' => $submission,
            'exercise' => $submission->exercise,
            'chapter' => $submission->exercise->chapter,
            'module' => $submission->exercise->chapter->module,
            'course' => $submission->exercise->chapter->module->course,
        ]);
    }

    /**
     * Update the specified submission (for admin grading).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Submission  $submission
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Submission $submission)
    {
        // Only admins can grade submissions
        if (!Auth::user()->isAdmin()) {
            abort(403, 'You do not have permission to grade submissions');
        }

        $validated = $request->validate([
            'score' => 'required|numeric|min:0|max:' . $submission->exercise->points,
            'feedback' => 'nullable|string|max:1000',
        ]);

        $submission->update([
            'score' => $validated['score'],
            'feedback' => $validated['feedback'],
            'graded_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Submission graded successfully');
    }

    /**
     * List all submissions for an exercise (admin only).
     *
     * @param  \App\Models\Exercise  $exercise
     * @return \Inertia\Response
     */
    public function index(Exercise $exercise)
    {
        // Only admins can view all submissions
        if (!Auth::user()->isAdmin()) {
            abort(403, 'You do not have permission to view all submissions');
        }

        $exercise->load(['chapter', 'chapter.module', 'chapter.module.course']);
        
        $submissions = $exercise->submissions()
            ->with('student')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Submissions/Index', [
            'exercise' => $exercise,
            'submissions' => $submissions,
            'chapter' => $exercise->chapter,
            'module' => $exercise->chapter->module,
            'course' => $exercise->chapter->module->course,
        ]);
    }
}
