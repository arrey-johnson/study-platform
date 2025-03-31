<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Chapter extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'module_id',
        'title',
        'content',
        'order',
        'content_type',
        'content_url',
        'pdf_notes',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function exercises(): HasMany
    {
        return $this->hasMany(Exercise::class)->orderBy('order');
    }
    
    /**
     * Get the users who have completed this chapter.
     */
    public function completedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chapter_completions')
            ->withTimestamps()
            ->withPivot('completed_at', 'time_spent', 'last_accessed_at');
    }
    
    /**
     * Check if the chapter is completed by a specific user.
     */
    public function isCompletedByUser(User $user): bool
    {
        return $this->completedByUsers()
            ->where('user_id', $user->id)
            ->wherePivot('completed_at', '!=', null)
            ->exists();
    }

    public function getProgressForUser(User $user): int
    {
        $totalExercises = $this->exercises()->count();
        if ($totalExercises === 0) return 100;

        $completedExercises = $this->exercises()
            ->whereHas('submissions', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->where('status', 'graded');
            })
            ->count();

        return round(($completedExercises / $totalExercises) * 100);
    }

    /**
     * Determine if the chapter has PDF notes.
     *
     * @return bool
     */
    public function getHasPdfAttribute(): bool
    {
        return !empty($this->pdf_notes);
    }

    /**
     * Get the time spent on this chapter by a specific user.
     * 
     * @param User $user
     * @return int Time in minutes
     */
    public function getTimeSpentByUser(User $user): int
    {
        $completion = $this->completedByUsers()
            ->where('user_id', $user->id)
            ->first();
            
        return $completion ? $completion->pivot->time_spent ?? 0 : 0;
    }

    /**
     * Get the last accessed date for this chapter by a specific user.
     * 
     * @param User $user
     * @return string|null Formatted date
     */
    public function getLastAccessedByUser(User $user): ?string
    {
        $completion = $this->completedByUsers()
            ->where('user_id', $user->id)
            ->first();
            
        return $completion && $completion->pivot->last_accessed_at 
            ? $completion->pivot->last_accessed_at->format('M d, Y') 
            : null;
    }
}
