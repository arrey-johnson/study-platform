<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    course: Object,
    progress: Number
});

const deleteType = ref(null);
const deleteId = ref(null);
const showDeleteModal = ref(false);
const activeModule = ref(null);
const completedModules = ref([]);

const confirmDelete = (type, id) => {
    deleteType.value = type;
    deleteId.value = id;
    showDeleteModal.value = true;
};

const cancelDelete = () => {
    deleteType.value = null;
    deleteId.value = null;
    showDeleteModal.value = false;
};

const executeDelete = () => {
    if (deleteType.value === 'course') {
        router.delete(route('courses.destroy', deleteId.value));
    } else if (deleteType.value === 'module') {
        router.delete(route('modules.destroy', deleteId.value));
    } else if (deleteType.value === 'chapter') {
        router.delete(route('chapters.destroy', deleteId.value));
    }
    showDeleteModal.value = false;
};

const toggleModule = (moduleId) => {
    activeModule.value = activeModule.value === moduleId ? null : moduleId;
};

const isAdmin = computed(() => {
    return props.course && props.$page?.props?.auth?.user?.role?.name === 'admin';
});

const isStudent = computed(() => {
    return props.course && props.$page?.props?.auth?.user?.role?.name === 'student';
});

const totalChapters = computed(() => {
    if (!props.course?.modules) return 0;
    return props.course.modules.reduce((sum, module) => sum + module.chapters.length, 0);
});

const totalPdfNotes = computed(() => {
    if (!props.course?.modules) return 0;
    let count = 0;
    props.course.modules.forEach(module => {
        module.chapters.forEach(chapter => {
            if (chapter.pdf_notes) count++;
        });
    });
    return count;
});

// Check if all chapters in a module are completed
const isModuleCompleted = (module) => {
    if (!module.chapters || module.chapters.length === 0) return false;
    
    // Check if all chapters in the module are completed
    for (const chapter of module.chapters) {
        if (!chapter.is_completed) {
            return false;
        }
    }
    
    return true;
};

// Mark all chapters in a module as completed
const markModuleComplete = (moduleId) => {
    router.post(route('modules.mark-completed', moduleId), {}, {
        preserveScroll: true,
        onSuccess: () => {
            // Refresh the page to update the progress
            router.reload();
        }
    });
};

</script>

<template>
    <Head :title="course.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ course.title }}
                </h2>
                <div class="flex space-x-4">
                    <Link
                        v-if="$page.props.auth.user.role?.name === 'admin'"
                        :href="route('courses.edit', course.id)"
                        class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition-colors duration-200"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                        Edit Course
                    </Link>
                    <Link
                        v-if="$page.props.auth.user.role?.name === 'admin'"
                        :href="route('courses.modules.create', course.id)"
                        class="inline-flex items-center rounded-md bg-orange-600 px-4 py-2 text-sm font-semibold text-white hover:bg-orange-700 transition-colors duration-200"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Add Module
                    </Link>
                    <Link
                        v-if="$page.props.auth.user.role?.name === 'admin'"
                        :href="route('courses.destroy', course.id)"
                        method="delete"
                        as="button"
                        class="inline-flex items-center rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 transition-colors duration-200"
                        @click.prevent="confirmDelete('course', course.id)"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        Delete Course
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Course Header -->
                <div class="mb-8 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="relative">
                        <div class="h-48 w-full bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <h1 class="text-4xl font-bold text-white drop-shadow-lg">{{ course.title }}</h1>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <!-- Course Stats -->
                        <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div class="rounded-lg bg-gray-50 p-4 text-center shadow">
                                <div class="text-3xl font-bold text-indigo-600">{{ course.modules.length }}</div>
                                <div class="text-sm text-gray-500">Modules</div>
                            </div>
                            <div class="rounded-lg bg-gray-50 p-4 text-center shadow">
                                <div class="text-3xl font-bold text-purple-600">{{ totalChapters }}</div>
                                <div class="text-sm text-gray-500">Chapters</div>
                            </div>
                            <div class="rounded-lg bg-gray-50 p-4 text-center shadow">
                                <div class="text-3xl font-bold text-pink-600">{{ totalPdfNotes }}</div>
                                <div class="text-sm text-gray-500">PDF Notes</div>
                            </div>
                        </div>
                        
                        <!-- Course Description -->
                        <div class="mb-8">
                            <h3 class="text-xl font-semibold text-gray-900">About This Course</h3>
                            <p class="mt-3 text-gray-600 leading-relaxed">{{ course.description }}</p>
                        </div>

                        <!-- Progress Bar (for students) -->
                        <div v-if="progress !== undefined" class="mb-8">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xl font-semibold text-gray-900">Your Progress</h3>
                                <span class="text-lg font-medium text-indigo-600">{{ progress }}%</span>
                            </div>
                            <div class="mt-3">
                                <div class="h-3 w-full rounded-full bg-gray-200">
                                    <div
                                        class="h-3 rounded-full transition-all duration-500"
                                        :class="{
                                            'bg-red-500': progress < 25,
                                            'bg-yellow-500': progress >= 25 && progress < 50,
                                            'bg-blue-500': progress >= 50 && progress < 75,
                                            'bg-green-500': progress >= 75
                                        }"
                                        :style="{ width: `${progress}%` }"
                                    ></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modules -->
                <div class="mb-8">
                    <h2 class="mb-6 text-2xl font-bold text-gray-800">Course Content</h2>
                    
                    <div v-if="course.modules.length === 0" class="rounded-lg bg-white p-8 text-center shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <p class="mt-4 text-gray-600">No modules available for this course yet.</p>
                    </div>
                    
                    <div v-else class="space-y-4">
                        <div v-for="module in course.modules" :key="module.id" class="overflow-hidden rounded-lg bg-white shadow-sm">
                            <!-- Module Header -->
                            <div 
                                class="flex cursor-pointer items-center justify-between bg-gray-50 p-4 hover:bg-gray-100 transition-colors duration-200"
                                @click="toggleModule(module.id)"
                            >
                                <div class="flex items-center space-x-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 text-indigo-600">
                                        <span class="text-lg font-semibold">{{ module.order || '?' }}</span>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900">{{ module.title }}</h3>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <span class="text-sm text-gray-500">{{ module.chapters.length }} chapters</span>
                                    
                                    <!-- Mark Complete Button (only for students) -->
                                    <button 
                                        v-if="isStudent && !isModuleCompleted(module)"
                                        @click.stop="markModuleComplete(module.id)"
                                        class="inline-flex items-center rounded-md bg-green-50 px-3 py-1 text-sm font-medium text-green-700 hover:bg-green-100"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Mark Complete
                                    </button>
                                    
                                    <!-- Completed Badge (only for students) -->
                                    <span 
                                        v-if="isStudent && isModuleCompleted(module)"
                                        class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Completed
                                    </span>
                                    
                                    <svg 
                                        xmlns="http://www.w3.org/2000/svg" 
                                        class="h-5 w-5 text-gray-500 transition-transform duration-200"
                                        :class="{ 'rotate-180': activeModule === module.id }"
                                        fill="none" 
                                        viewBox="0 0 24 24" 
                                        stroke="currentColor"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                            
                            <!-- Module Content -->
                            <div v-show="activeModule === module.id" class="border-t border-gray-200 p-6">
                                <div class="mb-4">
                                    <p class="text-gray-600">{{ module.description }}</p>
                                </div>
                                
                                <!-- Admin Actions -->
                                <div v-if="$page.props.auth.user.role?.name === 'admin'" class="mb-4 flex space-x-3">
                                    <Link
                                        :href="route('modules.edit', module.id)"
                                        class="inline-flex items-center rounded-md bg-blue-50 px-3 py-1.5 text-sm font-medium text-blue-700 hover:bg-blue-100"
                                    >
                                        Edit Module
                                    </Link>
                                    <Link
                                        :href="route('modules.chapters.create', module.id)"
                                        class="inline-flex items-center rounded-md bg-green-50 px-3 py-1.5 text-sm font-medium text-green-700 hover:bg-green-100"
                                    >
                                        Add Chapter
                                    </Link>
                                    <button
                                        @click.stop="confirmDelete('module', module.id)"
                                        class="inline-flex items-center rounded-md bg-red-50 px-3 py-1.5 text-sm font-medium text-red-700 hover:bg-red-100"
                                    >
                                        Delete Module
                                    </button>
                                </div>
                                
                                <!-- Chapters List -->
                                <div v-if="module.chapters.length > 0" class="space-y-3">
                                    <h4 class="font-medium text-gray-700">Chapters:</h4>
                                    <div v-for="chapter in module.chapters" :key="chapter.id" class="rounded-md border border-gray-200 p-4 hover:bg-gray-50 transition-colors duration-200">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 text-gray-600">
                                                    <span class="text-sm font-medium">{{ chapter.order || '?' }}</span>
                                                </div>
                                                <div>
                                                    <h5 class="font-medium text-gray-900">{{ chapter.title }}</h5>
                                                    <div class="mt-1 flex items-center space-x-2">
                                                        <span v-if="chapter.pdf_notes" class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                            </svg>
                                                            PDF Notes
                                                        </span>
                                                        <span v-if="chapter.exercises && chapter.exercises.length > 0" class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                            </svg>
                                                            {{ chapter.exercises.length }} Exercises
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex space-x-3">
                                                <Link
                                                    v-if="$page.props.auth.user.role?.name === 'admin'"
                                                    :href="route('chapters.edit', chapter.id)"
                                                    class="text-sm text-blue-600 hover:text-blue-800"
                                                >
                                                    Edit
                                                </Link>
                                                <Link
                                                    :href="route('chapters.show', chapter.id)"
                                                    class="inline-flex items-center rounded-md bg-indigo-50 px-3 py-1.5 text-sm font-medium text-indigo-700 hover:bg-indigo-100"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    View
                                                </Link>
                                                <Link
                                                    v-if="chapter.pdf_notes"
                                                    :href="route('chapters.pdf', chapter.id)"
                                                    target="_blank"
                                                    class="inline-flex items-center rounded-md bg-red-50 px-3 py-1.5 text-sm font-medium text-red-700 hover:bg-red-100"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                    </svg>
                                                    View PDF
                                                </Link>
                                                <button
                                                    v-if="$page.props.auth.user.role?.name === 'admin'"
                                                    @click.stop="confirmDelete('chapter', chapter.id)"
                                                    class="text-sm text-red-600 hover:text-red-800"
                                                >
                                                    Delete
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="rounded-md bg-gray-50 p-4 text-center">
                                    <p class="text-sm text-gray-500">No chapters available in this module yet.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="w-full max-w-md overflow-hidden rounded-lg bg-white shadow-xl">
                <div class="bg-red-600 px-4 py-3 text-white">
                    <h3 class="text-lg font-medium">Confirm Deletion</h3>
                </div>
                <div class="p-6">
                    <p class="mb-4 text-gray-700">
                        Are you sure you want to delete this {{ deleteType }}? This action cannot be undone.
                    </p>
                    <div class="flex justify-end space-x-3">
                        <button
                            @click="cancelDelete"
                            class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors duration-200"
                        >
                            Cancel
                        </button>
                        <button
                            @click="executeDelete"
                            class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors duration-200"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>