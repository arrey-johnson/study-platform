<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination/Pagination.vue';
import { computed } from 'vue';

const props = defineProps({
    enrollments: Object,
    stats: Object,
    popularCourses: Array
});

const isAdmin = computed(() => {
    return window.Laravel && window.Laravel.isAdmin;
});
</script>

<template>
    <Head title="Enrollments" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Course Enrollments
                </h2>
                <div v-if="isAdmin" class="flex space-x-4">
                    <Link
                        :href="route('enrollments.create')"
                        class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        Enroll Student
                    </Link>
                    <Link
                        :href="route('enrollments.batch.form')"
                        class="inline-flex items-center px-4 py-2 bg-white border border-purple-600 rounded-md font-semibold text-xs text-purple-600 uppercase tracking-widest hover:bg-purple-50 focus:bg-purple-50 active:bg-purple-100 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        Batch Enrollment
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-sm font-medium text-gray-500">Total Enrollments</div>
                            <div class="mt-1 text-3xl font-semibold text-gray-900">{{ stats.totalEnrollments }}</div>
                        </div>
                    </div>
                    
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-sm font-medium text-gray-500">Total Students</div>
                            <div class="mt-1 text-3xl font-semibold text-gray-900">{{ stats.totalStudents }}</div>
                        </div>
                    </div>
                    
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-sm font-medium text-gray-500">Total Courses</div>
                            <div class="mt-1 text-3xl font-semibold text-gray-900">{{ stats.totalCourses }}</div>
                        </div>
                    </div>
                </div>
                
                <!-- Main Content -->
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                    <!-- Enrollments Table -->
                    <div class="lg:col-span-3 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">All Enrollments</h3>
                            
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                        <tr>
                                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enrollment Date</th>
                                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="enrollment in enrollments.data" :key="enrollment.id">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ enrollment.student.name }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ enrollment.course.title }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ enrollment.created_at }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <Link 
                                                    v-if="enrollment.course.id" 
                                                    :href="route('courses.show', enrollment.course.id)" 
                                                    class="text-blue-600 hover:text-blue-900 mr-3"
                                                >
                                                    View Course
                                                </Link>
                                                <Link 
                                                    v-if="isAdmin" 
                                                    :href="route('enrollments.destroy', enrollment.id)" 
                                                    method="delete"
                                                    as="button"
                                                    type="button"
                                                    class="text-red-600 hover:text-red-900"
                                                    onclick="return confirm('Are you sure you want to remove this enrollment?')"
                                                >
                                                    Remove
                                                </Link>
                                            </td>
                                        </tr>
                                        <tr v-if="enrollments.data.length === 0">
                                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                                No enrollments found
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <Pagination :links="enrollments.links" class="mt-6" />
                        </div>
                    </div>
                    
                    <!-- Popular Courses -->
                    <div class="lg:col-span-1 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Popular Courses</h3>
                            
                            <div class="space-y-4">
                                <div v-for="course in popularCourses" :key="course.id" class="border-b border-gray-100 pb-3 last:border-b-0 last:pb-0">
                                    <div class="font-medium text-gray-900">{{ course.title }}</div>
                                    <div class="text-sm text-gray-500 mt-1">
                                        {{ course.enrollment_count }} enrollments
                                    </div>
                                    <Link 
                                        :href="route('courses.show', course.id)" 
                                        class="text-sm text-blue-600 hover:text-blue-800 mt-1 inline-block"
                                    >
                                        View course →
                                    </Link>
                                </div>
                                
                                <div v-if="popularCourses.length === 0" class="text-center py-4">
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
