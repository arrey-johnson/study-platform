<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Category;
use App\Models\Course;
use App\Models\Module;
use App\Models\Chapter;
use App\Models\User;
use Illuminate\Support\Facades\DB;

// Start transaction
DB::beginTransaction();

try {
    // Get or create Web Development category
    $category = Category::firstOrCreate(
        ['name' => 'Web Development'],
        [
            'description' => 'Courses related to web development and programming',
            'is_active' => true
        ]
    );
    
    // Get admin user
    $admin = User::whereHas('role', function($query) {
        $query->where('name', 'admin');
    })->first();
    
    if (!$admin) {
        throw new Exception('No admin user found');
    }
    
    // Create the course
    $course = Course::create([
        'title' => 'Comprehensive Backend Development',
        'description' => 'Master backend development with this comprehensive course covering server-side programming, databases, APIs, authentication, and deployment. Learn to build robust, scalable web applications using modern technologies and best practices.',
        'created_by' => $admin->id,
        'is_active' => true,
        'category_id' => $category->id
    ]);
    
    echo "Created course: {$course->title} (ID: {$course->id})\n";
    
    // Create modules
    $modules = [
        [
            'title' => 'Introduction to Backend Development',
            'description' => 'Learn the fundamentals of backend development and understand the role of server-side programming in web applications.',
            'order' => 1,
            'chapters' => [
                [
                    'title' => 'What is Backend Development?',
                    'content' => 'This chapter introduces the concept of backend development and its importance in web applications.',
                    'content_type' => 'text',
                    'order' => 1
                ],
                [
                    'title' => 'Backend vs Frontend: Understanding the Differences',
                    'content' => 'Learn the key differences between backend and frontend development and how they work together.',
                    'content_type' => 'text',
                    'order' => 2
                ],
                [
                    'title' => 'Backend Technologies Overview',
                    'content' => 'Explore the various technologies, languages, and frameworks used in modern backend development.',
                    'content_type' => 'text',
                    'order' => 3
                ]
            ]
        ],
        [
            'title' => 'Server-Side Programming with PHP',
            'description' => 'Master PHP, one of the most popular server-side programming languages for web development.',
            'order' => 2,
            'chapters' => [
                [
                    'title' => 'PHP Basics and Syntax',
                    'content' => 'Learn the fundamental syntax and concepts of PHP programming.',
                    'content_type' => 'text',
                    'order' => 1
                ],
                [
                    'title' => 'Working with Forms and User Input',
                    'content' => 'Understand how to process form submissions and handle user input securely.',
                    'content_type' => 'text',
                    'order' => 2
                ],
                [
                    'title' => 'PHP and Object-Oriented Programming',
                    'content' => 'Explore object-oriented programming concepts in PHP and how to implement them.',
                    'content_type' => 'text',
                    'order' => 3
                ],
                [
                    'title' => 'Error Handling and Debugging',
                    'content' => 'Learn effective techniques for error handling, logging, and debugging in PHP applications.',
                    'content_type' => 'text',
                    'order' => 4
                ]
            ]
        ],
        [
            'title' => 'Database Design and Management',
            'description' => 'Learn how to design, implement, and manage databases for web applications.',
            'order' => 3,
            'chapters' => [
                [
                    'title' => 'Relational Database Concepts',
                    'content' => 'Understand the fundamental concepts of relational databases and their role in web applications.',
                    'content_type' => 'text',
                    'order' => 1
                ],
                [
                    'title' => 'SQL Fundamentals',
                    'content' => 'Learn the SQL language for querying and manipulating database data.',
                    'content_type' => 'text',
                    'order' => 2
                ],
                [
                    'title' => 'Database Design Best Practices',
                    'content' => 'Explore best practices for designing efficient, scalable, and maintainable database schemas.',
                    'content_type' => 'text',
                    'order' => 3
                ],
                [
                    'title' => 'Working with MySQL',
                    'content' => 'Learn how to work with MySQL, one of the most popular relational database management systems.',
                    'content_type' => 'text',
                    'order' => 4
                ]
            ]
        ],
        [
            'title' => 'Building RESTful APIs',
            'description' => 'Learn how to design and implement RESTful APIs for web applications.',
            'order' => 4,
            'chapters' => [
                [
                    'title' => 'REST Architecture Principles',
                    'content' => 'Understand the principles and constraints of REST architecture.',
                    'content_type' => 'text',
                    'order' => 1
                ],
                [
                    'title' => 'Designing RESTful Endpoints',
                    'content' => 'Learn how to design intuitive and effective RESTful API endpoints.',
                    'content_type' => 'text',
                    'order' => 2
                ],
                [
                    'title' => 'API Authentication and Security',
                    'content' => 'Explore various methods for securing APIs and implementing authentication.',
                    'content_type' => 'text',
                    'order' => 3
                ],
                [
                    'title' => 'API Documentation with Swagger/OpenAPI',
                    'content' => 'Learn how to document your APIs effectively using Swagger/OpenAPI.',
                    'content_type' => 'text',
                    'order' => 4
                ]
            ]
        ],
        [
            'title' => 'Authentication and Authorization',
            'description' => 'Implement secure user authentication and authorization systems in web applications.',
            'order' => 5,
            'chapters' => [
                [
                    'title' => 'User Authentication Fundamentals',
                    'content' => 'Understand the concepts and best practices for user authentication.',
                    'content_type' => 'text',
                    'order' => 1
                ],
                [
                    'title' => 'Implementing JWT Authentication',
                    'content' => 'Learn how to implement JSON Web Token (JWT) authentication in your applications.',
                    'content_type' => 'text',
                    'order' => 2
                ],
                [
                    'title' => 'Role-Based Access Control',
                    'content' => 'Explore role-based access control (RBAC) for managing user permissions.',
                    'content_type' => 'text',
                    'order' => 3
                ],
                [
                    'title' => 'OAuth 2.0 and Social Authentication',
                    'content' => 'Implement OAuth 2.0 and social login functionality in your applications.',
                    'content_type' => 'text',
                    'order' => 4
                ]
            ]
        ],
        [
            'title' => 'Performance Optimization and Scaling',
            'description' => 'Learn techniques for optimizing and scaling backend applications to handle high traffic.',
            'order' => 6,
            'chapters' => [
                [
                    'title' => 'Backend Performance Optimization Techniques',
                    'content' => 'Explore various techniques for optimizing backend application performance.',
                    'content_type' => 'text',
                    'order' => 1
                ],
                [
                    'title' => 'Caching Strategies',
                    'content' => 'Learn different caching strategies to improve application performance.',
                    'content_type' => 'text',
                    'order' => 2
                ],
                [
                    'title' => 'Database Optimization',
                    'content' => 'Understand how to optimize database queries and structure for better performance.',
                    'content_type' => 'text',
                    'order' => 3
                ],
                [
                    'title' => 'Horizontal and Vertical Scaling',
                    'content' => 'Explore different approaches to scaling your application to handle increased load.',
                    'content_type' => 'text',
                    'order' => 4
                ]
            ]
        ],
        [
            'title' => 'Deployment and DevOps',
            'description' => 'Learn how to deploy and manage backend applications in production environments.',
            'order' => 7,
            'chapters' => [
                [
                    'title' => 'Introduction to DevOps',
                    'content' => 'Understand the principles and practices of DevOps in modern web development.',
                    'content_type' => 'text',
                    'order' => 1
                ],
                [
                    'title' => 'Containerization with Docker',
                    'content' => 'Learn how to containerize your applications using Docker for consistent deployment.',
                    'content_type' => 'text',
                    'order' => 2
                ],
                [
                    'title' => 'CI/CD Pipelines',
                    'content' => 'Implement continuous integration and continuous deployment pipelines for your applications.',
                    'content_type' => 'text',
                    'order' => 3
                ],
                [
                    'title' => 'Monitoring and Logging',
                    'content' => 'Set up monitoring and logging systems to track application performance and issues.',
                    'content_type' => 'text',
                    'order' => 4
                ]
            ]
        ],
        [
            'title' => 'Final Project: Building a Complete Backend System',
            'description' => 'Apply everything you\'ve learned to build a complete backend system for a real-world application.',
            'order' => 8,
            'chapters' => [
                [
                    'title' => 'Project Requirements and Planning',
                    'content' => 'Define the requirements and create a plan for your backend project.',
                    'content_type' => 'text',
                    'order' => 1
                ],
                [
                    'title' => 'Implementing Core Features',
                    'content' => 'Build the core features of your backend system based on the project requirements.',
                    'content_type' => 'text',
                    'order' => 2
                ],
                [
                    'title' => 'Testing and Quality Assurance',
                    'content' => 'Implement comprehensive testing to ensure the quality and reliability of your backend system.',
                    'content_type' => 'text',
                    'order' => 3
                ],
                [
                    'title' => 'Deployment and Presentation',
                    'content' => 'Deploy your backend system to a production environment and prepare a presentation of your work.',
                    'content_type' => 'text',
                    'order' => 4
                ]
            ]
        ]
    ];
    
    // Create modules and chapters
    foreach ($modules as $moduleData) {
        $chapters = $moduleData['chapters'] ?? [];
        unset($moduleData['chapters']);
        
        $moduleData['course_id'] = $course->id;
        $moduleData['is_active'] = true;
        
        $module = Module::create($moduleData);
        echo "Created module: {$module->title}\n";
        
        foreach ($chapters as $chapterData) {
            $chapterData['module_id'] = $module->id;
            $chapterData['is_active'] = true;
            
            $chapter = Chapter::create($chapterData);
            echo "  - Created chapter: {$chapter->title}\n";
        }
    }
    
    // Commit transaction
    DB::commit();
    
    echo "\nBackend Development course created successfully!\n";
    
} catch (Exception $e) {
    // Rollback transaction on error
    DB::rollBack();
    echo "Error: " . $e->getMessage() . "\n";
}
