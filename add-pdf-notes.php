<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Course;
use App\Models\Chapter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

// Function to generate a simple text file with markdown content
function generatePdfNotes($title, $content, $filename) {
    // Save the content to a markdown file (we'll treat it as a PDF for demonstration)
    $filePath = 'pdf-notes/' . $filename . '.md';
    Storage::disk('public')->put($filePath, $content);
    
    return $filePath;
}

// Start transaction
DB::beginTransaction();

try {
    // Check if the pdf_notes column exists in the chapters table
    if (!Schema::hasColumn('chapters', 'pdf_notes')) {
        Schema::table('chapters', function ($table) {
            $table->string('pdf_notes')->nullable();
        });
        echo "Added pdf_notes column to chapters table\n";
    }

    // Get the backend and frontend courses
    $backendCourse = Course::where('title', 'like', '%Backend Development%')->first();
    $frontendCourse = Course::where('title', 'like', '%Frontend Development%')->first();
    
    if (!$backendCourse && !$frontendCourse) {
        throw new Exception('Courses not found. Please create the courses first.');
    }
    
    $courses = [];
    if ($backendCourse) {
        $courses[] = $backendCourse;
        echo "Found backend course: {$backendCourse->title} (ID: {$backendCourse->id})\n";
    }
    if ($frontendCourse) {
        $courses[] = $frontendCourse;
        echo "Found frontend course: {$frontendCourse->title} (ID: {$frontendCourse->id})\n";
    }
    
    $totalUpdated = 0;
    
    foreach ($courses as $course) {
        echo "Adding PDF notes to course: {$course->title}\n";
        
        // Get all modules and chapters for the course
        $modules = $course->modules()->with('chapters')->get();
        echo "Found " . count($modules) . " modules for this course\n";
        
        foreach ($modules as $module) {
            echo "Processing module: {$module->title} (ID: {$module->id})\n";
            echo "Found " . count($module->chapters) . " chapters in this module\n";
            
            foreach ($module->chapters as $chapter) {
                // Generate a unique filename for the PDF
                $safeCourseName = preg_replace('/[^a-z0-9]+/', '-', strtolower($course->title));
                $safeModuleName = preg_replace('/[^a-z0-9]+/', '-', strtolower($module->title));
                $safeChapterName = preg_replace('/[^a-z0-9]+/', '-', strtolower($chapter->title));
                
                $filename = $safeCourseName . '-' . $safeModuleName . '-' . $safeChapterName;
                
                // Generate sample content for the PDF
                $content = "# {$chapter->title}\n\n";
                $content .= "## Module: {$module->title}\n\n";
                $content .= "### Course: {$course->title}\n\n";
                $content .= "This comprehensive guide covers everything you need to know about {$chapter->title}.\n\n";
                
                // Add specific content based on the chapter title
                if (strpos($chapter->title, 'Introduction') !== false) {
                    $content .= "## Introduction\n\n";
                    $content .= "This chapter introduces the fundamental concepts and provides an overview of what you'll learn.\n\n";
                    $content .= "### Key Concepts\n\n";
                    $content .= "- Understanding the basics\n";
                    $content .= "- Historical context and evolution\n";
                    $content .= "- Modern applications and use cases\n\n";
                } elseif (strpos($chapter->title, 'Fundamentals') !== false || strpos($chapter->title, 'Basics') !== false) {
                    $content .= "## Core Fundamentals\n\n";
                    $content .= "This chapter covers the essential building blocks and foundational knowledge.\n\n";
                    $content .= "### Key Concepts\n\n";
                    $content .= "- Basic syntax and structure\n";
                    $content .= "- Core principles and patterns\n";
                    $content .= "- Best practices for beginners\n\n";
                } elseif (strpos($chapter->title, 'Advanced') !== false) {
                    $content .= "## Advanced Techniques\n\n";
                    $content .= "This chapter explores advanced concepts and techniques for experienced developers.\n\n";
                    $content .= "### Key Concepts\n\n";
                    $content .= "- Advanced patterns and methodologies\n";
                    $content .= "- Performance optimization strategies\n";
                    $content .= "- Complex problem-solving approaches\n\n";
                } else {
                    $content .= "## Detailed Overview\n\n";
                    $content .= "This chapter provides a comprehensive exploration of {$chapter->title}.\n\n";
                    $content .= "### Key Concepts\n\n";
                    $content .= "- Core principles and methodologies\n";
                    $content .= "- Practical implementation strategies\n";
                    $content .= "- Real-world applications and examples\n\n";
                }
                
                $content .= "## Practical Examples\n\n";
                $content .= "Let's look at some practical examples to better understand these concepts.\n\n";
                
                // Add code examples based on course type
                if (strpos($course->title, 'Backend') !== false) {
                    $content .= "```php\n";
                    $content .= "<?php\n\n";
                    $content .= "// Example code for {$chapter->title}\n";
                    $content .= "function processData(\$data) {\n";
                    $content .= "    // Validate input\n";
                    $content .= "    if (empty(\$data)) {\n";
                    $content .= "        throw new Exception('Data cannot be empty');\n";
                    $content .= "    }\n\n";
                    $content .= "    // Process the data\n";
                    $content .= "    \$result = [];\n";
                    $content .= "    foreach (\$data as \$key => \$value) {\n";
                    $content .= "        \$result[\$key] = transform(\$value);\n";
                    $content .= "    }\n\n";
                    $content .= "    return \$result;\n";
                    $content .= "}\n";
                    $content .= "```\n\n";
                } else {
                    $content .= "```javascript\n";
                    $content .= "// Example code for {$chapter->title}\n";
                    $content .= "import React, { useState, useEffect } from 'react';\n\n";
                    $content .= "function ExampleComponent({ data }) {\n";
                    $content .= "  const [processedData, setProcessedData] = useState([]);\n\n";
                    $content .= "  useEffect(() => {\n";
                    $content .= "    // Process the data when it changes\n";
                    $content .= "    if (data && data.length > 0) {\n";
                    $content .= "      const result = data.map(item => transformItem(item));\n";
                    $content .= "      setProcessedData(result);\n";
                    $content .= "    }\n";
                    $content .= "  }, [data]);\n\n";
                    $content .= "  return (\n";
                    $content .= "    <div className=\"example-component\">\n";
                    $content .= "      {processedData.map(item => (\n";
                    $content .= "        <div key={item.id}>{item.name}</div>\n";
                    $content .= "      ))}\n";
                    $content .= "    </div>\n";
                    $content .= "  );\n";
                    $content .= "}\n";
                    $content .= "```\n\n";
                }
                
                $content .= "## Best Practices\n\n";
                $content .= "Follow these best practices to ensure optimal results:\n\n";
                $content .= "1. Always validate user input\n";
                $content .= "2. Write clean, maintainable code\n";
                $content .= "3. Document your code thoroughly\n";
                $content .= "4. Test your code extensively\n";
                $content .= "5. Consider performance implications\n\n";
                
                $content .= "## Summary\n\n";
                $content .= "In this chapter, we've covered the essential aspects of {$chapter->title}. You should now have a solid understanding of the core concepts and be ready to apply them in practical scenarios.\n\n";
                
                $content .= "## Additional Resources\n\n";
                $content .= "- Official Documentation: [Link to documentation]\n";
                $content .= "- Community Forums: [Link to forums]\n";
                $content .= "- Video Tutorials: [Link to tutorials]\n";
                $content .= "- Related Books: [Book recommendations]\n\n";
                
                $content .= "## Exercises\n\n";
                $content .= "1. Implement a basic version of the concepts covered\n";
                $content .= "2. Extend the example code with additional features\n";
                $content .= "3. Create a small project that applies these principles\n";
                $content .= "4. Review and refactor your code using the best practices\n\n";
                
                $content .= "## Next Steps\n\n";
                $content .= "After completing this chapter, you'll be ready to move on to the next topic. Make sure you've completed all the exercises and feel comfortable with the material before proceeding.";
                
                // Generate the PDF notes
                echo "  - Generating PDF notes for chapter: {$chapter->title} (ID: {$chapter->id})\n";
                $pdfPath = generatePdfNotes($chapter->title, $content, $filename);
                
                // Update the chapter record with the PDF path
                $chapter->pdf_notes = $pdfPath;
                $chapter->save();
                
                $totalUpdated++;
            }
        }
    }
    
    // Commit transaction
    DB::commit();
    
    echo "\nSuccessfully added PDF notes to {$totalUpdated} chapters!\n";
    
} catch (Exception $e) {
    // Rollback transaction on error
    DB::rollBack();
    echo "Error: " . $e->getMessage() . "\n";
}
