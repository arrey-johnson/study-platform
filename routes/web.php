<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\SecurePdfController; 
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\ChapterCompletionController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Http\Middleware\AdminMiddleware;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Enrollments routes
    Route::get('/enrollments', [EnrollmentController::class, 'index'])->name('enrollments.index');
    
    // Chapter completions routes
    Route::get('/chapter-completions', [ChapterCompletionController::class, 'index'])->name('chapter-completions.index');
    
    // Course routes
    Route::resource('courses', CourseController::class);
    Route::post('/courses/{course}/enroll', [CourseController::class, 'enroll'])->name('courses.enroll');
    Route::post('/courses/{course}/unenroll', [CourseController::class, 'unenroll'])->name('courses.unenroll');

    // Category routes
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');

    // Module routes
    Route::resource('courses.modules', ModuleController::class)->shallow();
    Route::post('/modules/{module}/reorder', [ModuleController::class, 'reorder'])->name('modules.reorder');
    Route::post('/modules/{module}/mark-completed', [ModuleController::class, 'markAsCompleted'])->name('modules.mark-completed');

    // Chapter routes
    Route::resource('modules.chapters', ChapterController::class)->shallow();
    Route::post('/chapters/{chapter}/reorder', [ChapterController::class, 'reorder'])->name('chapters.reorder');
    
    // Chapter progress routes
    Route::post('/chapters/{chapter}/complete', [ChapterController::class, 'markAsCompleted'])->name('chapters.complete');
    Route::post('/chapters/{chapter}/incomplete', [ChapterController::class, 'markAsIncomplete'])->name('chapters.incomplete');
    
    // Chapter PDF viewer route
    Route::get('/chapters/{chapter}/pdf', [ChapterController::class, 'viewPdf'])->name('chapters.pdf');
    
    // Chapter PDF reading progress tracking
    Route::post('/chapters/{chapter}/pdf-progress', [ChapterController::class, 'updatePdfProgress'])->name('chapters.pdf.progress');

    // Secure PDF viewer route
    Route::get('/secure-pdf-view', [SecurePdfController::class, 'view'])->name('secure.pdf.view');

    // Exercise routes within chapters
    Route::get('/courses/{course}/modules/{module}/chapters/{chapter}/exercises', [ExerciseController::class, 'index'])
        ->name('courses.modules.chapters.exercises.index');
    Route::get('/courses/{course}/modules/{module}/chapters/{chapter}/exercises/create', [ExerciseController::class, 'create'])
        ->middleware(AdminMiddleware::class)
        ->name('courses.modules.chapters.exercises.create');
    Route::post('/courses/{course}/modules/{module}/chapters/{chapter}/exercises', [ExerciseController::class, 'store'])
        ->middleware(AdminMiddleware::class)
        ->name('courses.modules.chapters.exercises.store');
    Route::get('/courses/{course}/modules/{module}/chapters/{chapter}/exercises/{exercise}', [ExerciseController::class, 'show'])
        ->name('courses.modules.chapters.exercises.show');
    Route::get('/courses/{course}/modules/{module}/chapters/{chapter}/exercises/{exercise}/edit', [ExerciseController::class, 'edit'])
        ->middleware(AdminMiddleware::class)
        ->name('courses.modules.chapters.exercises.edit');
    Route::put('/courses/{course}/modules/{module}/chapters/{chapter}/exercises/{exercise}', [ExerciseController::class, 'update'])
        ->middleware(AdminMiddleware::class)
        ->name('courses.modules.chapters.exercises.update');
    Route::delete('/courses/{course}/modules/{module}/chapters/{chapter}/exercises/{exercise}', [ExerciseController::class, 'destroy'])
        ->middleware(AdminMiddleware::class)
        ->name('courses.modules.chapters.exercises.destroy');
    Route::post('/courses/{course}/modules/{module}/chapters/{chapter}/exercises/{exercise}/submit', [ExerciseController::class, 'submit'])
        ->name('courses.modules.chapters.exercises.submit');
    
    // Submission routes
    Route::get('/submissions/{submission}', [SubmissionController::class, 'show'])->name('submissions.show');
    Route::post('/submissions/{submission}', [SubmissionController::class, 'update'])->name('submissions.update');
    Route::get('/exercises/{exercise}/submissions', [SubmissionController::class, 'index'])->name('exercises.submissions.index');
    
    // Student management routes
    Route::resource('students', StudentController::class);
    Route::post('/students/{student}/enroll', [StudentController::class, 'enroll'])->name('students.enroll');
    Route::delete('/students/{student}/unenroll', [StudentController::class, 'unenroll'])->name('students.unenroll');
    
    // Admin routes
    Route::middleware([AdminMiddleware::class])->group(function () {
        // Standalone exercise management
        Route::get('/exercises', [ExerciseController::class, 'index'])->name('exercises.index');
        Route::get('/exercises/create', [ExerciseController::class, 'create'])->name('exercises.create');
        Route::post('/exercises', [ExerciseController::class, 'store'])->name('exercises.store');
        Route::get('/exercises/{exercise}', [ExerciseController::class, 'show'])->name('exercises.show');
        Route::get('/exercises/{exercise}/edit', [ExerciseController::class, 'edit'])->name('exercises.edit');
        Route::put('/exercises/{exercise}', [ExerciseController::class, 'update'])->name('exercises.update');
        Route::delete('/exercises/{exercise}', [ExerciseController::class, 'destroy'])->name('exercises.destroy');
        
        // User management
        Route::resource('users', UserController::class);
        
        // Reports
        Route::get('/reports/course-progress', [ReportController::class, 'courseProgress'])->name('reports.course-progress');
        Route::get('/reports/student-performance', [ReportController::class, 'studentPerformance'])->name('reports.student-performance');
        
        // Admin-only enrollment management
        Route::get('/enrollments/create', [EnrollmentController::class, 'create'])->name('enrollments.create');
        Route::post('/enrollments', [EnrollmentController::class, 'store'])->name('enrollments.store');
        Route::delete('/enrollments/{enrollment}', [EnrollmentController::class, 'destroy'])->name('enrollments.destroy');
        Route::get('/enrollments/batch', [EnrollmentController::class, 'batchEnrollForm'])->name('enrollments.batch.form');
        Route::post('/enrollments/batch', [EnrollmentController::class, 'batchEnroll'])->name('enrollments.batch');
    });
});

Route::get('/student-dashboard', [StudentDashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('student.dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
