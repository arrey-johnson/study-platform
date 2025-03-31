<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination/Pagination.vue';

defineProps({
    completions: Object,
    stats: Object,
    courseCompletions: Array
});
</script>

<template>
    <Head title="Chapter Completions" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Chapter Completions
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-sm font-medium text-gray-500">Total Completions</div>
                            <div class="mt-1 text-3xl font-semibold text-gray-900">{{ stats.totalCompletions }}</div>
                        </div>
                    </div>
                    
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-sm font-medium text-gray-500">Avg. Comprehension Rating</div>
                            <div class="mt-1 text-3xl font-semibold text-gray-900">{{ stats.avgComprehension }}/5</div>
                        </div>
                    </div>
                    
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-sm font-medium text-gray-500">Avg. Time Spent (minutes)</div>
                            <div class="mt-1 text-3xl font-semibold text-gray-900">{{ stats.avgTimeSpent }}</div>
                        </div>
                    </div>
                </div>
                
                <!-- Main Content -->
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                    <!-- Completions Table -->
                    <div class="lg:col-span-3 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">All Chapter Completions</h3>
                            
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                        <tr>
                                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chapter</th>
                                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completion Date</th>
                                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stats</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="completion in completions.data" :key="completion.id">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ completion.student.name }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ completion.chapter.title }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ completion.chapter.module.course.title }}</div>
                                                <div class="text-xs text-gray-500">{{ completion.chapter.module.title }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ completion.completed_at }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500">
                                                    <div class="flex items-center mb-1">
                                                        <span class="text-xs text-gray-500 mr-2">Time:</span>
                                                        <span>{{ completion.time_spent }} min</span>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <span class="text-xs text-gray-500 mr-2">Rating:</span>
                                                        <div class="flex">
                                                            <template v-for="i in 5" :key="i">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" :class="i <= completion.comprehension_rating ? 'text-yellow-500' : 'text-gray-300'" viewBox="0 0 20 20" fill="currentColor">
                                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                                </svg>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr v-if="completions.data.length === 0">
                                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                                No chapter completions found
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <Pagination :links="completions.links" class="mt-6" />
                        </div>
                    </div>
                    
                    <!-- Course Completions -->
                    <div class="lg:col-span-1 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Courses by Completions</h3>
                            
                            <div class="space-y-4">
                                <div v-for="course in courseCompletions" :key="course.id" class="border-b border-gray-100 pb-3 last:border-b-0 last:pb-0">
                                    <div class="font-medium text-gray-900">{{ course.title }}</div>
                                    <div class="text-sm text-gray-500 mt-1">
                                        {{ course.completion_count }} chapter completions
                                    </div>
                                    <Link 
                                        :href="route('courses.show', course.id)" 
                                        class="text-sm text-blue-600 hover:text-blue-800 mt-1 inline-block"
                                    >
                                        View course →
                                    </Link>
                                </div>
                                
                                <div v-if="courseCompletions.length === 0" class="text-center py-4">
                                    <p class="text-gray-500 text-sm">No courses available</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
