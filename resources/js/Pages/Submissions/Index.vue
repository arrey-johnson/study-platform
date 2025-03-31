<template>
    <Head :title="`Submissions - ${exercise.title}`" />
    
    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Breadcrumb Navigation -->
                        <div class="flex items-center text-sm text-gray-500 mb-6">
                            <Link :href="route('dashboard')" class="hover:text-blue-600">Dashboard</Link>
                            <svg class="h-4 w-4 mx-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                            <Link :href="route('courses.show', course.id)" class="hover:text-blue-600">{{ course.title }}</Link>
                            <svg class="h-4 w-4 mx-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                            <Link :href="route('modules.show', module.id)" class="hover:text-blue-600">{{ module.title }}</Link>
                            <svg class="h-4 w-4 mx-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                            <Link :href="route('chapters.show', chapter.id)" class="hover:text-blue-600">{{ chapter.title }}</Link>
                            <svg class="h-4 w-4 mx-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                            <Link :href="route('courses.modules.chapters.exercises.show', { course: course.id, module: module.id, chapter: chapter.id, exercise: exercise.id })" class="hover:text-blue-600">{{ exercise.title }}</Link>
                            <svg class="h-4 w-4 mx-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                            <span>Submissions</span>
                        </div>
                        
                        <!-- Header -->
                        <div class="border-b pb-4 mb-6">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-900">All Submissions</h2>
                                    <p class="text-gray-600 mt-1">
                                        {{ exercise.title }} - {{ formatExerciseType(exercise.type) }} ({{ exercise.points }} points)
                                    </p>
                                </div>
                                <div class="flex space-x-3">
                                    <Link 
                                        :href="route('courses.modules.chapters.exercises.show', { course: course.id, module: module.id, chapter: chapter.id, exercise: exercise.id })" 
                                        class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                                        </svg>
                                        Back to Exercise
                                    </Link>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Submissions List -->
                        <div v-if="submissions.length === 0" class="text-center py-12 border border-gray-200 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <p class="text-gray-500">No submissions yet</p>
                        </div>
                        
                        <div v-else>
                            <!-- Submission Stats -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <h3 class="text-blue-700 font-medium mb-2">Total Submissions</h3>
                                    <p class="text-2xl font-bold">{{ submissions.length }}</p>
                                </div>
                                
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                    <h3 class="text-green-700 font-medium mb-2">Graded</h3>
                                    <p class="text-2xl font-bold">{{ submissions.filter(s => s.graded_at).length }}</p>
                                </div>
                                
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                    <h3 class="text-yellow-700 font-medium mb-2">Pending</h3>
                                    <p class="text-2xl font-bold">{{ submissions.filter(s => !s.graded_at).length }}</p>
                                </div>
                            </div>
                            
                            <!-- Submissions Table -->
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="submission in submissions" :key="submission.id" class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">{{ submission.student.name }}</div>
                                                        <div class="text-sm text-gray-500">{{ submission.student.email }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ formatDate(submission.created_at) }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span :class="{
                                                    'px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full': true,
                                                    'bg-green-100 text-green-800': submission.graded_at,
                                                    'bg-yellow-100 text-yellow-800': !submission.graded_at
                                                }">
                                                    {{ submission.graded_at ? 'Graded' : 'Pending' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    {{ submission.score !== null ? `${submission.score} / ${exercise.points}` : '—' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <Link :href="route('submissions.show', submission.id)" class="text-blue-600 hover:text-blue-900 mr-4">
                                                    View
                                                </Link>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Link, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    exercise: Object,
    submissions: Array,
    chapter: Object,
    module: Object,
    course: Object
});

const formatDate = (date) => {
    if (!date) return '';
    const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    return new Date(date).toLocaleDateString(undefined, options);
};

const formatExerciseType = (type) => {
    switch (type) {
        case 'multiple_choice':
            return 'Multiple Choice';
        case 'true_false':
            return 'True/False';
        case 'written':
            return 'Written Answer';
        case 'file_upload':
            return 'File Upload';
        default:
            return type.replace('_', ' ');
    }
};
</script>
