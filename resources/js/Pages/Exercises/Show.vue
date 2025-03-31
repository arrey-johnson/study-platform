<template>
    <Head :title="exercise.title" />
    
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ exercise.title }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ chapter.title }} - {{ module.title }} - {{ course.title }}
                    </p>
                </div>
                <div class="flex space-x-4">
                    <Link
                        :href="route('courses.modules.chapters.exercises.index', {
                            course: course.id,
                            module: module.id,
                            chapter: chapter.id
                        })"
                        class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300"
                    >
                        Back to Exercises
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
                        <!-- Exercise Details Card -->
                        <div class="mb-8 p-6 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="flex items-center">
                                        <h3 class="text-xl font-medium text-gray-900">{{ exercise.title }}</h3>
                                        <span v-if="exercise.is_active" class="ml-2 px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Active</span>
                                        <span v-else class="ml-2 px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">Inactive</span>
                                    </div>
                                    <p class="mt-3 text-gray-600">{{ exercise.description }}</p>
                                    <div class="mt-4 flex flex-wrap items-center gap-4 text-sm text-gray-500">
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
                                    </div>
                                </div>
                                <div v-if="$page.props.auth.user.role?.name === 'admin'" class="flex space-x-3">
                                    <Link 
                                        :href="route('exercises.submissions.index', exercise.id)" 
                                        class="inline-flex items-center px-4 py-2 bg-blue-100 border border-blue-300 rounded-md font-semibold text-xs text-blue-700 uppercase tracking-widest shadow-sm hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        View All Submissions
                                    </Link>
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
                                        @click="deleteExercise"
                                        class="inline-flex items-center px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm transition-colors"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Student Submission Section -->
                        <div v-if="!$page.props.auth.user.role?.name === 'admin'" class="mt-8">
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Your Answer</h4>
                            
                            <!-- If already submitted -->
                            <div v-if="exercise.submission" class="p-6 border border-gray-200 rounded-lg bg-gray-50">
                                <div class="mb-4">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="font-medium">Submitted</span>
                                        <span class="ml-2 text-sm text-gray-500">{{ formatDate(exercise.submission.created_at) }}</span>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <h5 class="text-sm font-medium text-gray-700 mb-2">Your Answer:</h5>
                                    
                                    <div v-if="exercise.type === 'multiple_choice'">
                                        <div v-for="(option, index) in exercise.options" :key="index" class="mt-2">
                                            <label class="inline-flex items-center">
                                                <input
                                                    type="checkbox"
                                                    :checked="exercise.submission.answers.includes(index.toString())"
                                                    class="form-checkbox"
                                                    disabled
                                                />
                                                <span class="ml-2" :class="{
                                                    'text-green-600 font-medium': exercise.correct_answers.includes(index.toString()),
                                                }">{{ option }}</span>
                                                <svg v-if="exercise.correct_answers.includes(index.toString())" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </label>
                                        </div>
                                    </div>

                                    <div v-else-if="exercise.type === 'true_false'">
                                        <div class="flex space-x-4">
                                            <label class="inline-flex items-center">
                                                <input
                                                    type="radio"
                                                    :checked="exercise.submission.answers[0] === 'true'"
                                                    class="form-radio"
                                                    disabled
                                                />
                                                <span class="ml-2" :class="{'text-green-600 font-medium': exercise.correct_answers[0] === 'true'}">True</span>
                                                <svg v-if="exercise.correct_answers[0] === 'true'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input
                                                    type="radio"
                                                    :checked="exercise.submission.answers[0] === 'false'"
                                                    class="form-radio"
                                                    disabled
                                                />
                                                <span class="ml-2" :class="{'text-green-600 font-medium': exercise.correct_answers[0] === 'false'}">False</span>
                                                <svg v-if="exercise.correct_answers[0] === 'false'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </label>
                                        </div>
                                    </div>

                                    <div v-else-if="exercise.type === 'written'">
                                        <div class="p-4 bg-white border border-gray-200 rounded">
                                            <p class="whitespace-pre-wrap">{{ exercise.submission.answers[0] }}</p>
                                        </div>
                                    </div>

                                    <div v-else-if="exercise.type === 'file_upload'">
                                        <div class="flex items-center p-3 bg-white border border-gray-200 rounded">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <a :href="'/storage/' + exercise.submission.file_path" target="_blank" class="text-blue-600 hover:underline">
                                                View Uploaded File
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-6 border-t border-gray-200 pt-4">
                                    <div class="flex items-center mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        <span class="font-medium">Score: </span>
                                        <span class="ml-1">{{ exercise.submission.score }} / {{ exercise.points }}</span>
                                    </div>
                                    
                                    <div v-if="exercise.submission.feedback" class="mt-3 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                        <div class="flex items-start">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <div>
                                                <p class="font-medium text-blue-800 mb-1">Feedback from instructor:</p>
                                                <p class="text-blue-700">{{ exercise.submission.feedback }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- If not submitted yet -->
                            <div v-else>
                                <form @submit.prevent="submit" class="p-6 border border-gray-200 rounded-lg">
                                    <div v-if="exercise.type === 'multiple_choice'" class="space-y-3">
                                        <h5 class="font-medium text-gray-700 mb-2">Select all correct answers:</h5>
                                        <div v-for="(option, index) in exercise.options" :key="index" class="mt-2">
                                            <label class="inline-flex items-center">
                                                <input
                                                    type="checkbox"
                                                    v-model="form.answers"
                                                    :value="index.toString()"
                                                    class="form-checkbox h-5 w-5 text-blue-600"
                                                />
                                                <span class="ml-2">{{ option }}</span>
                                            </label>
                                        </div>
                                        <div v-if="form.errors.answers" class="text-red-500 text-sm mt-1">
                                            {{ form.errors.answers }}
                                        </div>
                                    </div>

                                    <div v-else-if="exercise.type === 'true_false'" class="space-y-3">
                                        <h5 class="font-medium text-gray-700 mb-2">Select the correct answer:</h5>
                                        <div class="flex space-x-6">
                                            <label class="inline-flex items-center">
                                                <input
                                                    type="radio"
                                                    v-model="form.answers"
                                                    value="true"
                                                    class="form-radio h-5 w-5 text-blue-600"
                                                    required
                                                />
                                                <span class="ml-2">True</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input
                                                    type="radio"
                                                    v-model="form.answers"
                                                    value="false"
                                                    class="form-radio h-5 w-5 text-blue-600"
                                                    required
                                                />
                                                <span class="ml-2">False</span>
                                            </label>
                                        </div>
                                        <div v-if="form.errors.answers" class="text-red-500 text-sm mt-1">
                                            {{ form.errors.answers }}
                                        </div>
                                    </div>

                                    <div v-else-if="exercise.type === 'written'" class="space-y-3">
                                        <h5 class="font-medium text-gray-700 mb-2">Your answer:</h5>
                                        <textarea
                                            v-model="form.answers"
                                            rows="6"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                            required
                                        ></textarea>
                                        <div v-if="form.errors.answers" class="text-red-500 text-sm mt-1">
                                            {{ form.errors.answers }}
                                        </div>
                                    </div>

                                    <div v-else-if="exercise.type === 'file_upload'" class="space-y-3">
                                        <h5 class="font-medium text-gray-700 mb-2">Upload your file:</h5>
                                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                            <div class="space-y-1 text-center">
                                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                <div class="flex text-sm text-gray-600">
                                                    <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                        <span>Upload a file</span>
                                                        <input 
                                                            id="file-upload" 
                                                            name="file-upload" 
                                                            type="file" 
                                                            class="sr-only"
                                                            @change="handleFileUpload"
                                                            required
                                                        />
                                                    </label>
                                                    <p class="pl-1">or drag and drop</p>
                                                </div>
                                                <p class="text-xs text-gray-500">
                                                    PDF, DOC, DOCX, ZIP up to 10MB
                                                </p>
                                                <p v-if="form.file" class="text-sm text-green-600 mt-2">
                                                    Selected: {{ form.file.name }}
                                                </p>
                                            </div>
                                        </div>
                                        <div v-if="form.errors.file" class="text-red-500 text-sm mt-1">
                                            {{ form.errors.file }}
                                        </div>
                                    </div>

                                    <div class="mt-6">
                                        <button 
                                            type="submit" 
                                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150"
                                            :class="{ 'opacity-25': form.processing }" 
                                            :disabled="form.processing"
                                        >
                                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Submit Answer
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Admin Submissions Section -->
                        <div v-if="$page.props.auth.user.role?.name === 'admin'" class="mt-8">
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Student Submissions</h4>
                            
                            <div v-if="!submissions || submissions.length === 0" class="text-center py-8 border border-gray-200 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <p class="text-gray-500">No submissions yet</p>
                            </div>
                            
                            <div v-else class="space-y-4">
                                <div v-for="submission in submissions" :key="submission.id" class="border rounded-lg p-4 hover:bg-gray-50">
                                    <div class="flex justify-between">
                                        <div>
                                            <h5 class="font-medium">{{ submission.student.name }}</h5>
                                            <p class="text-sm text-gray-500">Submitted: {{ formatDate(submission.created_at) }}</p>
                                            <div class="mt-2">
                                                <span class="text-sm">Score: {{ submission.score }} / {{ exercise.points }}</span>
                                            </div>
                                        </div>
                                        <div>
                                            <Link :href="route('submissions.show', submission.id)" class="text-blue-600 hover:text-blue-800">
                                                View Details
                                            </Link>
                                        </div>
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
import { Link, useForm, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    course: Object,
    module: Object,
    chapter: Object,
    exercise: Object,
    submissions: Array
});

const form = useForm({
    answers: props.exercise.type === 'multiple_choice' ? [] : '',
    file: null
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

const handleFileUpload = (event) => {
    form.file = event.target.files[0];
};

const submit = () => {
    // For multiple choice, ensure answers is an array
    if (props.exercise.type === 'multiple_choice' && !Array.isArray(form.answers)) {
        form.answers = [form.answers];
    }
    
    // For true/false, ensure answers is an array with a single value
    if (props.exercise.type === 'true_false' && !Array.isArray(form.answers)) {
        form.answers = [form.answers];
    }
    
    // For written, ensure answers is an array with a single value
    if (props.exercise.type === 'written' && !Array.isArray(form.answers)) {
        form.answers = [form.answers];
    }
    
    // Create FormData for file uploads
    if (props.exercise.type === 'file_upload' && form.file) {
        const formData = new FormData();
        formData.append('file', form.file);
        
        router.post(route('courses.modules.chapters.exercises.submit', {
            course: props.course.id,
            module: props.module.id,
            chapter: props.chapter.id,
            exercise: props.exercise.id
        }), formData);
        
        return;
    }
    
    // For other types
    form.post(route('courses.modules.chapters.exercises.submit', {
        course: props.course.id,
        module: props.module.id,
        chapter: props.chapter.id,
        exercise: props.exercise.id
    }));
};

const deleteExercise = () => {
    if (confirm(`Are you sure you want to delete the exercise "${props.exercise.title}"? This action cannot be undone.`)) {
        router.delete(route('courses.modules.chapters.exercises.destroy', {
            course: props.course.id,
            module: props.module.id,
            chapter: props.chapter.id,
            exercise: props.exercise.id
        }), {
            onSuccess: () => {
                router.visit(route('courses.modules.chapters.exercises.index', {
                    course: props.course.id,
                    module: props.module.id,
                    chapter: props.chapter.id
                }));
            }
        });
    }
};
</script>