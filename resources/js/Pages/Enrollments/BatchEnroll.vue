<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Batch Enroll Students
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="mb-6">
                            <Link :href="route('enrollments.index')" class="text-purple-600 hover:text-purple-900">
                                ← Back to Enrollments
                            </Link>
                        </div>
                        
                        <form @submit.prevent="submit">
                            <div class="mb-6">
                                <h3 class="text-lg font-medium text-gray-900">Enroll Multiple Students in a Course</h3>
                                <p class="mt-1 text-sm text-gray-600">
                                    Select a course and multiple students to create batch enrollments.
                                </p>
                            </div>

                            <div v-if="form.errors.length > 0" class="mb-4 p-4 bg-red-50 rounded border border-red-200">
                                <div class="font-medium text-red-800">Whoops! Something went wrong.</div>
                                <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                                    <li v-for="(error, index) in form.errors" :key="index">{{ error }}</li>
                                </ul>
                            </div>

                            <div class="mb-6">
                                <label for="course" class="block text-sm font-medium text-gray-700">Course</label>
                                <select
                                    id="course"
                                    v-model="form.course_id"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm rounded-md"
                                    required
                                >
                                    <option value="" disabled>Select a course</option>
                                    <option v-for="course in courses" :key="course.id" :value="course.id">
                                        {{ course.title }}
                                    </option>
                                </select>
                                <p v-if="selectedCourse" class="mt-2 text-sm text-gray-500">
                                    {{ selectedCourse.description }}
                                </p>
                            </div>

                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Select Students</label>
                                
                                <div class="mb-3">
                                    <input 
                                        type="text" 
                                        v-model="searchTerm" 
                                        placeholder="Search students..." 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500"
                                    />
                                </div>
                                
                                <div class="mb-2 flex items-center">
                                    <button 
                                        type="button" 
                                        @click="selectAll" 
                                        class="text-sm text-purple-600 hover:text-purple-900"
                                    >
                                        Select All
                                    </button>
                                    <span class="mx-2 text-gray-500">|</span>
                                    <button 
                                        type="button" 
                                        @click="deselectAll" 
                                        class="text-sm text-purple-600 hover:text-purple-900"
                                    >
                                        Deselect All
                                    </button>
                                    <span class="ml-auto text-sm text-gray-500">
                                        {{ form.user_ids.length }} students selected
                                    </span>
                                </div>
                                
                                <div class="border border-gray-300 rounded-md max-h-80 overflow-y-auto p-2">
                                    <div v-if="filteredStudents.length === 0" class="p-4 text-center text-gray-500">
                                        No students match your search criteria
                                    </div>
                                    <div v-for="student in filteredStudents" :key="student.id" class="p-2 hover:bg-gray-50 rounded-md">
                                        <label class="flex items-center space-x-3 cursor-pointer">
                                            <input
                                                type="checkbox"
                                                :value="student.id"
                                                v-model="form.user_ids"
                                                class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                                            />
                                            <span class="text-sm font-medium text-gray-900">{{ student.name }}</span>
                                            <span class="text-sm text-gray-500">{{ student.email }}</span>
                                        </label>
                                    </div>
                                </div>
                                <p class="mt-2 text-sm text-red-600" v-if="form.errors.user_ids">
                                    {{ form.errors.user_ids }}
                                </p>
                            </div>

                            <div class="mb-6">
                                <div class="flex items-center">
                                    <input
                                        id="send_notification"
                                        v-model="form.send_notification"
                                        type="checkbox"
                                        class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                                    />
                                    <label for="send_notification" class="ml-2 block text-sm text-gray-900">
                                        Send notification emails to students
                                    </label>
                                </div>
                            </div>

                            <div class="flex items-center justify-end mt-6">
                                <Link
                                    :href="route('enrollments.index')"
                                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 mr-3"
                                >
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                    :disabled="form.processing || form.user_ids.length === 0"
                                >
                                    Enroll Students
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
import { computed, ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

export default {
    components: {
        AuthenticatedLayout,
        Link
    },
    
    props: {
        courses: Array,
        students: Array
    },
    
    setup(props) {
        const form = useForm({
            course_id: '',
            user_ids: [],
            send_notification: true
        });
        
        const searchTerm = ref('');
        
        const selectedCourse = computed(() => {
            if (!form.course_id) return null;
            return props.courses.find(course => course.id === form.course_id);
        });
        
        const filteredStudents = computed(() => {
            if (!searchTerm.value) return props.students;
            
            const term = searchTerm.value.toLowerCase();
            return props.students.filter(student => 
                student.name.toLowerCase().includes(term) || 
                student.email.toLowerCase().includes(term)
            );
        });
        
        const selectAll = () => {
            form.user_ids = filteredStudents.value.map(student => student.id);
        };
        
        const deselectAll = () => {
            form.user_ids = [];
        };
        
        const submit = () => {
            if (form.user_ids.length === 0) {
                form.errors.user_ids = 'Please select at least one student to enroll';
                return;
            }
            
            form.post(route('enrollments.batch'), {
                onSuccess: () => {
                    form.reset();
                    searchTerm.value = '';
                }
            });
        };
        
        return {
            form,
            searchTerm,
            selectedCourse,
            filteredStudents,
            selectAll,
            deselectAll,
            submit
        };
    }
};
</script>
