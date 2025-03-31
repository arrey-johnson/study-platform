<template>
    <Head :title="`Submission Details - ${exercise.title}`" />
    
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
                            <span>Submission</span>
                        </div>
                        
                        <!-- Header -->
                        <div class="border-b pb-4 mb-6">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-900">Submission Details</h2>
                                    <p class="text-gray-600 mt-1">
                                        {{ exercise.title }} - Submitted by {{ submission.student.name }}
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
                        
                        <!-- Submission Info -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                            <div class="bg-gray-50 rounded-lg p-4 border">
                                <h3 class="font-medium text-gray-700 mb-2">Submission Info</h3>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Submitted:</span>
                                        <span>{{ formatDate(submission.created_at) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Status:</span>
                                        <span :class="{
                                            'text-green-600': submission.graded_at,
                                            'text-yellow-600': !submission.graded_at
                                        }">
                                            {{ submission.graded_at ? 'Graded' : 'Pending Review' }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Score:</span>
                                        <span>{{ submission.score !== null ? `${submission.score} / ${exercise.points}` : 'Not graded' }}</span>
                                    </div>
                                    <div v-if="submission.graded_at" class="flex justify-between">
                                        <span class="text-gray-500">Graded on:</span>
                                        <span>{{ formatDate(submission.graded_at) }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-gray-50 rounded-lg p-4 border md:col-span-2">
                                <h3 class="font-medium text-gray-700 mb-2">Exercise Details</h3>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Type:</span>
                                        <span>{{ formatExerciseType(exercise.type) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Points:</span>
                                        <span>{{ exercise.points }}</span>
                                    </div>
                                    <div class="mt-2">
                                        <span class="text-gray-500 block mb-1">Description:</span>
                                        <div class="bg-white p-2 rounded border text-gray-700">
                                            {{ exercise.description }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Submission Content -->
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Submission Content</h3>
                            
                            <div class="bg-white border rounded-lg p-4 mb-6">
                                <!-- Multiple Choice / True False -->
                                <div v-if="exercise.type === 'multiple_choice' || exercise.type === 'true_false'">
                                    <h4 class="font-medium mb-3">Selected Answer(s):</h4>
                                    <div v-if="submission.answers && submission.answers.length" class="space-y-2">
                                        <div v-for="(answer, index) in submission.answers" :key="index" class="flex items-start">
                                            <div class="flex-shrink-0 h-5 w-5 mt-0.5">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-gray-700">{{ answer }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <p v-else class="text-gray-500 italic">No answer selected</p>
                                </div>
                                
                                <!-- Written Answer -->
                                <div v-else-if="exercise.type === 'written'">
                                    <h4 class="font-medium mb-3">Written Response:</h4>
                                    <div class="bg-gray-50 p-4 rounded border">
                                        <p v-if="submission.answers && submission.answers.length" class="whitespace-pre-wrap">{{ submission.answers[0] }}</p>
                                        <p v-else class="text-gray-500 italic">No response provided</p>
                                    </div>
                                </div>
                                
                                <!-- File Upload -->
                                <div v-else-if="exercise.type === 'file_upload'">
                                    <h4 class="font-medium mb-3">Uploaded File:</h4>
                                    <div v-if="submission.file_path" class="flex items-center p-3 bg-gray-50 border rounded">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <a :href="'/storage/' + submission.file_path" target="_blank" class="text-blue-600 hover:underline">
                                            View Uploaded File
                                        </a>
                                    </div>
                                    <p v-else class="text-gray-500 italic">No file uploaded</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Grading Section (Admin Only) -->
                        <div v-if="$page.props.auth.user.role?.name === 'admin'" class="border-t pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Grade Submission</h3>
                            
                            <form @submit.prevent="submitGrade">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="score" class="block text-sm font-medium text-gray-700 mb-1">Score (out of {{ exercise.points }})</label>
                                        <input 
                                            id="score" 
                                            v-model="form.score" 
                                            type="number" 
                                            min="0" 
                                            :max="exercise.points" 
                                            class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                            :class="{ 'border-red-500': form.errors.score }"
                                        >
                                        <div v-if="form.errors.score" class="text-red-500 text-sm mt-1">{{ form.errors.score }}</div>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <label for="feedback" class="block text-sm font-medium text-gray-700 mb-1">Feedback (optional)</label>
                                    <textarea 
                                        id="feedback" 
                                        v-model="form.feedback" 
                                        rows="4" 
                                        class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        :class="{ 'border-red-500': form.errors.feedback }"
                                    ></textarea>
                                    <div v-if="form.errors.feedback" class="text-red-500 text-sm mt-1">{{ form.errors.feedback }}</div>
                                </div>
                                
                                <div class="mt-6 flex justify-end">
                                    <button 
                                        type="submit" 
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150"
                                        :disabled="form.processing"
                                    >
                                        <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        {{ form.processing ? 'Saving...' : 'Save Grade' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Feedback Section (Student View) -->
                        <div v-else-if="submission.graded_at" class="border-t pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Instructor Feedback</h3>
                            
                            <div class="bg-gray-50 rounded-lg p-4 border">
                                <div class="mb-4">
                                    <span class="font-medium">Your Score:</span>
                                    <span class="ml-2 text-lg">{{ submission.score }} / {{ exercise.points }}</span>
                                </div>
                                
                                <div v-if="submission.feedback">
                                    <h4 class="font-medium mb-2">Feedback:</h4>
                                    <div class="bg-white p-4 rounded border">
                                        <p class="whitespace-pre-wrap">{{ submission.feedback }}</p>
                                    </div>
                                </div>
                                <p v-else class="text-gray-500 italic">No additional feedback provided</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Link, useForm, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    submission: Object,
    exercise: Object,
    chapter: Object,
    module: Object,
    course: Object
});

const form = useForm({
    score: props.submission.score || 0,
    feedback: props.submission.feedback || ''
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

const submitGrade = () => {
    form.post(route('submissions.update', props.submission.id), {
        preserveScroll: true
    });
};
</script>
