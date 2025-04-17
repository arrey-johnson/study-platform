-- Drop database if it exists (comment this out in production)
-- DROP DATABASE IF EXISTS study_platform;

-- Create database
CREATE DATABASE IF NOT EXISTS study_platform CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Use the database
USE study_platform;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    role ENUM('student', 'teacher', 'admin') NOT NULL DEFAULT 'student',
    profile_image VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Courses table
CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    category_id INT,
    teacher_id INT NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    status ENUM('draft', 'published', 'archived') NOT NULL DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Course enrollments table
CREATE TABLE IF NOT EXISTS course_enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    student_id INT NOT NULL,
    enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'completed', 'dropped') NOT NULL DEFAULT 'active',
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_enrollment (course_id, student_id)
);

-- Modules table
CREATE TABLE IF NOT EXISTS modules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    order_index INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- Chapters table
CREATE TABLE IF NOT EXISTS chapters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    module_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    order_index INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE
);

-- Chapter completions table
CREATE TABLE IF NOT EXISTS chapter_completions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chapter_id INT NOT NULL,
    student_id INT NOT NULL,
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (chapter_id) REFERENCES chapters(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_completion (chapter_id, student_id)
);

-- Exercises table
CREATE TABLE IF NOT EXISTS exercises (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    type ENUM('multiple_choice', 'coding', 'essay') NOT NULL,
    points INT NOT NULL DEFAULT 10,
    due_date DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- Exercise submissions table
CREATE TABLE IF NOT EXISTS exercise_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    exercise_id INT NOT NULL,
    student_id INT NOT NULL,
    content TEXT,
    status ENUM('submitted', 'graded', 'completed') NOT NULL DEFAULT 'submitted',
    score INT DEFAULT NULL,
    feedback TEXT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    graded_at TIMESTAMP NULL,
    FOREIGN KEY (exercise_id) REFERENCES exercises(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert default admin user
INSERT INTO users (username, email, password, first_name, last_name, role)
VALUES ('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'User', 'admin');

-- Insert default teacher
INSERT INTO users (username, email, password, first_name, last_name, role)
VALUES ('teacher', 'teacher@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Teacher', 'User', 'teacher');

-- Insert default student
INSERT INTO users (username, email, password, first_name, last_name, role)
VALUES ('student', 'student@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Student', 'User', 'student');

-- Insert sample categories
INSERT INTO categories (name, description)
VALUES 
('Programming', 'Learn programming languages and software development'),
('Web Development', 'Build websites and web applications'),
('Data Science', 'Analyze and visualize data');

-- Insert sample courses
INSERT INTO courses (title, description, category_id, teacher_id, status)
VALUES 
('Introduction to PHP', 'Learn the basics of PHP programming', 1, 2, 'published'),
('Web Development with HTML & CSS', 'Create responsive websites', 2, 2, 'published'),
('JavaScript Fundamentals', 'Master JavaScript programming', 2, 2, 'published');

-- Enroll student in courses
INSERT INTO course_enrollments (course_id, student_id)
VALUES 
(1, 3),
(2, 3);

-- Add modules to courses
INSERT INTO modules (course_id, title, order_index)
VALUES 
(1, 'PHP Basics', 1),
(1, 'PHP Functions', 2),
(1, 'PHP and MySQL', 3),
(2, 'HTML Fundamentals', 1),
(2, 'CSS Styling', 2);

-- Add chapters to modules
INSERT INTO chapters (module_id, title, content, order_index)
VALUES 
(1, 'Variables and Data Types', 'PHP variables are used to store data...', 1),
(1, 'Control Structures', 'PHP supports conditional statements...', 2),
(2, 'Creating Functions', 'Functions are blocks of reusable code...', 1),
(4, 'HTML Document Structure', 'Every HTML document starts with...', 1),
(5, 'CSS Selectors', 'CSS selectors are used to target HTML elements...', 1);

-- Add exercises
INSERT INTO exercises (course_id, title, description, type, points, due_date)
VALUES 
(1, 'PHP Variables Quiz', 'Test your knowledge of PHP variables', 'multiple_choice', 10, DATE_ADD(NOW(), INTERVAL 7 DAY)),
(1, 'Create a Function', 'Write a PHP function that calculates the factorial of a number', 'coding', 20, DATE_ADD(NOW(), INTERVAL 14 DAY)),
(2, 'Build a Responsive Layout', 'Create a responsive webpage using HTML and CSS', 'coding', 30, DATE_ADD(NOW(), INTERVAL 10 DAY));

-- Add some chapter completions
INSERT INTO chapter_completions (chapter_id, student_id)
VALUES 
(1, 3),
(2, 3);

-- Add some exercise submissions
INSERT INTO exercise_submissions (exercise_id, student_id, content, status, score)
VALUES 
(1, 3, 'My answers to the quiz...', 'completed', 8);
