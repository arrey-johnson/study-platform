<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    courses: Object
});

const isAdmin = () => {
    return window.Laravel && window.Laravel.isAdmin;
};
</script>

<template>
    <Head title="Courses" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Courses
                </h2>
                <Link
                    v-if="$page.props.auth.user.role?.name === 'admin'"
                    :href="route('courses.create')"
                    class="inline-flex items-center rounded-md bg-orange-600 px-4 py-2 text-sm font-semibold text-white hover:bg-orange-700"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    New Course
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            <div v-for="course in courses.data" :key="course.id" class="overflow-hidden rounded-lg bg-white shadow">
                                <div class="p-6">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-medium text-gray-900">{{ course.title }}</h3>
                                        <span v-if="course.is_active" class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                            Active
                                        </span>
                                    </div>
                                    <p class="mt-2 text-sm text-gray-500">{{ course.description }}</p>
                                    <div class="mt-4 flex items-center justify-between">
                                        <div class="text-sm text-gray-500">
                                            {{ course.enrollments_count }} students enrolled
                                        </div>
                                        <div class="flex space-x-2">
                                            <!-- Admin-only enrollment management buttons -->
                                            <Link
                                                v-if="$page.props.auth.user.role?.name === 'admin'"
                                                :href="route('enrollments.create')"
                                                class="text-sm font-medium bg-orange-600 text-white px-3 py-1 rounded hover:bg-orange-700"
                                            >
                                                Manage Enrollments
                                            </Link>
                                            
                                            <!-- Student view - no self-enrollment button -->
                                            <span 
                                                v-if="$page.props.auth.user.role?.name === 'student' && course.is_enrolled" 
                                                class="text-sm font-medium bg-green-100 text-green-800 px-3 py-1 rounded"
                                            >
                                                Enrolled
                                            </span>
                                            
                                            <Link
                                                :href="route('courses.show', course.id)"
                                                class="text-sm font-medium text-orange-600 hover:text-orange-900"
                                            >
                                                View Course
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div v-if="courses.links.length > 3" class="mt-6">
                            <div class="flex items-center justify-between">
                                <div v-if="courses.current_page > 1">
                                    <Link
                                        :href="courses.prev_page_url"
                                        class="inline-flex items-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                                    >
                                        Previous
                                    </Link>
                                </div>
                                <div v-if="courses.current_page < courses.last_page">
                                    <Link
                                        :href="courses.next_page_url"
                                        class="inline-flex items-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                                    >
                                        Next
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>