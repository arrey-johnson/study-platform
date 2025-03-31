<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseEnrollment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_id',
        'user_id',
        'enrolled_at',
        'last_accessed_at',
        'progress_percentage',
        'status'
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'last_accessed_at' => 'datetime'
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the student associated with the enrollment.
     * This is an alias for the user relationship.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isDropped(): bool
    {
        return $this->status === 'dropped';
    }

    public function updateProgress(): void
    {
        // First, load the course with its modules and chapters if not already loaded
        if (!$this->course->relationLoaded('modules')) {
            $this->course->load('modules.chapters');
        }
        
        // Get total chapters count
        $totalChapters = 0;
        
        // Count all chapters in the course
        foreach ($this->course->modules as $module) {
            $totalChapters += $module->chapters->count();
        }
        
        if ($totalChapters === 0) {
            $this->update(['progress_percentage' => 100]);
            return;
        }
        
        // Count completed chapters
        $user = $this->user;
        $completedChapters = \DB::table('chapter_completions')
            ->where('user_id', $user->id)
            ->whereNotNull('completed_at')
            ->whereIn('chapter_id', function($query) {
                $query->select('chapters.id')
                    ->from('chapters')
                    ->join('modules', 'chapters.module_id', '=', 'modules.id')
                    ->where('modules.course_id', $this->course_id);
            })
            ->count();
        
        // Calculate percentage
        $progressPercentage = round(($completedChapters / $totalChapters) * 100);
        
        $this->update([
            'progress_percentage' => $progressPercentage,
            'last_accessed_at' => now()
        ]);

        if ($this->progress_percentage >= 100) {
            $this->update(['status' => 'completed']);
        }
    }
}
