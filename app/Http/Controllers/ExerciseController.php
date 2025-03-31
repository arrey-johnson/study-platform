<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Course;
use App\Models\Exercise;
use App\Models\Module;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use App\Http\Middleware\AdminMiddleware;

class ExerciseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(AdminMiddleware::class)->except(['index', 'show', 'submit']);
    }

    public function index(Course $course, Module $module, Chapter $chapter)
    {
        $exercises = $chapter->exercises()->with(['chapter.module.course'])->get();
        
        return Inertia::render('Exercises/Index', [
            'course' => $course,
            'module' => $module,
            'chapter' => $chapter,
            'exercises' => $exercises
        ]);
    }

    public function create(Course $course, Module $module, Chapter $chapter)
    {
        return Inertia::render('Exercises/Create', [
            'course' => $course,
            'module' => $module,
            'chapter' => $chapter
        ]);
    }

    public function store(Request $request, Course $course, Module $module, Chapter $chapter)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:multiple_choice,true_false,written,file_upload',
            'options' => 'required_if:type,multiple_choice|array',
            'correct_answers' => 'required_if:type,multiple_choice,true_false|array',
            'points' => 'required|integer|min:0',
            'deadline' => 'nullable|date|after:now',
            'is_active' => 'boolean',
        ]);

        $exercise = new Exercise($validated);
        $exercise->chapter_id = $chapter->id;
        $exercise->save();

        return redirect()->route('courses.modules.chapters.exercises.index', [
            'course' => $course->id,
            'module' => $module->id,
            'chapter' => $chapter->id
        ])->with('success', 'Exercise created successfully.');
    }

    public function show(Course $course, Module $module, Chapter $chapter, Exercise $exercise)
    {
        $exercise->load(['chapter.module.course', 'submissions' => function ($query) {
            $query->where('user_id', Auth::id());
        }]);

        return Inertia::render('Exercises/Show', [
            'course' => $course,
            'module' => $module,
            'chapter' => $chapter,
            'exercise' => $exercise,
            'hasSubmission' => $exercise->submissions->isNotEmpty()
        ]);
    }

    public function edit(Course $course, Module $module, Chapter $chapter, Exercise $exercise)
    {
        return Inertia::render('Exercises/Edit', [
            'course' => $course,
            'module' => $module,
            'chapter' => $chapter,
            'exercise' => $exercise
        ]);
    }

    public function update(Request $request, Course $course, Module $module, Chapter $chapter, Exercise $exercise)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:multiple_choice,true_false,written,file_upload',
            'options' => 'required_if:type,multiple_choice|array',
            'correct_answers' => 'required_if:type,multiple_choice,true_false|array',
            'points' => 'required|integer|min:0',
            'deadline' => 'nullable|date|after:now',
            'is_active' => 'boolean',
        ]);

        $exercise->update($validated);

        return redirect()->route('courses.modules.chapters.exercises.index', [
            'course' => $course->id,
            'module' => $module->id,
            'chapter' => $chapter->id
        ])->with('success', 'Exercise updated successfully.');
    }

    public function destroy(Course $course, Module $module, Chapter $chapter, Exercise $exercise)
    {
        $exercise->delete();

        return redirect()->route('courses.modules.chapters.exercises.index', [
            'course' => $course->id,
            'module' => $module->id,
            'chapter' => $chapter->id
        ])->with('success', 'Exercise deleted successfully.');
    }

    public function submit(Request $request, Course $course, Module $module, Chapter $chapter, Exercise $exercise)
    {
        $validated = $request->validate([
            'answer' => 'required_if:type,multiple_choice,true_false,written',
            'file' => 'required_if:type,file_upload|file|max:10240', // 10MB max
        ]);

        $submission = new Submission();
        $submission->user_id = Auth::id();
        $submission->exercise_id = $exercise->id;

        if ($exercise->type === 'file_upload' && $request->hasFile('file')) {
            $path = $request->file('file')->store('submissions');
            $submission->file_path = $path;
        } else {
            $submission->answer = $validated['answer'];
        }

        // Auto-grade multiple choice and true/false questions
        if (in_array($exercise->type, ['multiple_choice', 'true_false'])) {
            $submission->score = collect($validated['answer'])->diff($exercise->correct_answers)->isEmpty() ? $exercise->points : 0;
        }

        $submission->save();

        return redirect()->back()->with('success', 'Exercise submitted successfully.');
    }
}
