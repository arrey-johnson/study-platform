<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exercise extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'type',
        'options',
        'correct_answers',
        'points',
        'deadline',
        'is_active',
        'chapter_id'
    ];

    protected $casts = [
        'options' => 'array',
        'correct_answers' => 'array',
        'deadline' => 'datetime',
        'is_active' => 'boolean'
    ];

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function getSubmissionForUser(User $user): ?Submission
    {
        return $this->submissions()->where('user_id', $user->id)->first();
    }

    public function calculateScore($answers): float
    {
        if ($this->type === 'multiple_choice') {
            $correctCount = count(array_intersect($answers, $this->correct_answers));
            return ($correctCount / count($this->correct_answers)) * $this->points;
        }

        if ($this->type === 'true_false') {
            return $answers === $this->correct_answers ? $this->points : 0;
        }

        // For written and file_upload exercises, scoring is done manually by instructors
        return 0;
    }

    public function isSubmittedBy(User $user): bool
    {
        return $this->submissions()->where('user_id', $user->id)->exists();
    }

    public function isGradedForUser(User $user): bool
    {
        return $this->submissions()
            ->where('user_id', $user->id)
            ->where('status', 'graded')
            ->exists();
    }

    public function isOverdue(): bool
    {
        return $this->deadline && $this->deadline->isPast();
    }

    public function isAutoGraded(): bool
    {
        return in_array($this->type, ['multiple_choice', 'true_false']);
    }
}
