<?php

namespace App\Http\Controllers;

use App\Models\ChapterCompletion;
use App\Models\Chapter;
use App\Models\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ChapterCompletionController extends Controller
{
    /**
     * Display a listing of the chapter completions.
     *
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', ChapterCompletion::class);
        
        $completions = DB::table('chapter_completions')
            ->join('users', 'chapter_completions.user_id', '=', 'users.id')
            ->join('chapters', 'chapter_completions.chapter_id', '=', 'chapters.id')
            ->join('modules', 'chapters.module_id', '=', 'modules.id')
            ->join('courses', 'modules.course_id', '=', 'courses.id')
            ->select(
                'chapter_completions.id',
                'chapter_completions.completed_at',
                'chapter_completions.time_spent',
                'chapter_completions.comprehension_rating',
                'chapter_completions.notes',
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
            ->paginate(15)
            ->through(function ($completion) {
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
                    'comprehension_rating' => $completion->comprehension_rating ?? 0,
                    'notes' => $completion->notes
                ];
            });
            
        // Get completion statistics
        $totalCompletions = DB::table('chapter_completions')->count();
        $avgComprehension = DB::table('chapter_completions')
            ->whereNotNull('comprehension_rating')
            ->avg('comprehension_rating') ?? 0;
        $avgTimeSpent = DB::table('chapter_completions')
            ->whereNotNull('time_spent')
            ->avg('time_spent') ?? 0;
            
        // Get courses with most completions
        $courseCompletions = DB::table('chapter_completions')
            ->join('chapters', 'chapter_completions.chapter_id', '=', 'chapters.id')
            ->join('modules', 'chapters.module_id', '=', 'modules.id')
            ->join('courses', 'modules.course_id', '=', 'courses.id')
            ->select('courses.id', 'courses.title', DB::raw('count(*) as completion_count'))
            ->groupBy('courses.id', 'courses.title')
            ->orderBy('completion_count', 'desc')
            ->take(5)
            ->get();
            
        return Inertia::render('ChapterCompletions/Index', [
            'completions' => $completions,
            'stats' => [
                'totalCompletions' => $totalCompletions,
                'avgComprehension' => round($avgComprehension, 1),
                'avgTimeSpent' => round($avgTimeSpent)
            ],
            'courseCompletions' => $courseCompletions
        ]);
    }
}
