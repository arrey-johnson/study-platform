<template>
    <Head title="Create Exercise" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create New Exercise
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit">
                            <div class="mb-6">
                                <h3 class="text-lg font-medium text-gray-700 mb-4">Exercise Details</h3>
                                
                                <!-- Course and Chapter Selection -->
                                <div class="mb-4">
                                    <label for="course" class="block text-sm font-medium text-gray-700 mb-1">Course</label>
                                    <select 
                                        id="course" 
                                        v-model="selectedCourse" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                    >
                                        <option value="" disabled>Select a course</option>
                                        <option v-for="course in courses" :key="course.id" :value="course">
                                            {{ course.title }}
                                        </option>
                                    </select>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="module" class="block text-sm font-medium text-gray-700 mb-1">Module</label>
                                    <select 
                                        id="module" 
                                        v-model="selectedModule" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                        :disabled="!selectedCourse"
                                    >
                                        <option value="" disabled>Select a module</option>
                                        <option v-for="module in modules" :key="module.id" :value="module">
                                            {{ module.title }}
                                        </option>
                                    </select>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="chapter" class="block text-sm font-medium text-gray-700 mb-1">Chapter</label>
                                    <select 
                                        id="chapter" 
                                        v-model="form.chapter_id" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                        :disabled="!selectedModule"
                                    >
                                        <option value="" disabled>Select a chapter</option>
                                        <option v-for="chapter in chapters" :key="chapter.id" :value="chapter.id">
                                            {{ chapter.title }}
                                        </option>
                                    </select>
                                </div>
                                
                                <!-- Exercise Title -->
                                <div class="mb-4">
                                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                                    <input 
                                        id="title" 
                                        v-model="form.title" 
                                        type="text" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                    />
                                </div>
                                
                                <!-- Exercise Description -->
                                <div class="mb-4">
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                    <textarea 
                                        id="description" 
                                        v-model="form.description" 
                                        rows="4" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                    ></textarea>
                                </div>
                                
                                <!-- Exercise Type -->
                                <div class="mb-4">
                                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                    <select 
                                        id="type" 
                                        v-model="form.type" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                    >
                                        <option value="" disabled>Select a type</option>
                                        <option value="multiple_choice">Multiple Choice</option>
                                        <option value="true_false">True/False</option>
                                        <option value="written">Written</option>
                                        <option value="file_upload">File Upload</option>
                                    </select>
                                </div>
                                
                                <!-- Multiple Choice Options -->
                                <div v-if="form.type === 'multiple_choice'" class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Options</label>
                                    <div v-for="(option, index) in form.options" :key="index" class="flex items-center mb-2">
                                        <input 
                                            :id="`option-${index}`" 
                                            v-model="form.options[index]" 
                                            type="text" 
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            required
                                        />
                                        <div class="ml-2 flex items-center">
                                            <input 
                                                :id="`correct-${index}`" 
                                                type="checkbox" 
                                                :value="index" 
                                                v-model="form.correct_answers" 
                                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                            />
                                            <label :for="`correct-${index}`" class="ml-2 text-sm text-gray-700">Correct</label>
                                            
                                            <button 
                                                type="button" 
                                                @click="removeOption(index)" 
                                                class="ml-2 text-red-600 hover:text-red-800"
                                                v-if="form.options.length > 2"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <button 
                                        type="button" 
                                        @click="addOption" 
                                        class="mt-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                        </svg>
                                        Add Option
                                    </button>
                                </div>
                                
                                <!-- True/False Options -->
                                <div v-if="form.type === 'true_false'" class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Correct Answer</label>
                                    <div class="flex items-center space-x-4">
                                        <div class="flex items-center">
                                            <input 
                                                id="true" 
                                                type="radio" 
                                                name="correct_answer" 
                                                :value="true" 
                                                v-model="form.correct_answers[0]" 
                                                class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                                required
                                            />
                                            <label for="true" class="ml-2 text-sm text-gray-700">True</label>
                                        </div>
                                        <div class="flex items-center">
                                            <input 
                                                id="false" 
                                                type="radio" 
                                                name="correct_answer" 
                                                :value="false" 
                                                v-model="form.correct_answers[0]" 
                                                class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                                required
                                            />
                                            <label for="false" class="ml-2 text-sm text-gray-700">False</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Points -->
                                <div class="mb-4">
                                    <label for="points" class="block text-sm font-medium text-gray-700 mb-1">Points</label>
                                    <input 
                                        id="points" 
                                        v-model="form.points" 
                                        type="number" 
                                        min="0" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                    />
                                </div>
                                
                                <!-- Deadline -->
                                <div class="mb-4">
                                    <label for="deadline" class="block text-sm font-medium text-gray-700 mb-1">Deadline (Optional)</label>
                                    <input 
                                        id="deadline" 
                                        v-model="form.deadline" 
                                        type="datetime-local" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                </div>
                                
                                <!-- Active Status -->
                                <div class="mb-4 flex items-center">
                                    <input 
                                        id="is_active" 
                                        v-model="form.is_active" 
                                        type="checkbox" 
                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                    />
                                    <label for="is_active" class="ml-2 text-sm text-gray-700">Active</label>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-end">
                                <Link
                                    :href="route('dashboard')"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 mr-2"
                                >
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                    :disabled="form.processing"
                                >
                                    Create Exercise
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, computed, watch } from 'vue';

export default {
    components: {
        AuthenticatedLayout,
        Head,
        Link
    },
    props: {
        courses: Array
    },
    setup(props) {
        const selectedCourse = ref('');
        const selectedModule = ref('');
        
        const form = useForm({
            title: '',
            description: '',
            type: '',
            options: ['', ''],
            correct_answers: [],
            points: 10,
            deadline: '',
            is_active: true,
            chapter_id: ''
        });
        
        const modules = computed(() => {
            if (!selectedCourse.value) return [];
            return selectedCourse.value.modules || [];
        });
        
        const chapters = computed(() => {
            if (!selectedModule.value) return [];
            return selectedModule.value.chapters || [];
        });
        
        watch(selectedCourse, (newValue) => {
            selectedModule.value = '';
            form.chapter_id = '';
        });
        
        watch(selectedModule, (newValue) => {
            form.chapter_id = '';
        });
        
        const addOption = () => {
            form.options.push('');
        };
        
        const removeOption = (index) => {
            form.options.splice(index, 1);
            
            // Remove from correct answers if it was selected
            const correctIndex = form.correct_answers.indexOf(index);
            if (correctIndex !== -1) {
                form.correct_answers.splice(correctIndex, 1);
            }
            
            // Update indices for correct answers greater than the removed index
            form.correct_answers = form.correct_answers.map(i => {
                if (i > index) return i - 1;
                return i;
            });
        };
        
        const submit = () => {
            form.post(route('exercises.store'));
        };
        
        return {
            selectedCourse,
            selectedModule,
            modules,
            chapters,
            form,
            addOption,
            removeOption,
            submit
        };
    }
};
</script>
