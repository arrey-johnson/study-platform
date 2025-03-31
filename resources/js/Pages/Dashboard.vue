<script>
import { Link, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Chart from 'chart.js/auto';

export default {
    components: {
        AuthenticatedLayout,
        Link,
        Head
    },
    props: {
        adminStats: Object,
        enrollmentChart: Array,
        completionChart: Array,
        recentEnrollments: Array,
        inactiveStudents: Array,
        studentProgress: Object,
        courses: Array,
        upcomingDeadlines: Array,
        coursePerformance: Array,
        studentEngagement: Array,
        recentCompletions: Array
    },
    data() {
        return {
            enrollmentChartInstance: null,
            activityChartInstance: null,
            engagementChartInstance: null,
            completionChartInstance: null
        };
    },
    computed: {
        sortedCourses() {
            if (!this.courses) return [];
            return [...this.courses].sort((a, b) => b.progress - a.progress);
        }
    },
    mounted() {
        this.$nextTick(() => {
            console.log('Auth user:', this.$page.props.auth.user);
            console.log('Auth user role:', this.$page.props.auth.user?.role);
            
            // Initialize charts based on user role
            if (this.$page.props.auth.user?.role?.name === 'admin') {
                // Wait a moment for the DOM to be fully rendered
                setTimeout(() => {
                    this.initAdminCharts();
                }, 100);
            }
        });
    },
    methods: {
        formatDate(date) {
            if (!date) return '';
            const options = { year: 'numeric', month: 'short', day: 'numeric' };
            return new Date(date).toLocaleDateString(undefined, options);
        },
        
        // Initialize all admin charts at once
        initAdminCharts() {
            console.log('Initializing admin charts');
            console.log('Enrollment Chart Data:', this.enrollmentChart);
            console.log('Completion Chart Data:', this.completionChart);
            
            // Enrollment Chart
            if (this.$refs.enrollmentChart && this.enrollmentChart && this.enrollmentChart.length > 0 && 
                this.enrollmentChart.some(item => item.count > 0)) {
                const ctx = this.$refs.enrollmentChart.getContext('2d');
                this.enrollmentChartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: this.enrollmentChart.map(item => item.month),
                        datasets: [{
                            label: 'New Enrollments',
                            data: this.enrollmentChart.map(item => item.count),
                            backgroundColor: 'rgba(234, 88, 12, 0.2)',
                            borderColor: 'rgba(234, 88, 12, 1)',
                            borderWidth: 2,
                            tension: 0.3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
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
            } else {
                console.warn('Cannot initialize enrollment chart - no data available');
                if (this.$refs.enrollmentChart) {
                    this.showNoDataMessage(this.$refs.enrollmentChart, 'No enrollment data available yet');
                }
            }
            
            // Completion Chart
            if (this.$refs.completionChart && this.completionChart && this.completionChart.length > 0 && 
                this.completionChart.some(item => item.count > 0)) {
                const ctx = this.$refs.completionChart.getContext('2d');
                this.completionChartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: this.completionChart.map(item => item.month),
                        datasets: [{
                            label: 'Chapter Completions',
                            data: this.completionChart.map(item => item.count),
                            backgroundColor: 'rgba(59, 130, 246, 0.5)',
                            borderColor: 'rgba(59, 130, 246, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
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
            } else {
                console.warn('Cannot initialize completion chart - no data available');
                if (this.$refs.completionChart) {
                    this.showNoDataMessage(this.$refs.completionChart, 'No chapter completion data available yet');
                }
            }
        },
        
        // Export chart data to CSV
        exportChartData() {
            if (!this.enrollmentChart || this.enrollmentChart.length === 0) {
                alert('No enrollment data available to export');
                return;
            }
            
            let csvContent = "data:text/csv;charset=utf-8,";
            csvContent += "Month,New Enrollments\n";
            
            this.enrollmentChart.forEach(item => {
                csvContent += `${item.month},${item.count}\n`;
            });
            
            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "enrollment_data.csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        },
        
        // Export course performance data
        exportCourseData() {
            if (!this.coursePerformance || this.coursePerformance.length === 0) {
                alert('No course performance data available to export');
                return;
            }
            
            let csvContent = "data:text/csv;charset=utf-8,";
            csvContent += "Course,Students,Enrollments,Completion Rate,Average Score\n";
            
            this.coursePerformance.forEach(course => {
                csvContent += `"${course.title}",${course.students},${course.enrollments},${course.completion_rate}%,${course.average_score}\n`;
            });
            
            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "course_performance.csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        },
        
        // Export student engagement data
        exportStudentData() {
            if (!this.studentEngagement || this.studentEngagement.length === 0) {
                alert('No student engagement data available to export');
                return;
            }
            
            let csvContent = "data:text/csv;charset=utf-8,";
            csvContent += "Student,Email,Last Active,Courses,Completion Rate\n";
            
            this.studentEngagement.forEach(student => {
                csvContent += `"${student.name}","${student.email}","${student.last_active}",${student.courses},${student.completion_rate}%\n`;
            });
            
            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "student_engagement.csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        },
        
        // Show a message when no data is available
        showNoDataMessage(canvas, message) {
            const ctx = canvas.getContext('2d');
            const width = canvas.width;
            const height = canvas.height;
            
            ctx.clearRect(0, 0, width, height);
            ctx.font = '14px Arial';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillStyle = '#999';
            ctx.fillText(message, width / 2, height / 2);
        }
    }
};
</script>

<style scoped>
/* BAO Styles */
.dashboard-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.dashboard-title {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 24px;
}

.stat-card {
    @apply bg-white overflow-hidden shadow-sm sm:rounded-lg p-6;
}

.stat-title {
    @apply text-sm font-medium text-gray-500 mb-1;
}

.stat-value {
    @apply text-2xl font-bold text-gray-900;
}

.stat-subtitle {
    color: #6B7280;
    font-size: 14px;
}

.flame-icon {
    color: #F97316;
    margin-right: 4px;
}

.section-card {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    margin-bottom: 24px;
}

.section-title {
    font-size: 18px;
    font-weight: 600;
    padding: 16px 24px;
    border-bottom: 1px solid #E5E7EB;
}

.empty-message {
    color: #6B7280;
    text-align: center;
    padding: 32px;
}

.course-link {
    color: #F97316;
    font-weight: 500;
    text-decoration: none;
}

.course-link:hover {
    text-decoration: underline;
}

/* Admin styles */
.admin-dashboard {
    /* Add admin-specific styles here */
}
</style>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $page.props.auth.user?.role?.name === 'admin' ? 'Admin Dashboard' : 'Student Dashboard' }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Student Dashboard -->
                <div v-if="$page.props.auth.user?.role?.name === 'student'" class="space-y-8">
                    <!-- Student Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-2">Completed Readings</h3>
                                <p class="text-3xl font-bold text-orange-600">{{ studentProgress?.completedReadings || 0 }}</p>
                            </div>
                        </div>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-2">Average Reading Time</h3>
                                <p class="text-3xl font-bold text-blue-600">{{ studentProgress?.averageReadingTime || 0 }} min</p>
                            </div>
                        </div>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-2">Reading Streak</h3>
                                <p class="text-3xl font-bold text-green-600">{{ studentProgress?.streak || 0 }} days</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- My Courses -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">My Courses</h3>
                            <div class="space-y-6">
                                <div v-for="course in sortedCourses" :key="course.id" class="border-b border-gray-100 pb-4 last:border-b-0">
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                        <div class="mb-2 sm:mb-0">
                                            <h4 class="text-base font-medium text-gray-900">{{ course.title }}</h4>
                                            <p class="text-sm text-gray-500">{{ course.completed_readings }} of {{ course.total_readings }} PDF notes read</p>
                                        </div>
                                        <div class="flex items-center space-x-4">
                                            <div class="w-24 text-right">
                                                <span class="text-sm font-medium" :class="{'text-green-600': course.progress >= 75, 'text-orange-600': course.progress >= 25 && course.progress < 75, 'text-red-600': course.progress < 25}">{{ course.progress }}%</span>
                                            </div>
                                            <Link :href="`/courses/${course.id}`" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                Continue
                                            </Link>
                                        </div>
                                    </div>
                                    <div class="mt-2 w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="h-2.5 rounded-full" :class="{'bg-green-600': course.progress >= 75, 'bg-orange-500': course.progress >= 25 && course.progress < 75, 'bg-red-600': course.progress < 25}" :style="`width: ${course.progress}%`"></div>
                                    </div>
                                </div>
                                <div v-if="!courses || courses.length === 0" class="text-center py-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                    <p class="text-gray-500">You are not enrolled in any courses yet</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Upcoming Readings -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Upcoming Readings</h3>
                            <div class="space-y-4">
                                <div v-if="upcomingDeadlines && upcomingDeadlines.length > 0">
                                    <div v-for="reading in upcomingDeadlines" :key="reading.id" class="border-b border-gray-100 pb-4 last:border-b-0">
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ reading.title }}</p>
                                                <p class="text-xs text-gray-500">{{ reading.module_title }} - {{ reading.course_title }}</p>
                                            </div>
                                            <Link :href="`/courses/${reading.course_id}/modules/${reading.module_id}/chapters/${reading.chapter_id}`" class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                Read
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="text-center py-4">
                                    <p class="text-gray-500 text-sm">No upcoming readings</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Admin Dashboard -->
                <div v-else>
                    <!-- Admin Quick Actions Panel -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
                            <div class="flex flex-wrap gap-4">
                                <Link href="/courses/create" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors inline-block">
                                    Add New Course
                                </Link>
                                <Link href="/modules/create" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md transition-colors inline-block">
                                    Add New Module
                                </Link>
                                <Link href="/chapters/create" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-md transition-colors inline-block">
                                    Add New Chapter
                                </Link>
                                <button @click="exportChartData" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-md transition-colors inline-block">
                                    Export Statistics
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                        <!-- Total Students Card -->
                        <div class="stat-card">
                            <div class="stat-title">Total Students</div>
                            <div class="stat-value">{{ adminStats?.totalStudents || 0 }}</div>
                            <div class="stat-desc">Platform users</div>
                        </div>
                        
                        <!-- Total Courses Card -->
                        <div class="stat-card">
                            <div class="stat-title">Total Courses</div>
                            <div class="stat-value">{{ adminStats?.totalCourses || 0 }}</div>
                            <div class="stat-desc">Available courses</div>
                        </div>
                        
                        <!-- Total Modules Card -->
                        <div class="stat-card">
                            <div class="stat-title">Total Modules</div>
                            <div class="stat-value">{{ adminStats?.totalModules || 0 }}</div>
                            <div class="stat-desc">Course modules</div>
                        </div>
                        
                        <!-- Total Chapters Card -->
                        <div class="stat-card">
                            <div class="stat-title">Total Chapters</div>
                            <div class="stat-value">{{ adminStats?.totalChapters || 0 }}</div>
                            <div class="stat-desc">Learning chapters</div>
                        </div>
                    </div>
                    
                    <!-- Charts Section -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Enrollment Chart -->
                        <div class="stat-card">
                            <h3 class="text-lg font-semibold mb-4">Monthly Enrollments</h3>
                            <div class="h-64">
                                <canvas ref="enrollmentChart"></canvas>
                            </div>
                        </div>
                        
                        <!-- Completion Chart -->
                        <div class="stat-card">
                            <h3 class="text-lg font-semibold mb-4">Monthly Chapter Completions</h3>
                            <div class="h-64">
                                <canvas ref="completionChart"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Enrollments and Recent Completions -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Recent Enrollments -->
                        <div class="stat-card">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold">Recent Enrollments</h3>
                                <Link :href="route('enrollments.index')" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View all →</Link>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="enrollment in recentEnrollments" :key="enrollment.id">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ enrollment.student.name }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ enrollment.course.title }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ enrollment.created_at }}
                                            </td>
                                        </tr>
                                        <tr v-if="!recentEnrollments || recentEnrollments.length === 0">
                                            <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">
                                                No recent enrollments
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Recent Chapter Completions -->
                        <div class="stat-card">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold">Recent Chapter Completions</h3>
                                <Link :href="route('chapter-completions.index')" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View all →</Link>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chapter</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completion</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="completion in recentCompletions" :key="completion.id">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ completion.student.name }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ completion.chapter.title }}</div>
                                                <div class="text-xs text-gray-500">{{ completion.chapter.module.title }} - {{ completion.chapter.module.course.title }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500">{{ completion.completed_at }}</div>
                                                <div class="flex items-center mt-1">
                                                    <span class="text-xs text-gray-500 mr-2">Rating:</span>
                                                    <div class="flex">
                                                        <template v-for="i in 5" :key="i">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" :class="i <= completion.comprehension_rating ? 'text-yellow-500' : 'text-gray-300'" viewBox="0 0 20 20" fill="currentColor">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                            </svg>
                                                        </template>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr v-if="!recentCompletions || recentCompletions.length === 0">
                                            <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">
                                                No recent chapter completions
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Course Performance Section -->
                    <div class="stat-card mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Course Performance</h3>
                            <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View all courses →</a>
                            <button @click="exportCourseData" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-md transition-colors inline-block">
                                Export Data
                            </button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Students</th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enrollments</th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chapters</th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completed Chapters</th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg. Comprehension</th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg. Time (min)</th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completion Rate</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="course in coursePerformance" :key="course.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="font-medium text-gray-900">{{ course.title }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ course.students }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ course.enrollments }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ course.totalChapters }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ course.completedChapters }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="text-sm text-gray-900">{{ course.avgComprehension }}/5</div>
                                                <div class="ml-2 w-16 bg-gray-200 rounded-full h-2">
                                                    <div class="bg-blue-600 h-2 rounded-full" :style="{ width: (course.avgComprehension / 5 * 100) + '%' }"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ course.avgTimeSpent }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="text-sm text-gray-900">{{ course.completionRate }}%</div>
                                                <div class="ml-2 w-16 bg-gray-200 rounded-full h-2">
                                                    <div class="bg-green-600 h-2 rounded-full" :style="{ width: course.completionRate + '%' }"></div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="!coursePerformance || coursePerformance.length === 0">
                                        <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                            No course data available
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Student Engagement Metrics -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Student Engagement -->
                        <div class="stat-card">
                            <h3 class="text-lg font-semibold mb-4">Student Engagement</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completed Chapters</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg. Comprehension</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Time (hrs)</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <tr v-for="student in studentEngagement" :key="student.id" class="hover:bg-gray-50">
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ student.name }}</div>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ student.completedChapters }}</div>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="text-sm text-gray-900">{{ student.avgComprehension }}/5</div>
                                                    <div class="ml-2 w-16 bg-gray-200 rounded-full h-2">
                                                        <div class="bg-blue-600 h-2 rounded-full" :style="{ width: (student.avgComprehension / 5 * 100) + '%' }"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ student.totalTimeSpent }}</div>
                                            </td>
                                        </tr>
                                        <tr v-if="!studentEngagement || studentEngagement.length === 0">
                                            <td colspan="4" class="px-4 py-3 text-center text-sm text-gray-500">
                                                No student engagement data available
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Engagement Chart -->
                        <div class="stat-card">
                            <h3 class="text-lg font-semibold mb-4">Student Engagement Metrics</h3>
                            <canvas id="engagementChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <Link :href="route('courses.create')" class="stat-card bg-orange-50 hover:bg-orange-100 transition-colors flex flex-col items-center justify-center py-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-orange-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <div class="text-lg font-medium text-orange-700">Create New Course</div>
                        </Link>
                        
                        <Link href="/courses" class="stat-card bg-blue-50 hover:bg-blue-100 transition-colors flex flex-col items-center justify-center py-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-blue-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 0h.01M6 19a2 2 0 01-2-2V7a2 2 0 012-2h4a2 2 0 012 2v10a2 2 0 01-2 2H6a2 2 0 01-2-2v-1a2 2 0 012-2h2a2 2 0 012 2v1z" />
                            </svg>
                            <div class="text-lg font-medium text-blue-700">Manage Courses</div>
                        </Link>
                        
                        <Link :href="route('students.index')" class="stat-card bg-green-50 hover:bg-green-100 transition-colors flex flex-col items-center justify-center py-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-green-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <div class="text-lg font-medium text-green-700">Manage Students</div>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
