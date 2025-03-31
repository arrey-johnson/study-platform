<template>
    <Head :title="`Exercises - ${chapter.title}`" />
    
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Exercises - {{ chapter.title }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ module.title }} - {{ course.title }}
                    </p>
                </div>
                <div class="flex space-x-4">
                    <Link
                        :href="route('modules.show', module.id)"
                        class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300"
                    >
                        Back to Module
                    </Link>
                    <Link
                        :href="route('chapters.show', chapter.id)"
                        class="inline-flex items-center px-4 py-2 bg-blue-200 border border-transparent rounded-md font-semibold text-xs text-blue-700 uppercase tracking-widest hover:bg-blue-300"
                    >
                        View Chapter
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-medium text-gray-900">Exercise List</h3>
                            <Link
                                v-if="$page.props.auth.user.role?.name === 'admin'"
                                :href="route('courses.modules.chapters.exercises.create', {
                                    course: course.id,
                                    module: module.id,
                                    chapter: chapter.id
                                })"
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add Exercise
                            </Link>
                        </div>

                        <div v-if="exercises.length === 0" class="text-center py-8">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <p class="text-gray-500 mb-2">No exercises available for this chapter yet.</p>
                            <div v-if="$page.props.auth.user.role?.name === 'admin'" class="mt-4">
                                <Link
                                    :href="route('courses.modules.chapters.exercises.create', {
                                        course: course.id,
                                        module: module.id,
                                        chapter: chapter.id
                                    })"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700"
                                >
                                    Create First Exercise
                                </Link>
                            </div>
                        </div>

                        <div v-else class="space-y-4">
                            <div v-for="exercise in exercises" :key="exercise.id" class="border rounded-lg p-6 hover:bg-gray-50 transition-colors">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center">
                                            <h4 class="text-lg font-medium text-gray-900">{{ exercise.title }}</h4>
                                            <span v-if="exercise.is_active" class="ml-2 px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Active</span>
                                            <span v-else class="ml-2 px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">Inactive</span>
                                        </div>
                                        <p class="mt-2 text-sm text-gray-600">{{ exercise.description }}</p>
                                        <div class="mt-3 flex flex-wrap items-center gap-4 text-sm text-gray-500">
                                            <div class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <span class="capitalize">{{ formatExerciseType(exercise.type) }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span>{{ exercise.points }} points</span>
                                            </div>
                                            <div v-if="exercise.deadline" class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <span>Due: {{ formatDate(exercise.deadline) }}</span>
                                            </div>
                                            <div v-if="exercise.submission_status" class="flex items-center">
                                                <span :class="{
                                                    'text-green-600': exercise.submission_status === 'completed',
                                                    'text-yellow-600': exercise.submission_status === 'pending',
                                                    'text-red-600': exercise.submission_status === 'late'
                                                }">
                                                    {{ formatSubmissionStatus(exercise.submission_status) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex space-x-2 ml-4">
                                        <Link
                                            :href="route('courses.modules.chapters.exercises.show', {
                                                course: course.id,
                                                module: module.id,
                                                chapter: chapter.id,
                                                exercise: exercise.id
                                            })"
                                            class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm transition-colors"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            View
                                        </Link>
                                        <template v-if="$page.props.auth.user.role?.name === 'admin'">
                                            <Link
                                                :href="route('courses.modules.chapters.exercises.edit', {
                                                    course: course.id,
                                                    module: module.id,
                                                    chapter: chapter.id,
                                                    exercise: exercise.id
                                                })"
                                                class="inline-flex items-center px-3 py-2 bg-orange-600 text-white rounded hover:bg-orange-700 text-sm transition-colors"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Edit
                                            </Link>
                                            <button
                                                @click="deleteExercise(exercise)"
                                                class="inline-flex items-center px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm transition-colors"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Delete
                                            </button>
                                        </template>
                                    </div>
                                </div>
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
import { router } from '@inertiajs/vue3';

const props = defineProps({
    course: Object,
    module: Object,
    chapter: Object,
    exercises: Array
});

const formatDate = (date) => {
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

const formatSubmissionStatus = (status) => {
    switch (status) {
        case 'completed':
            return 'Completed';
        case 'pending':
            return 'Pending Review';
        case 'late':
            return 'Submitted Late';
        default:
            return status;
    }
};

const deleteExercise = (exercise) => {
    if (confirm(`Are you sure you want to delete the exercise "${exercise.title}"? This action cannot be undone.`)) {
        router.delete(route('courses.modules.chapters.exercises.destroy', {
            course: props.course.id,
            module: props.module.id,
            chapter: props.chapter.id,
            exercise: exercise.id
        }));
    }
};
</script>