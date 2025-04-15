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
                            <a class="nav-link active" href="<?= APP_URL ?>/student/dashboard">
                                <i class="fas fa-home me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= APP_URL ?>/student/courses">
                                <i class="fas fa-book me-2"></i>My Courses
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
                    <h2 class="card-title">Welcome to Your Dashboard</h2>
                    <p class="card-text">Track your progress and manage your courses here.</p>
                </div>
            </div>

            <!-- Enrolled Courses -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title mb-0">Enrolled Courses</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($courses)): ?>
                        <p class="text-muted">You are not enrolled in any courses yet.</p>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($courses as $course): ?>
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        <img src="<?= asset('images/courses/' . $course['image']) ?>" class="card-img-top" alt="<?= e($course['title']) ?>">
                                        <div class="card-body">
                                            <h5 class="card-title"><?= e($course['title']) ?></h5>
                                            <p class="card-text"><?= str_truncate(e($course['description']), 100) ?></p>
                                            <div class="progress mb-2">
                                                <div class="progress-bar" role="progressbar" style="width: <?= $course['progress'] ?>%">
                                                    <?= $course['progress'] ?>%
                                                </div>
                                            </div>
                                            <small class="text-muted">
                                                <?= $course['completed_exercises'] ?> of <?= $course['total_exercises'] ?> exercises completed
                                            </small>
                                            <a href="<?= APP_URL ?>/student/course/<?= $course['id'] ?>" class="btn btn-primary btn-sm mt-2">Continue Learning</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Upcoming Deadlines -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title mb-0">Upcoming Deadlines</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($deadlines)): ?>
                        <p class="text-muted">No upcoming deadlines.</p>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($deadlines as $deadline): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1"><?= e($deadline['title']) ?></h6>
                                            <small class="text-muted"><?= e($deadline['course_title']) ?></small>
                                        </div>
                                        <div class="text-end">
                                            <small class="text-muted">Due: <?= format_date($deadline['due_date'], 'M d, Y') ?></small>
                                            <a href="<?= APP_URL ?>/student/exercise/<?= $deadline['id'] ?>" class="btn btn-primary btn-sm ms-2">View</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">Recent Activities</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($activities)): ?>
                        <p class="text-muted">No recent activities.</p>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($activities as $activity): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1"><?= e($activity['exercise_title']) ?></h6>
                                            <small class="text-muted"><?= e($activity['course_title']) ?></small>
                                        </div>
                                        <small class="text-muted"><?= format_date($activity['submitted_at'], 'M d, Y') ?></small>
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