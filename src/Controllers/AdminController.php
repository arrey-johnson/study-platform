<?php
namespace App\Controllers;

class AdminController extends Controller
{
    /**
     * Show admin dashboard
     */
    public function dashboard()
    {
        $this->requireAdmin();

        // Get total students
        $totalStudents = $this->db->fetch(
            "SELECT COUNT(*) as count FROM users WHERE role = 'student'"
        )['count'];

        // Get total courses
        $totalCourses = $this->db->fetch(
            "SELECT COUNT(*) as count FROM courses"
        )['count'];

        // Get total exercises
        $totalExercises = $this->db->fetch(
            "SELECT COUNT(*) as count FROM exercises"
        )['count'];

        // Get recent enrollments
        $recentEnrollments = $this->db->fetchAll(
            "SELECT ce.*, u.name as student_name, c.title as course_title
             FROM course_enrollments ce
             INNER JOIN users u ON ce.student_id = u.id
             INNER JOIN courses c ON ce.course_id = c.id
             ORDER BY ce.enrolled_at DESC
             LIMIT 5"
        );

        // Get recent exercise submissions
        $recentSubmissions = $this->db->fetchAll(
            "SELECT es.*, u.name as student_name, e.title as exercise_title, c.title as course_title
             FROM exercise_submissions es
             INNER JOIN users u ON es.student_id = u.id
             INNER JOIN exercises e ON es.exercise_id = e.id
             INNER JOIN courses c ON e.course_id = c.id
             ORDER BY es.submitted_at DESC
             LIMIT 5"
        );

        $data = [
            'title' => 'Admin Dashboard - ' . APP_NAME,
            'totalStudents' => $totalStudents,
            'totalCourses' => $totalCourses,
            'totalExercises' => $totalExercises,
            'recentEnrollments' => $recentEnrollments,
            'recentSubmissions' => $recentSubmissions
        ];
        
        $this->render('admin/dashboard', $data);
    }
} 