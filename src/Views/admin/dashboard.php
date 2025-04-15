<div class="container py-4">
    <div class="row">
        <div class="col-md-3">
            <!-- Sidebar -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <img src="<?= asset('images/default-avatar.png') ?>" alt="Profile" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                        <h5 class="mt-2"><?= e($_SESSION['name']) ?></h5>
                        <p class="text-muted"><?= e($_SESSION['email']) ?></p>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="<?= APP_URL ?>/admin/dashboard">
                                <i class="fas fa-home me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= APP_URL ?>/admin/courses">
                                <i class="fas fa-book me-2"></i>Courses
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= APP_URL ?>/admin/students">
                                <i class="fas fa-users me-2"></i>Students
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= APP_URL ?>/admin/exercises">
                                <i class="fas fa-tasks me-2"></i>Exercises
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= APP_URL ?>/auth/logout">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <!-- Main Content -->
            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="card-title">Admin Dashboard</h2>
                    <p class="card-text">Manage your learning platform from here.</p>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Students</h5>
                            <h2 class="card-text"><?= $totalStudents ?></h2>
                            <i class="fas fa-users fa-2x position-absolute bottom-0 end-0 mb-3 me-3 opacity-50"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Courses</h5>
                            <h2 class="card-text"><?= $totalCourses ?></h2>
                            <i class="fas fa-book fa-2x position-absolute bottom-0 end-0 mb-3 me-3 opacity-50"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Exercises</h5>
                            <h2 class="card-text"><?= $totalExercises ?></h2>
                            <i class="fas fa-tasks fa-2x position-absolute bottom-0 end-0 mb-3 me-3 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Enrollments -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title mb-0">Recent Enrollments</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($recentEnrollments)): ?>
                        <p class="text-muted">No recent enrollments.</p>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($recentEnrollments as $enrollment): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1"><?= e($enrollment['student_name']) ?></h6>
                                            <small class="text-muted">Enrolled in <?= e($enrollment['course_title']) ?></small>
                                        </div>
                                        <small class="text-muted"><?= format_date($enrollment['enrolled_at'], 'M d, Y') ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Submissions -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">Recent Exercise Submissions</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($recentSubmissions)): ?>
                        <p class="text-muted">No recent submissions.</p>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($recentSubmissions as $submission): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1"><?= e($submission['student_name']) ?></h6>
                                            <small class="text-muted">
                                                Submitted <?= e($submission['exercise_title']) ?> in <?= e($submission['course_title']) ?>
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-<?= $submission['status'] === 'completed' ? 'success' : 'warning' ?>">
                                                <?= ucfirst($submission['status']) ?>
                                            </span>
                                            <small class="text-muted d-block"><?= format_date($submission['submitted_at'], 'M d, Y') ?></small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div> 