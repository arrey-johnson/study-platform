<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'created_by');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    /**
     * Get the chapters that have been marked as completed by the user.
     */
    public function completedChapters()
    {
        return $this->belongsToMany(Chapter::class, 'chapter_completions', 'user_id', 'chapter_id')
            ->withTimestamps();
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function isAdmin(): bool
    {
        if (!$this->role) {
            return false;
        }
        
        return $this->role->name === 'admin';
    }

    public function isStudent(): bool
    {
        if (!$this->role) {
            return false;
        }
        
        return $this->role->name === 'student';
    }

    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'course_enrollments')
            ->withTimestamps()
            ->withPivot('status');
    }
    
    public function chapterProgress()
    {
        return $this->hasMany(ChapterProgress::class);
    }
    
    public function calculateCourseProgress($courseId)
    {
        $course = Course::findOrFail($courseId);
        
        // Get all chapter IDs in this course
        $chapterIds = [];
        foreach ($course->modules as $module) {
            foreach ($module->chapters as $chapter) {
                $chapterIds[] = $chapter->id;
            }
        }
        
        if (empty($chapterIds)) {
            return 0; // No chapters in this course
        }
        
        // Count completed chapters
        $completedCount = $this->completedChapters()
            ->whereIn('chapters.id', $chapterIds)
            ->count();
            
        // Calculate percentage
        return round(($completedCount / count($chapterIds)) * 100);
    }
}
