<?php

namespace App\Models;

use App\Models\Exercise;
use App\Models\Category;
use App\Models\Module;
use App\Models\Chapter;
use App\Models\CourseEnrollment;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'created_by',
        'is_active',
        'category_id'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function modules(): HasMany
    {
        return $this->hasMany(Module::class)->orderBy('order');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    /**
     * Get the completed enrollments for the course.
     */
    public function completions(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class)->where('status', 'completed');
    }

    public function enrolledStudents(): HasMany
    {
        return $this->hasMany(User::class, 'course_enrollments')
            ->join('course_enrollments', 'users.id', '=', 'course_enrollments.user_id')
            ->where('course_enrollments.course_id', $this->id);
    }

    /**
     * Get all exercises associated with this course through its modules and chapters
     */
    public function exercises()
    {
        // We need to use a custom query here since the relationship is through modules->chapters->exercises
        return Exercise::whereHas('chapter.module', function($query) {
            $query->where('modules.course_id', $this->id);
        });
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function getProgressForUser(User $user): int
    {
        $enrollment = $this->enrollments()->where('user_id', $user->id)->first();
        return $enrollment ? $enrollment->progress_percentage : 0;
    }

    public function isEnrolledBy(User $user): bool
    {
        return $this->enrollments()->where('user_id', $user->id)->exists();
    }
}
