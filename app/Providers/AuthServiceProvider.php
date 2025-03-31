<?php

namespace App\Providers;

use App\Models\Exercise;
use App\Models\Course;
use App\Models\Category;
use App\Models\CourseEnrollment;
use App\Models\ChapterCompletion;
use App\Policies\ExercisePolicy;
use App\Policies\CoursePolicy;
use App\Policies\CategoryPolicy;
use App\Policies\CourseEnrollmentPolicy;
use App\Policies\ChapterCompletionPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Exercise::class => ExercisePolicy::class,
        Course::class => CoursePolicy::class,
        Category::class => CategoryPolicy::class,
        CourseEnrollment::class => CourseEnrollmentPolicy::class,
        ChapterCompletion::class => ChapterCompletionPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}