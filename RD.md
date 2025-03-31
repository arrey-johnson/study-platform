Study Platform for Bao Technologies and
Travels
Requirements Document
1. Introduction
1.1 Purpose
This document defines the requirements for a self-study platform by Bao Technologies and
Travels. The platform will provide student and admin dashboards to facilitate structured learning,
close student follow-up, and interactive communication.
Admins will be able to create courses, assign students, track their progress in detail, review
submissions, and communicate effectively. Students will have access to assigned courses,
complete exercises, receive feedback, and interact with admins.
1.2 Scope
The platform will be a web-based application built with PHP for the backend and MySQL for the
database. The two main user roles are:
● Admins: Manage courses, students, assignments, progress tracking, and
communication.
● Students: Access assigned courses, complete exercises, track progress, and receive
feedback.
2. System Features
2.1 Admin Dashboard
Authentication & Authorization
Secure login/logout functionality.
Role-based access control for admins.
Course Management
Create, edit, and delete courses.
Organize courses into modules and chapters.
Add practice exercises and assignments.
Set deadlines for exercises and assignments.
Student Management
Register new students and assign them to courses.
Remove or update student assignments.
Monitor student engagement and inactivity.
Progress Tracking & Reporting
Live progress updates per student.
Track completion of modules, chapters, exercises, and time spent on materials.
Generate reports on student engagement, strengths, and weaknesses.
Identify students who are inactive or struggling.
Exercise & Submission Management
Support auto-graded exercises (e.g., multiple choice, true/false).
Allow students to upload files for assignments.
Enable manual review and feedback for written responses.
Set grading criteria and deadlines for exercises.
Messaging & Follow-up System
Real-time chat and discussion boards for students and admins.
Enable admins to send personalized reminders to inactive students.
Automated email/SMS notifications for new assignments, deadlines, and feedback.
Allow admins to schedule one-on-one check-ins with students.
2.2 Student Dashboard
Authentication
Secure login/logout functionality.
Password recovery/reset via email.
Course Access
View assigned courses, modules, and chapters.
Access study materials in different formats (PDF, video, quizzes).
Practice & Progress Tracking
Complete and submit exercises.
Receive instant feedback on auto-graded exercises.
Track progress and performance trends.
Receive reminders for pending or overdue exercises.
Messaging & Support
Chat with assigned instructors (admins).
Receive feedback on assignments and exercises.
Participate in discussion forums related to each course.
3. System Architecture
3.1 Tech Stack
Frontend: HTML, CSS, JavaScript (VanillaJS or Vue.js for interactivity)
Backend: PHP (Laravel or Core PHP)
Database: MySQL
Authentication: PHP sessions & JWT (if using APIs)
Hosting: Apache/Nginx on a VPS or shared hosting
4. Functional Requirements
4.1 User Authentication
Secure login for students and admins.
Password reset via email.
Session management for active users.
4.2 Course and Content Management
Admins can create, update, and assign courses.
Students can view only assigned courses.
4.3 Student Progress Tracking
System records module/chapter completion.
Admins can track time spent per lesson.
Reports highlight student strengths/weaknesses.
Automatic reminders for inactive students.
4.4 Exercise & Submission Workflow
Auto-grading for quizzes.
File uploads for written exercises.
Admins can review and grade manually.
Students can resubmit exercises after feedback.
4.5 Messaging & Notifications
In-app messaging for student-admin communication.
Discussion boards for student collaboration.
Automatic alerts for new assignments, deadlines, and responses.
5. Non-Functional Requirements
Security: Secure authentication, encrypted passwords.
Scalability: The system should handle multiple users simultaneously.
Performance: Fast database queries, optimized backend operations.
Usability: Mobile-friendly design, intuitive UI