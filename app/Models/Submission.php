<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Submission extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'exercise_id',
        'user_id',
        'answers',
        'file_path',
        'score',
        'feedback',
        'graded_by',
        'graded_at',
        'status'
    ];

    protected $casts = [
        'answers' => 'array',
        'score' => 'float',
        'graded_at' => 'datetime'
    ];

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the student who submitted this submission.
     * This is an alias for the user relationship.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    public function isGraded(): bool
    {
        return $this->status === 'graded';
    }

    public function needsRevision(): bool
    {
        return $this->status === 'needs_revision';
    }

    public function grade(int $score, string $feedback, User $grader): bool
    {
        return $this->update([
            'score' => $score,
            'feedback' => $feedback,
            'graded_by' => $grader->id,
            'graded_at' => now(),
            'status' => 'graded'
        ]);
    }

    public function requestRevision(string $feedback): bool
    {
        return $this->update([
            'feedback' => $feedback,
            'status' => 'needs_revision'
        ]);
    }
}
