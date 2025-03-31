<template>
    <Head title="Reports" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Reports Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Report Type Selection -->
                <div class="mb-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex flex-wrap gap-4">
                                <button 
                                    @click="activeReport = 'student'"
                                    :class="[
                                        'px-4 py-2 rounded-md font-medium text-sm transition-colors',
                                        activeReport === 'student' 
                                            ? 'bg-indigo-600 text-white' 
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                    ]"
                                >
                                    Student Performance
                                </button>
                                <button 
                                    @click="activeReport = 'course'"
                                    :class="[
                                        'px-4 py-2 rounded-md font-medium text-sm transition-colors',
                                        activeReport === 'course' 
                                            ? 'bg-indigo-600 text-white' 
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                    ]"
                                >
                                    Course Performance
                                </button>
                                <button 
                                    @click="activeReport = 'activity'"
                                    :class="[
                                        'px-4 py-2 rounded-md font-medium text-sm transition-colors',
                                        activeReport === 'activity' 
                                            ? 'bg-indigo-600 text-white' 
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                    ]"
                                >
                                    Activity Report
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Report Content -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Student Performance Report -->
                        <div v-if="activeReport === 'student'" class="space-y-6">
                            <h3 class="text-lg font-medium text-gray-900">Student Performance Report</h3>
                            
                            <!-- Filters -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="student" class="block text-sm font-medium text-gray-700 mb-1">Student</label>
                                    <select 
                                        id="student" 
                                        v-model="filters.student_id" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="">All Students</option>
                                        <option v-for="student in students" :key="student.id" :value="student.id">
                                            {{ student.name }}
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label for="course" class="block text-sm font-medium text-gray-700 mb-1">Course</label>
                                    <select 
                                        id="course" 
                                        v-model="filters.course_id" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="">All Courses</option>
                                        <option v-for="course in courses" :key="course.id" :value="course.id">
                                            {{ course.title }}
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label for="date_range" class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                                    <select 
                                        id="date_range" 
                                        v-model="filters.date_range" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="7">Last 7 days</option>
                                        <option value="30">Last 30 days</option>
                                        <option value="90">Last 3 months</option>
                                        <option value="180">Last 6 months</option>
                                        <option value="365">Last year</option>
                                        <option value="all">All time</option>
                                    </select>
                                </div>
                            </div>
                            
                            <button 
                                @click="generateStudentReport"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                Generate Report
                            </button>
                            
                            <!-- Report Results -->
                            <div v-if="studentReport.loaded" class="mt-6">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                    <div class="bg-white p-4 rounded-lg border border-gray-200">
                                        <h4 class="text-sm font-medium text-gray-500">Average Score</h4>
                                        <p class="text-2xl font-bold text-gray-900">{{ studentReport.averageScore }}%</p>
                                    </div>
                                    <div class="bg-white p-4 rounded-lg border border-gray-200">
                                        <h4 class="text-sm font-medium text-gray-500">Completed Exercises</h4>
                                        <p class="text-2xl font-bold text-gray-900">{{ studentReport.completedExercises }}</p>
                                    </div>
                                    <div class="bg-white p-4 rounded-lg border border-gray-200">
                                        <h4 class="text-sm font-medium text-gray-500">Completion Rate</h4>
                                        <p class="text-2xl font-bold text-gray-900">{{ studentReport.completionRate }}%</p>
                                    </div>
                                </div>
                                
                                <!-- Performance Chart -->
                                <div class="bg-white p-4 rounded-lg border border-gray-200 mb-6">
                                    <h4 class="text-sm font-medium text-gray-500 mb-4">Performance Over Time</h4>
                                    <div class="h-64">
                                        <!-- Chart will be rendered here -->
                                        <div class="flex items-center justify-center h-full text-gray-500">
                                            Performance chart will be displayed here
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Recent Submissions -->
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 mb-4">Recent Submissions</h4>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exercise</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                <tr v-for="(submission, index) in studentReport.recentSubmissions" :key="index">
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ submission.exercise }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ submission.date }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ submission.score }}%</td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span :class="{
                                                            'px-2 inline-flex text-xs leading-5 font-semibold rounded-full': true,
                                                            'bg-green-100 text-green-800': submission.status === 'Passed',
                                                            'bg-red-100 text-red-800': submission.status === 'Failed',
                                                            'bg-yellow-100 text-yellow-800': submission.status === 'Pending'
                                                        }">
                                                            {{ submission.status }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Course Performance Report -->
                        <div v-if="activeReport === 'course'" class="space-y-6">
                            <h3 class="text-lg font-medium text-gray-900">Course Performance Report</h3>
                            
                            <!-- Filters -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="course" class="block text-sm font-medium text-gray-700 mb-1">Course</label>
                                    <select 
                                        id="course" 
                                        v-model="filters.course_id" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="">All Courses</option>
                                        <option v-for="course in courses" :key="course.id" :value="course.id">
                                            {{ course.title }}
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label for="date_range" class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                                    <select 
                                        id="date_range" 
                                        v-model="filters.date_range" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="7">Last 7 days</option>
                                        <option value="30">Last 30 days</option>
                                        <option value="90">Last 3 months</option>
                                        <option value="180">Last 6 months</option>
                                        <option value="365">Last year</option>
                                        <option value="all">All time</option>
                                    </select>
                                </div>
                            </div>
                            
                            <button 
                                @click="generateCourseReport"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                Generate Report
                            </button>
                            
                            <!-- Report Results -->
                            <div v-if="courseReport.loaded" class="mt-6">
                                <!-- Course Stats -->
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                                    <div class="bg-white p-4 rounded-lg border border-gray-200">
                                        <h4 class="text-sm font-medium text-gray-500">Enrolled Students</h4>
                                        <p class="text-2xl font-bold text-gray-900">{{ courseReport.enrolledStudents }}</p>
                                    </div>
                                    <div class="bg-white p-4 rounded-lg border border-gray-200">
                                        <h4 class="text-sm font-medium text-gray-500">Average Completion</h4>
                                        <p class="text-2xl font-bold text-gray-900">{{ courseReport.averageCompletion }}%</p>
                                    </div>
                                    <div class="bg-white p-4 rounded-lg border border-gray-200">
                                        <h4 class="text-sm font-medium text-gray-500">Average Score</h4>
                                        <p class="text-2xl font-bold text-gray-900">{{ courseReport.averageScore }}%</p>
                                    </div>
                                    <div class="bg-white p-4 rounded-lg border border-gray-200">
                                        <h4 class="text-sm font-medium text-gray-500">Active Students</h4>
                                        <p class="text-2xl font-bold text-gray-900">{{ courseReport.activeStudents }}</p>
                                    </div>
                                </div>
                                
                                <!-- Performance by Module -->
                                <div class="bg-white p-4 rounded-lg border border-gray-200 mb-6">
                                    <h4 class="text-sm font-medium text-gray-500 mb-4">Performance by Module</h4>
                                    <div class="h-64">
                                        <!-- Chart will be rendered here -->
                                        <div class="flex items-center justify-center h-full text-gray-500">
                                            Module performance chart will be displayed here
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Top Performing Students -->
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 mb-4">Top Performing Students</h4>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completion</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Average Score</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Activity</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                <tr v-for="(student, index) in courseReport.topStudents" :key="index">
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="flex items-center">
                                                            <div class="flex-shrink-0 h-10 w-10">
                                                                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-500 font-bold">
                                                                    {{ student.name.charAt(0) }}
                                                                </div>
                                                            </div>
                                                            <div class="ml-4">
                                                                <div class="text-sm font-medium text-gray-900">{{ student.name }}</div>
                                                                <div class="text-sm text-gray-500">{{ student.email }}</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ student.completion }}%</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ student.averageScore }}%</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ student.lastActivity }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Activity Report -->
                        <div v-if="activeReport === 'activity'" class="space-y-6">
                            <h3 class="text-lg font-medium text-gray-900">Activity Report</h3>
                            
                            <!-- Filters -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="activity_type" class="block text-sm font-medium text-gray-700 mb-1">Activity Type</label>
                                    <select 
                                        id="activity_type" 
                                        v-model="filters.activity_type" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="">All Activities</option>
                                        <option value="login">Logins</option>
                                        <option value="submission">Submissions</option>
                                        <option value="enrollment">Enrollments</option>
                                        <option value="content_view">Content Views</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="date_range" class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                                    <select 
                                        id="date_range" 
                                        v-model="filters.date_range" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="7">Last 7 days</option>
                                        <option value="30">Last 30 days</option>
                                        <option value="90">Last 3 months</option>
                                        <option value="180">Last 6 months</option>
                                        <option value="365">Last year</option>
                                        <option value="all">All time</option>
                                    </select>
                                </div>
                            </div>
                            
                            <button 
                                @click="generateActivityReport"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                Generate Report
                            </button>
                            
                            <!-- Report Results -->
                            <div v-if="activityReport.loaded" class="mt-6">
                                <!-- Activity Stats -->
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                                    <div class="bg-white p-4 rounded-lg border border-gray-200">
                                        <h4 class="text-sm font-medium text-gray-500">Total Logins</h4>
                                        <p class="text-2xl font-bold text-gray-900">{{ activityReport.totalLogins }}</p>
                                    </div>
                                    <div class="bg-white p-4 rounded-lg border border-gray-200">
                                        <h4 class="text-sm font-medium text-gray-500">Total Submissions</h4>
                                        <p class="text-2xl font-bold text-gray-900">{{ activityReport.totalSubmissions }}</p>
                                    </div>
                                    <div class="bg-white p-4 rounded-lg border border-gray-200">
                                        <h4 class="text-sm font-medium text-gray-500">New Enrollments</h4>
                                        <p class="text-2xl font-bold text-gray-900">{{ activityReport.newEnrollments }}</p>
                                    </div>
                                    <div class="bg-white p-4 rounded-lg border border-gray-200">
                                        <h4 class="text-sm font-medium text-gray-500">Content Views</h4>
                                        <p class="text-2xl font-bold text-gray-900">{{ activityReport.contentViews }}</p>
                                    </div>
                                </div>
                                
                                <!-- Activity Over Time -->
                                <div class="bg-white p-4 rounded-lg border border-gray-200 mb-6">
                                    <h4 class="text-sm font-medium text-gray-500 mb-4">Activity Over Time</h4>
                                    <div class="h-64">
                                        <!-- Chart will be rendered here -->
                                        <div class="flex items-center justify-center h-full text-gray-500">
                                            Activity chart will be displayed here
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Recent Activity -->
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 mb-4">Recent Activity</h4>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activity</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date/Time</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                <tr v-for="(activity, index) in activityReport.recentActivity" :key="index">
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="flex items-center">
                                                            <div class="flex-shrink-0 h-8 w-8">
                                                                <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-500 font-bold">
                                                                    {{ activity.user.charAt(0) }}
                                                                </div>
                                                            </div>
                                                            <div class="ml-3 text-sm font-medium text-gray-900">
                                                                {{ activity.user }}
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span :class="{
                                                            'px-2 inline-flex text-xs leading-5 font-semibold rounded-full': true,
                                                            'bg-blue-100 text-blue-800': activity.type === 'login',
                                                            'bg-green-100 text-green-800': activity.type === 'submission',
                                                            'bg-purple-100 text-purple-800': activity.type === 'enrollment',
                                                            'bg-yellow-100 text-yellow-800': activity.type === 'content_view'
                                                        }">
                                                            {{ activity.type }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ activity.details }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ activity.datetime }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
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

<script>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, reactive } from 'vue';
import axios from 'axios';

export default {
    components: {
        AuthenticatedLayout,
        Head
    },
    props: {
        students: Array,
        courses: Array
    },
    setup() {
        const activeReport = ref('student');
        
        const filters = reactive({
            student_id: '',
            course_id: '',
            date_range: '30',
            activity_type: ''
        });
        
        const studentReport = reactive({
            loaded: false,
            averageScore: 0,
            completedExercises: 0,
            completionRate: 0,
            recentSubmissions: []
        });
        
        const courseReport = reactive({
            loaded: false,
            enrolledStudents: 0,
            averageCompletion: 0,
            averageScore: 0,
            activeStudents: 0,
            topStudents: []
        });
        
        const activityReport = reactive({
            loaded: false,
            totalLogins: 0,
            totalSubmissions: 0,
            newEnrollments: 0,
            contentViews: 0,
            recentActivity: []
        });
        
        const generateStudentReport = async () => {
            try {
                const response = await axios.get(route('reports.student'), {
                    params: {
                        student_id: filters.student_id,
                        course_id: filters.course_id,
                        date_range: filters.date_range
                    }
                });
                
                Object.assign(studentReport, response.data, { loaded: true });
            } catch (error) {
                console.error('Error generating student report:', error);
            }
        };
        
        const generateCourseReport = async () => {
            try {
                const response = await axios.get(route('reports.course'), {
                    params: {
                        course_id: filters.course_id,
                        date_range: filters.date_range
                    }
                });
                
                Object.assign(courseReport, response.data, { loaded: true });
            } catch (error) {
                console.error('Error generating course report:', error);
            }
        };
        
        const generateActivityReport = async () => {
            try {
                const response = await axios.get(route('reports.activity'), {
                    params: {
                        activity_type: filters.activity_type,
                        date_range: filters.date_range
                    }
                });
                
                Object.assign(activityReport, response.data, { loaded: true });
            } catch (error) {
                console.error('Error generating activity report:', error);
            }
        };
        
        return {
            activeReport,
            filters,
            studentReport,
            courseReport,
            activityReport,
            generateStudentReport,
            generateCourseReport,
            generateActivityReport
        };
    }
};
</script>
