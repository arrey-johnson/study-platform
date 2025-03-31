<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { Chart } from 'chart.js/auto';

const props = defineProps({
    student: Object,
    enrolledCourses: Array,
    recentSubmissions: Array,
    activityStats: Object,
    weeklyActivity: Array,
    availableCourses: Array
});

const activityChart = ref(null);

const enrollForm = useForm({
    course_id: ''
});

const handleEnroll = () => {
    enrollForm.post(route('students.enroll', props.student.id), {
        onSuccess: () => {
            enrollForm.reset();
        }
    });
};

const handleUnenroll = (courseId) => {
    if (confirm('Are you sure you want to unenroll this student from this course?')) {
        useForm({
            course_id: courseId
        }).delete(route('students.unenroll', props.student.id));
    }
};

onMounted(() => {
    if (activityChart.value && props.weeklyActivity) {
        new Chart(activityChart.value, {
            type: 'bar',
            data: {
                labels: props.weeklyActivity.map(item => item.week),
                datasets: [{
                    label: 'Submissions',
                    data: props.weeklyActivity.map(item => item.submissions),
                    backgroundColor: 'rgba(249, 115, 22, 0.7)',
                    borderColor: 'rgb(249, 115, 22)',
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleFont: {
                            size: 13
                        },
                        bodyFont: {
                            size: 12
                        },
                        padding: 10,
                        cornerRadius: 8
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    }
});
</script>

<template>
    <Head :title="`Student: ${student.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Student: {{ student.name }}
                </h2>
                <div class="flex space-x-2">
                    <Link 
                        :href="route('students.edit', student.id)" 
                        class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-md transition-colors"
                    >
                        Edit Student
                    </Link>
                    <Link 
                        :href="route('students.index')" 
                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md transition-colors"
                    >
                        Back to List
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Student Info Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Student Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p><span class="font-medium">Name:</span> {{ student.name }}</p>
                                <p><span class="font-medium">Email:</span> {{ student.email }}</p>
                            </div>
                            <div>
                                <p><span class="font-medium">Joined:</span> {{ student.created_at }}</p>
                                <p>
                                    <span class="font-medium">Contact:</span> 
                                    <a :href="`mailto:${student.email}`" class="text-orange-600 hover:text-orange-800">
                                        Send Email
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activity Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-sm text-gray-600 mb-1">Total Submissions</div>
                            <div class="text-3xl font-bold">{{ activityStats.submissionsCount }}</div>
                        </div>
                    </div>
                    
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-sm text-gray-600 mb-1">Completed Exercises</div>
                            <div class="text-3xl font-bold">{{ activityStats.completedCount }}</div>
                        </div>
                    </div>
                    
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-sm text-gray-600 mb-1">Average Score</div>
                            <div class="text-3xl font-bold">{{ activityStats.averageScore }}</div>
                        </div>
                    </div>
                    
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-sm text-gray-600 mb-1">Enrolled Courses</div>
                            <div class="text-3xl font-bold">{{ activityStats.enrollmentsCount }}</div>
                        </div>
                    </div>
                </div>

                <!-- Weekly Activity Chart -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Weekly Activity</h3>
                        <div class="h-64">
                            <canvas ref="activityChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Enrolled Courses -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Enrolled Courses</h3>
                            <div class="space-y-4">
                                <div v-for="course in enrolledCourses" :key="course.id" class="border-b pb-4 last:border-b-0">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <Link :href="route('courses.show', course.id)" class="font-medium text-orange-600 hover:text-orange-800">
                                                {{ course.title }}
                                            </Link>
                                            <div class="text-sm text-gray-600 mt-1">
                                                Enrolled: {{ course.enrolled_at }}
                                            </div>
                                            <div class="mt-2">
                                                <div class="text-xs text-gray-500 mb-1">
                                                    Progress: {{ course.completed_exercises }} of {{ course.total_exercises }} exercises completed
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                                    <div class="bg-orange-500 h-2.5 rounded-full" :style="{ width: `${course.progress}%` }"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <button 
                                            @click="handleUnenroll(course.id)" 
                                            class="px-3 py-1 bg-red-100 text-red-700 rounded-md text-xs font-medium hover:bg-red-200"
                                        >
                                            Unenroll
                                        </button>
                                    </div>
                                </div>
                                <div v-if="enrolledCourses.length === 0" class="text-gray-500 text-center py-4">
                                    No enrolled courses
                                </div>
                            </div>

                            <!-- Enroll in New Course -->
                            <div class="mt-6 pt-6 border-t" v-if="availableCourses.length > 0">
                                <h4 class="font-medium mb-2">Enroll in New Course</h4>
                                <form @submit.prevent="handleEnroll" class="flex space-x-2">
                                    <select 
                                        v-model="enrollForm.course_id" 
                                        class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                        required
                                    >
                                        <option value="">Select a course...</option>
                                        <option v-for="course in availableCourses" :key="course.id" :value="course.id">
                                            {{ course.title }}
                                        </option>
                                    </select>
                                    <button 
                                        type="submit" 
                                        class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-md transition-colors"
                                        :disabled="enrollForm.processing"
                                    >
                                        Enroll
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Submissions -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Recent Submissions</h3>
                            <div class="space-y-4">
                                <div v-for="submission in recentSubmissions" :key="submission.id" class="border-b pb-4 last:border-b-0">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <div class="font-medium">{{ submission.exercise.title }}</div>
                                            <div class="text-sm text-gray-600">
                                                Course: {{ submission.course.title }}
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                Submitted: {{ submission.submitted_at }}
                                            </div>
                                        </div>
                                        <div :class="{
                                            'px-2 py-1 rounded text-xs font-medium': true,
                                            'bg-green-100 text-green-800': submission.status === 'completed',
                                            'bg-yellow-100 text-yellow-800': submission.status === 'pending',
                                            'bg-red-100 text-red-800': submission.status === 'failed'
                                        }">
                                            {{ submission.status }}
                                            <span v-if="submission.score !== null">
                                                ({{ submission.score }}%)
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="recentSubmissions.length === 0" class="text-gray-500 text-center py-4">
                                    No recent submissions
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
