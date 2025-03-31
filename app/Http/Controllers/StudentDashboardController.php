<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Chapter;
use App\Models\ChapterCompletion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if (!$user->isStudent()) {
            return redirect()->route('dashboard');
        }
        
        // Get student reading progress data
        $completedReadings = ChapterCompletion::where('user_id', $user->id)
            ->whereNotNull('completed_at')
            ->count();
        
        // Calculate average reading time (in minutes)
        $averageReadingTime = ChapterCompletion::where('user_id', $user->id)
            ->whereNotNull('completed_at')
            ->where('time_spent', '>', 0) // Only include valid time entries
            ->avg('time_spent') ?? 0;
        $averageReadingTime = round($averageReadingTime / 60); // Convert seconds to minutes
        
        // Calculate reading streak based on chapter completion history
        $streak = $this->calculateReadingStreak($user);
        
        // Get enrolled courses with progress
        $enrolledCourses = $user->enrolledCourses()->with(['modules.chapters'])->get();
        $courses = [];
        
        foreach ($enrolledCourses as $course) {
            // Calculate course progress based on PDF readings
            $totalChaptersWithPdf = 0;
            $completedChaptersWithPdf = 0;
            
            foreach ($course->modules as $module) {
                foreach ($module->chapters as $chapter) {
                    // Only count chapters with PDF notes
                    if (!empty($chapter->pdf_notes)) {
                        $totalChaptersWithPdf++;
                        
                        // Check if this chapter with PDF is completed by the user
                        $isCompleted = $user->completedChapters()
                            ->wherePivot('chapter_id', $chapter->id)
                            ->wherePivot('completed_at', '!=', null)
                            ->exists();
                            
                        if ($isCompleted) {
                            $completedChaptersWithPdf++;
                        }
                    }
                }
            }
            
            $progress = $totalChaptersWithPdf > 0 ? round(($completedChaptersWithPdf / $totalChaptersWithPdf) * 100) : 0;
            
            $courses[] = [
                'id' => $course->id,
                'title' => $course->title,
                'description' => $course->description,
                'progress' => $progress,
                'total_readings' => $totalChaptersWithPdf,
                'completed_readings' => $completedChaptersWithPdf,
                'image' => $course->image,
                'created_at' => $course->created_at,
            ];
        }
        
        // Get upcoming reading materials
        $upcomingReadings = Chapter::whereHas('module.course.enrollments', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->whereNotNull('pdf_notes')
        ->whereDoesntHave('completedByUsers', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->with(['module.course'])
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get()
        ->map(function($chapter) {
            return [
                'id' => $chapter->id,
                'course_id' => $chapter->module->course->id,
                'module_id' => $chapter->module->id,
                'chapter_id' => $chapter->id,
                'title' => $chapter->title,
                'module_title' => $chapter->module->title,
                'course_title' => $chapter->module->course->title,
            ];
        });
        
        // For now, let's use the Dashboard view instead of StudentDashboard
        return Inertia::render('Dashboard', [
            'studentProgress' => [
                'completedReadings' => $completedReadings,
                'averageReadingTime' => $averageReadingTime,
                'streak' => $streak,
            ],
            'courses' => $courses,
            'upcomingDeadlines' => $upcomingReadings
        ]);
    }
    
    /**
     * Calculate the reading streak based on chapter completion history
     */
    private function calculateReadingStreak(User $user) {
        // Get all chapter completions ordered by date
        $completions = ChapterCompletion::where('user_id', $user->id)
            ->whereNotNull('completed_at')
            ->orderBy('completed_at', 'desc')
            ->get();
            
        // If no completions, streak is 0
        if ($completions->isEmpty()) {
            return 0;
        }
        
        $streak = 0;
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        
        // Check if user has read anything today
        $hasReadToday = $completions->contains(function ($completion) use ($today) {
            return $completion->completed_at->startOfDay()->equalTo($today);
        });
        
        // If no reading today, check if there was one yesterday to maintain streak
        if (!$hasReadToday) {
            $hasReadYesterday = $completions->contains(function ($completion) use ($yesterday) {
                return $completion->completed_at->startOfDay()->equalTo($yesterday);
            });
            
            // If no reading yesterday either, streak is broken
            if (!$hasReadYesterday) {
                return 0;
            }
        }
        
        // Group completions by day
        $completionsByDay = $completions->groupBy(function ($completion) {
            return $completion->completed_at->format('Y-m-d');
        });
        
        // Sort days in descending order
        $days = $completionsByDay->keys()->sort(function ($a, $b) {
            return Carbon::parse($b)->compare(Carbon::parse($a));
        });
        
        // Calculate streak
        $currentDate = $hasReadToday ? $today : $yesterday;
        $streak = 1; // Start with 1 for today/yesterday
        
        for ($i = 1; $i < $days->count(); $i++) {
            $previousDate = Carbon::parse($days[$i]);
            
            // If the previous date is exactly one day before the current date, increment streak
            if ($currentDate->subDay()->startOfDay()->equalTo($previousDate->startOfDay())) {
                $streak++;
                $currentDate = $previousDate;
            } else {
                break; // Streak is broken
            }
        }
        
        return $streak;
    }
}
