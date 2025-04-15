<?php
namespace App\Controllers;

class StudentController extends Controller
{
    /**
     * Show student dashboard
     */
    public function dashboard()
    {
        $this->requireStudent();

        // Get student's enrolled courses
        $courses = $this->db->fetchAll(
            "SELECT c.*, 
                    (SELECT COUNT(*) FROM exercises WHERE course_id = c.id) as total_exercises,
                    (SELECT COUNT(*) FROM exercise_submissions 
                     WHERE exercise_id IN (SELECT id FROM exercises WHERE course_id = c.id) 
                     AND student_id = ? AND status = 'completed') as completed_exercises
             FROM courses c
             INNER JOIN course_enrollments ce ON c.id = ce.course_id
             WHERE ce.student_id = ?",
            [$_SESSION['user_id'], $_SESSION['user_id']]
        );

        // Calculate progress for each course
        foreach ($courses as &$course) {
            $course['progress'] = $course['total_exercises'] > 0 
                ? round(($course['completed_exercises'] / $course['total_exercises']) * 100) 
                : 0;
        }

        // Get upcoming deadlines
        $deadlines = $this->db->fetchAll(
            "SELECT e.*, c.title as course_title
             FROM exercises e
             INNER JOIN courses c ON e.course_id = c.id
             INNER JOIN course_enrollments ce ON c.id = ce.course_id
             WHERE ce.student_id = ? 
             AND e.due_date > NOW()
             AND NOT EXISTS (
                 SELECT 1 FROM exercise_submissions 
                 WHERE exercise_id = e.id 
                 AND student_id = ?
             )
             ORDER BY e.due_date ASC
             LIMIT 5",
            [$_SESSION['user_id'], $_SESSION['user_id']]
        );

        // Get recent activities
        $activities = $this->db->fetchAll(
            "SELECT es.*, e.title as exercise_title, c.title as course_title
             FROM exercise_submissions es
             INNER JOIN exercises e ON es.exercise_id = e.id
             INNER JOIN courses c ON e.course_id = c.id
             WHERE es.student_id = ?
             ORDER BY es.submitted_at DESC
             LIMIT 5",
            [$_SESSION['user_id']]
        );

        $data = [
            'title' => 'Student Dashboard - ' . APP_NAME,
            'courses' => $courses,
            'deadlines' => $deadlines,
            'activities' => $activities
        ];
        
        $this->render('student/dashboard', $data);
    }
} 