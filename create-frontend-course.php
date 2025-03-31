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
        'title' => 'Modern Frontend Development with React',
        'description' => 'Master modern frontend development with React. This comprehensive course covers JavaScript fundamentals, React core concepts, state management, routing, API integration, testing, and deployment. Build real-world projects and learn best practices for creating dynamic, responsive web applications.',
        'created_by' => $admin->id,
        'is_active' => true,
        'category_id' => $category->id
    ]);
    
    echo "Created course: {$course->title} (ID: {$course->id})\n";
    
    // Create modules
    $modules = [
        [
            'title' => 'JavaScript Fundamentals for React',
            'description' => 'Build a solid foundation in modern JavaScript concepts essential for React development.',
            'order' => 1,
            'chapters' => [
                [
                    'title' => 'Modern JavaScript Syntax',
                    'content' => 'Learn modern JavaScript syntax including let/const, template literals, and arrow functions.',
                    'content_type' => 'text',
                    'order' => 1
                ],
                [
                    'title' => 'ES6+ Features',
                    'content' => 'Explore ES6+ features like destructuring, spread/rest operators, and optional chaining.',
                    'content_type' => 'text',
                    'order' => 2
                ],
                [
                    'title' => 'Working with Arrays and Objects',
                    'content' => 'Master array methods like map, filter, reduce, and learn object manipulation techniques.',
                    'content_type' => 'text',
                    'order' => 3
                ],
                [
                    'title' => 'Asynchronous JavaScript',
                    'content' => 'Understand Promises, async/await, and handling asynchronous operations in JavaScript.',
                    'content_type' => 'text',
                    'order' => 4
                ]
            ]
        ],
        [
            'title' => 'React Fundamentals',
            'description' => 'Learn the core concepts of React and how to build components-based applications.',
            'order' => 2,
            'chapters' => [
                [
                    'title' => 'Introduction to React',
                    'content' => 'Understand what React is, its core philosophy, and how it fits into modern web development.',
                    'content_type' => 'text',
                    'order' => 1
                ],
                [
                    'title' => 'Creating Your First React App',
                    'content' => 'Set up your development environment and create your first React application with Create React App.',
                    'content_type' => 'text',
                    'order' => 2
                ],
                [
                    'title' => 'JSX and React Components',
                    'content' => 'Learn JSX syntax and how to create functional and class components in React.',
                    'content_type' => 'text',
                    'order' => 3
                ],
                [
                    'title' => 'Props and Component Communication',
                    'content' => 'Understand how to pass data between components using props and handle component communication.',
                    'content_type' => 'text',
                    'order' => 4
                ]
            ]
        ],
        [
            'title' => 'React State and Lifecycle',
            'description' => 'Master state management and component lifecycle in React applications.',
            'order' => 3,
            'chapters' => [
                [
                    'title' => 'Understanding React State',
                    'content' => 'Learn how to manage component state in React and when to use it.',
                    'content_type' => 'text',
                    'order' => 1
                ],
                [
                    'title' => 'React Hooks: useState and useEffect',
                    'content' => 'Master the essential React hooks for state management and side effects.',
                    'content_type' => 'text',
                    'order' => 2
                ],
                [
                    'title' => 'Advanced Hooks',
                    'content' => 'Explore useContext, useReducer, useCallback, useMemo, and custom hooks.',
                    'content_type' => 'text',
                    'order' => 3
                ],
                [
                    'title' => 'Component Lifecycle and Optimization',
                    'content' => 'Understand component lifecycle and optimization techniques in React.',
                    'content_type' => 'text',
                    'order' => 4
                ]
            ]
        ],
        [
            'title' => 'Styling and UI in React',
            'description' => 'Learn different approaches to styling React components and building user interfaces.',
            'order' => 4,
            'chapters' => [
                [
                    'title' => 'CSS in React Applications',
                    'content' => 'Explore different ways to use CSS in React: traditional CSS, CSS modules, and inline styles.',
                    'content_type' => 'text',
                    'order' => 1
                ],
                [
                    'title' => 'CSS-in-JS with Styled Components',
                    'content' => 'Learn how to use Styled Components for component-scoped styling in React.',
                    'content_type' => 'text',
                    'order' => 2
                ],
                [
                    'title' => 'Responsive Design in React',
                    'content' => 'Implement responsive design principles in React applications.',
                    'content_type' => 'text',
                    'order' => 3
                ],
                [
                    'title' => 'UI Component Libraries',
                    'content' => 'Integrate and customize UI libraries like Material-UI or Chakra UI in your React applications.',
                    'content_type' => 'text',
                    'order' => 4
                ]
            ]
        ],
        [
            'title' => 'Routing and Navigation',
            'description' => 'Implement client-side routing and navigation in single-page React applications.',
            'order' => 5,
            'chapters' => [
                [
                    'title' => 'Introduction to React Router',
                    'content' => 'Learn the basics of React Router and how to set up routes in your application.',
                    'content_type' => 'text',
                    'order' => 1
                ],
                [
                    'title' => 'Route Parameters and Nested Routes',
                    'content' => 'Implement dynamic routes with parameters and create nested route structures.',
                    'content_type' => 'text',
                    'order' => 2
                ],
                [
                    'title' => 'Protected Routes and Authentication',
                    'content' => 'Create protected routes that require authentication before access.',
                    'content_type' => 'text',
                    'order' => 3
                ],
                [
                    'title' => 'Navigation and History Management',
                    'content' => 'Manage navigation programmatically and handle browser history in React applications.',
                    'content_type' => 'text',
                    'order' => 4
                ]
            ]
        ],
        [
            'title' => 'State Management with Redux',
            'description' => 'Learn how to manage application state with Redux and integrate it with React.',
            'order' => 6,
            'chapters' => [
                [
                    'title' => 'Introduction to Redux',
                    'content' => 'Understand the core concepts of Redux: store, actions, and reducers.',
                    'content_type' => 'text',
                    'order' => 1
                ],
                [
                    'title' => 'Redux with React: React-Redux',
                    'content' => 'Integrate Redux with React using the React-Redux library.',
                    'content_type' => 'text',
                    'order' => 2
                ],
                [
                    'title' => 'Asynchronous Actions with Redux Thunk',
                    'content' => 'Handle asynchronous operations in Redux using Redux Thunk middleware.',
                    'content_type' => 'text',
                    'order' => 3
                ],
                [
                    'title' => 'Redux Toolkit',
                    'content' => 'Simplify Redux development with Redux Toolkit, the official, opinionated approach to Redux.',
                    'content_type' => 'text',
                    'order' => 4
                ]
            ]
        ],
        [
            'title' => 'API Integration and Data Fetching',
            'description' => 'Learn how to fetch data from APIs and integrate external services in React applications.',
            'order' => 7,
            'chapters' => [
                [
                    'title' => 'Fetching Data with Fetch API and Axios',
                    'content' => 'Learn different methods for making HTTP requests in React applications.',
                    'content_type' => 'text',
                    'order' => 1
                ],
                [
                    'title' => 'React Query for Data Fetching',
                    'content' => 'Implement efficient data fetching, caching, and state management with React Query.',
                    'content_type' => 'text',
                    'order' => 2
                ],
                [
                    'title' => 'Working with REST APIs',
                    'content' => 'Integrate RESTful APIs into your React applications and handle CRUD operations.',
                    'content_type' => 'text',
                    'order' => 3
                ],
                [
                    'title' => 'GraphQL with Apollo Client',
                    'content' => 'Learn how to use GraphQL with Apollo Client in React applications.',
                    'content_type' => 'text',
                    'order' => 4
                ]
            ]
        ],
        [
            'title' => 'Testing React Applications',
            'description' => 'Learn how to test React components and applications for reliability and quality.',
            'order' => 8,
            'chapters' => [
                [
                    'title' => 'Introduction to Testing in React',
                    'content' => 'Understand the importance of testing and different types of tests for React applications.',
                    'content_type' => 'text',
                    'order' => 1
                ],
                [
                    'title' => 'Unit Testing with Jest and React Testing Library',
                    'content' => 'Write unit tests for React components using Jest and React Testing Library.',
                    'content_type' => 'text',
                    'order' => 2
                ],
                [
                    'title' => 'Integration Testing',
                    'content' => 'Create integration tests to verify that components work together as expected.',
                    'content_type' => 'text',
                    'order' => 3
                ],
                [
                    'title' => 'End-to-End Testing with Cypress',
                    'content' => 'Implement end-to-end tests for your React applications using Cypress.',
                    'content_type' => 'text',
                    'order' => 4
                ]
            ]
        ],
        [
            'title' => 'Performance Optimization',
            'description' => 'Learn techniques to optimize the performance of React applications.',
            'order' => 9,
            'chapters' => [
                [
                    'title' => 'React Performance Fundamentals',
                    'content' => 'Understand common performance issues in React and how to identify them.',
                    'content_type' => 'text',
                    'order' => 1
                ],
                [
                    'title' => 'Memoization with React.memo, useMemo, and useCallback',
                    'content' => 'Implement memoization techniques to prevent unnecessary re-renders.',
                    'content_type' => 'text',
                    'order' => 2
                ],
                [
                    'title' => 'Code Splitting and Lazy Loading',
                    'content' => 'Improve initial load time with code splitting and lazy loading components.',
                    'content_type' => 'text',
                    'order' => 3
                ],
                [
                    'title' => 'Performance Monitoring and Optimization',
                    'content' => 'Use tools to monitor and optimize the performance of your React applications.',
                    'content_type' => 'text',
                    'order' => 4
                ]
            ]
        ],
        [
            'title' => 'Deployment and CI/CD',
            'description' => 'Learn how to deploy React applications and set up continuous integration and deployment.',
            'order' => 10,
            'chapters' => [
                [
                    'title' => 'Building for Production',
                    'content' => 'Prepare your React application for production deployment.',
                    'content_type' => 'text',
                    'order' => 1
                ],
                [
                    'title' => 'Deploying to Netlify and Vercel',
                    'content' => 'Deploy your React applications to popular hosting platforms like Netlify and Vercel.',
                    'content_type' => 'text',
                    'order' => 2
                ],
                [
                    'title' => 'Setting Up CI/CD Pipelines',
                    'content' => 'Implement continuous integration and deployment pipelines for your React applications.',
                    'content_type' => 'text',
                    'order' => 3
                ],
                [
                    'title' => 'Environment Variables and Configuration',
                    'content' => 'Manage environment variables and configuration for different deployment environments.',
                    'content_type' => 'text',
                    'order' => 4
                ]
            ]
        ],
        [
            'title' => 'Final Project: Building a Full-Featured React Application',
            'description' => 'Apply everything you\'ve learned to build a complete, production-ready React application.',
            'order' => 11,
            'chapters' => [
                [
                    'title' => 'Project Planning and Setup',
                    'content' => 'Plan your project architecture and set up the initial project structure.',
                    'content_type' => 'text',
                    'order' => 1
                ],
                [
                    'title' => 'Implementing Core Features',
                    'content' => 'Build the main features of your application using React and related technologies.',
                    'content_type' => 'text',
                    'order' => 2
                ],
                [
                    'title' => 'Testing and Quality Assurance',
                    'content' => 'Write tests and ensure the quality of your React application.',
                    'content_type' => 'text',
                    'order' => 3
                ],
                [
                    'title' => 'Deployment and Project Presentation',
                    'content' => 'Deploy your application to production and prepare a presentation of your work.',
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
    
    echo "\nFrontend Development with React course created successfully!\n";
    
} catch (Exception $e) {
    // Rollback transaction on error
    DB::rollBack();
    echo "Error: " . $e->getMessage() . "\n";
}
