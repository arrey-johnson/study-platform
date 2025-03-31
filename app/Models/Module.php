<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class)->orderBy('order');
    }

    public function getProgressForUser(User $user): int
    {
        $totalChapters = $this->chapters()->count();
        if ($totalChapters === 0) return 100;

        // Count chapters that have been marked as completed
        $completedChapters = $this->chapters()
            ->whereHas('progress', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->where('completed', true);
            })
            ->count();

        return round(($completedChapters / $totalChapters) * 100);
    }
}
