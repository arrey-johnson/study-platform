# Study Platform (Core PHP)

A modern study platform built with core PHP following MVC architecture.

## Project Structure

```
study-platform/
├── public/              # Publicly accessible files
│   ├── index.php       # Entry point
│   ├── assets/         # CSS, JS, images
│   └── .htaccess      # URL rewriting rules
├── src/                # Source code
│   ├── Controllers/    # Controller classes
│   ├── Models/         # Model classes
│   ├── Views/          # View templates
│   ├── Config/         # Configuration files
│   ├── Database/       # Database connection and queries
│   ├── Helpers/        # Helper functions
│   └── Utils/          # Utility classes
├── uploads/            # User uploaded content
└── vendor/             # Third-party libraries (if any)

```

## Features

- User Authentication (Students, Teachers, Admins)
- Course Management
- Student Enrollment
- Progress Tracking
- Exercise Submissions
- Dashboard Analytics

## Setup Instructions

1. Configure your web server (Apache/Nginx) to point to the `public` directory
2. Set up a MySQL database and import the schema
3. Copy `src/Config/config.example.php` to `src/Config/config.php` and update settings
4. Ensure `uploads` directory has write permissions

## Requirements

- PHP 8.0 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- PDO PHP Extension
- GD PHP Extension (for image processing)

## Security Features

- Password Hashing
- CSRF Protection
- XSS Prevention
- SQL Injection Prevention
- Input Validation
- Session Security
