<template>
    <Head title="Student Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Student Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <!-- Completed Readings Card -->
                    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 hover:shadow-lg transition-shadow duration-300">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-orange-100 text-orange-600 mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500 uppercase tracking-wide font-semibold">Completed Readings</div>
                                    <div class="mt-1 text-3xl font-bold text-gray-900">{{ studentProgress.completedReadings }}</div>
                                    <div class="mt-1 text-sm text-gray-600">PDF chapters completed</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Average Reading Time Card -->
                    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 hover:shadow-lg transition-shadow duration-300">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500 uppercase tracking-wide font-semibold">Average Reading Time</div>
                                    <div class="mt-1 text-3xl font-bold text-gray-900">{{ studentProgress.averageReadingTime }} min</div>
                                    <div class="mt-1 text-sm text-gray-600">Per PDF chapter</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Reading Streak Card -->
                    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 hover:shadow-lg transition-shadow duration-300">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500 uppercase tracking-wide font-semibold">Reading Streak</div>
                                    <div class="mt-1 text-3xl font-bold text-gray-900">{{ studentProgress.streak }} days</div>
                                    <div class="mt-1 text-sm text-gray-600">
                                        <span class="inline-flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-500 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                            Keep the streak alive!
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Weekly Progress Chart -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 mb-8">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Weekly Progress</h3>
                        <div class="h-64 flex items-center justify-center">
                            <canvas ref="weeklyProgressChart" v-if="weeklyProgress && weeklyProgress.labels && weeklyProgress.labels.length > 0"></canvas>
                            <div v-else class="text-center py-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                <p class="text-gray-500">No weekly progress data available yet</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- My Courses and Topic Progress -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                    <!-- My Courses -->
                    <div class="md:col-span-2 bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-lg font-semibold text-gray-800">My Courses</h3>
                                <Link href="/courses" class="text-sm text-orange-600 hover:text-orange-800 font-medium">View All</Link>
                            </div>
                            
                            <div class="space-y-6">
                                <div v-for="course in sortedCourses" :key="course.id" class="border-b border-gray-100 pb-6 last:border-b-0 last:pb-0">
                                    <div class="flex justify-between items-start mb-2">
                                        <Link :href="`/courses/${course.id}`" class="text-lg font-medium text-gray-900 hover:text-orange-600">
                                            {{ course.title }}
                                        </Link>
                                        <span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs font-semibold rounded-full">
                                            {{ course.progress }}%
                                        </span>
                                    </div>
                                    <p class="text-gray-600 text-sm mb-2">{{ course.description }}</p>
                                    <div class="flex items-center text-sm text-gray-500 mb-2">
                                        <span>{{ course.completed_exercises }} of {{ course.total_exercises }} exercises completed</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-orange-500 h-2 rounded-full" :style="{ width: `${course.progress}%` }"></div>
                                    </div>
                                </div>
                                
                                <div v-if="!courses || courses.length === 0" class="py-8 text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                    <p class="text-gray-500 mb-4">You're not enrolled in any courses yet</p>
                                    <Link href="/courses" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-md transition-colors inline-block">
                                        Browse Available Courses
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Topic Progress -->
                    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Progress by Topic</h3>
                            <div class="h-64 flex items-center justify-center">
                                <canvas ref="topicProgressChart" v-if="topicProgress && topicProgress.length > 0"></canvas>
                                <div v-else class="text-center py-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                    <p class="text-gray-500">No topic data available yet</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Upcoming Deadlines and Recent Activity -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Upcoming Deadlines -->
                    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Upcoming Deadlines</h3>
                            <div class="space-y-4">
                                <div v-for="deadline in upcomingDeadlines" :key="deadline.id" class="border-b border-gray-100 pb-4 last:border-b-0">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <Link 
                                                :href="`/courses/${deadline.course_id}/modules/${deadline.module_id}/chapters/${deadline.chapter_id}/exercises/${deadline.exercise.id}`" 
                                                class="font-medium text-gray-900 hover:text-orange-600"
                                            >
                                                {{ deadline.exercise.title }}
                                            </Link>
                                            <div class="text-sm text-gray-600 mt-1">Due: {{ deadline.due_date }}</div>
                                        </div>
                                        <Link 
                                            :href="`/courses/${deadline.course_id}/modules/${deadline.module_id}/chapters/${deadline.chapter_id}/exercises/${deadline.exercise.id}`" 
                                            class="px-3 py-1 bg-orange-100 text-orange-700 rounded-md text-xs font-medium hover:bg-orange-200"
                                        >
                                            Start
                                        </Link>
                                    </div>
                                </div>
                                
                                <div v-if="!upcomingDeadlines || upcomingDeadlines.length === 0" class="py-8 text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-gray-500">No upcoming deadlines</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Activity -->
                    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Activity</h3>
                            <div class="space-y-4">
                                <div v-for="activity in recentActivity" :key="activity.id" class="border-b border-gray-100 pb-4 last:border-b-0">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <div class="font-medium text-gray-900">{{ activity.exercise.title }}</div>
                                            <div class="text-sm text-gray-600">
                                                Course: {{ activity.course.title }}
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1">{{ activity.submitted_at }}</div>
                                        </div>
                                        <div :class="{
                                            'px-2 py-1 rounded text-xs font-medium': true,
                                            'bg-green-100 text-green-800': activity.status === 'completed',
                                            'bg-yellow-100 text-yellow-800': activity.status === 'pending',
                                            'bg-red-100 text-red-800': activity.status === 'failed'
                                        }">
                                            {{ activity.status }}
                                            <span v-if="activity.score !== null">
                                                ({{ activity.score }}%)
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div v-if="!recentActivity || recentActivity.length === 0" class="py-8 text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-gray-500">No recent activity</p>
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
import { Link, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

export default {
    components: {
        AuthenticatedLayout,
        Link,
        Head
    },
    props: {
        studentProgress: Object,
        courses: Array,
        weeklyProgress: Object,
        topicProgress: Array,
        recentActivity: Array,
        upcomingDeadlines: Array
    },
    data() {
        return {
            weeklyProgressChartInstance: null,
            topicProgressChartInstance: null
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
            this.initWeeklyProgressChart();
            this.initTopicProgressChart();
        });
    },
    methods: {
        initWeeklyProgressChart() {
            if (!this.weeklyProgress || !this.$refs.weeklyProgressChart) return;
            
            // Destroy existing chart instance if it exists
            if (this.weeklyProgressChartInstance) {
                this.weeklyProgressChartInstance.destroy();
            }
            
            const ctx = this.$refs.weeklyProgressChart.getContext('2d');
            
            // Ensure data is properly formatted
            const labels = this.weeklyProgress.labels || [];
            const data = this.weeklyProgress.data || [];
            
            console.log('Weekly Progress Data:', { labels, data });
            
            this.weeklyProgressChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Completed Exercises',
                        data: data,
                        backgroundColor: 'rgba(234, 88, 12, 0.7)',
                        borderColor: 'rgba(234, 88, 12, 1)',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: false,
                            text: 'Weekly Progress'
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
        },
        initTopicProgressChart() {
            if (!this.topicProgress || this.topicProgress.length === 0 || !this.$refs.topicProgressChart) return;
            
            // Destroy existing chart instance if it exists
            if (this.topicProgressChartInstance) {
                this.topicProgressChartInstance.destroy();
            }
            
            const ctx = this.$refs.topicProgressChart.getContext('2d');
            
            const labels = this.topicProgress.map(topic => topic.name);
            const data = this.topicProgress.map(topic => topic.progress);
            
            console.log('Topic Progress Data:', { labels, data });
            
            const backgroundColors = [
                'rgba(234, 88, 12, 0.7)',  // Orange
                'rgba(59, 130, 246, 0.7)', // Blue
                'rgba(16, 185, 129, 0.7)', // Green
                'rgba(139, 92, 246, 0.7)'  // Purple
            ];
            
            this.topicProgressChartInstance = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: backgroundColors,
                        borderColor: 'white',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                        }
                    },
                    cutout: '65%'
                }
            });
        }
    }
};
</script>
