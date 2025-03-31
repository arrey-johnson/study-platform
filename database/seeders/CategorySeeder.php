<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Programming',
                'description' => 'Learn programming languages and software development.',
            ],
            [
                'name' => 'Data Science',
                'description' => 'Courses related to data analysis, statistics, and machine learning.',
            ],
            [
                'name' => 'Web Development',
                'description' => 'Front-end, back-end, and full-stack web development courses.',
            ],
            [
                'name' => 'Mobile Development',
                'description' => 'iOS, Android, and cross-platform mobile development.',
            ],
            [
                'name' => 'Design',
                'description' => 'UX/UI design, graphic design, and visual arts.',
            ],
            [
                'name' => 'Mathematics',
                'description' => 'Algebra, calculus, statistics, and applied mathematics.',
            ],
            [
                'name' => 'Computer Science',
                'description' => 'Algorithms, data structures, and computer theory.',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
} 