<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    module: Object,
    progress: Number
});

const deleteType = ref(null);
const deleteId = ref(null);
const showDeleteModal = ref(false);

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
    if (deleteType.value === 'module') {
        router.delete(route('modules.destroy', deleteId.value));
    } else if (deleteType.value === 'chapter') {
        router.delete(route('chapters.destroy', deleteId.value));
    }
    showDeleteModal.value = false;
};

const markChapterComplete = (chapterId) => {
    router.post(route('chapters.mark-completed', chapterId), {}, {
        preserveScroll: true,
        onSuccess: () => {
            // Refresh the page to update the completion status
            router.reload();
        }
    });
};
</script>

<template>
    <Head :title="module.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        {{ module.title }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ module.course.title }}
                    </p>
                </div>
                <div class="flex space-x-4">
                    <Link
                        :href="route('courses.show', module.course.id)"
                        class="inline-flex items-center rounded-md bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Course
                    </Link>
                    <Link
                        v-if="$page.props.auth.user.role?.name === 'admin'"
                        :href="route('modules.chapters.create', module.id)"
                        class="inline-flex items-center rounded-md bg-orange-600 px-4 py-2 text-sm font-semibold text-white hover:bg-orange-700"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Add Chapter
                    </Link>
                    <Link
                        v-if="$page.props.auth.user.role?.name === 'admin'"
                        @click="confirmDelete('module', module.id)"
                        class="inline-flex items-center rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h3.382l-.76 3.84A2 2 0 0111 16V6a1 1 0 00-.553-.894L9 2zm5 14.5a1 1 0 00-1-1V7a1 1 0 00-1-1H4a1 1 0 000 2h1a1 1 0 001 1v10a1 1 0 001 1h5a1 1 0 001-1z" clip-rule="evenodd" />
                        </svg>
                        Delete Module
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <!-- Module Description -->
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900">Description</h3>
                            <p class="mt-2 text-sm text-gray-500">{{ module.description }}</p>
                        </div>

                        <!-- Progress Bar (for students) -->
                        <div v-if="progress !== undefined" class="mb-8">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-medium text-gray-900">Your Progress</h3>
                                <span class="text-sm font-medium text-gray-500">{{ progress }}%</span>
                            </div>
                            <div class="mt-2">
                                <div class="h-2 w-full rounded-full bg-gray-200">
                                    <div
                                        class="h-2 rounded-full bg-orange-600 transition-all duration-500"
                                        :style="{ width: `${progress}%` }"
                                    ></div>
                                </div>
                            </div>
                        </div>

                        <!-- Chapters -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Chapters</h3>
                            <div class="mt-4 space-y-4">
                                <div v-for="chapter in module.chapters" :key="chapter.id" class="overflow-hidden rounded-lg bg-white shadow">
                                    <div class="p-6">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <h4 class="text-lg font-medium text-gray-900">{{ chapter.title }}</h4>
                                                <!-- Completion indicator -->
                                                <div v-if="chapter.is_completed" class="ml-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="flex space-x-4">
                                                <!-- View PDF Button -->
                                                <Link
                                                    v-if="chapter.has_pdf"
                                                    :href="route('chapters.pdf', chapter.id)"
                                                    class="inline-flex items-center px-3 py-1.5 bg-purple-600 text-white rounded-md hover:bg-purple-700 text-sm font-medium transition-colors"
                                                    target="_blank"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                    </svg>
                                                    View PDF
                                                </Link>

                                                <!-- View Chapter Button -->
                                                <Link
                                                    :href="route('chapters.show', chapter.id)"
                                                    class="inline-flex items-center text-sm text-blue-600 hover:text-blue-900"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    View
                                                </Link>

                                                <!-- Mark Complete Button -->
                                                <button
                                                    v-if="!chapter.is_completed && $page.props.auth.user.role?.name === 'student'"
                                                    @click.prevent="markChapterComplete(chapter.id)"
                                                    class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium transition-colors"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Mark Complete
                                                </button>

                                                <!-- Edit Chapter Button -->
                                                <Link
                                                    v-if="$page.props.auth.user.role?.name === 'admin'"
                                                    :href="route('chapters.edit', chapter.id)"
                                                    class="inline-flex items-center text-sm text-orange-600 hover:text-orange-900"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    Edit
                                                </Link>

                                                <!-- Delete Chapter Button -->
                                                <Link
                                                    v-if="$page.props.auth.user.role?.name === 'admin'"
                                                    @click="confirmDelete('chapter', chapter.id)"
                                                    class="inline-flex items-center text-sm text-red-600 hover:text-red-900"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h3.382l-.76 3.84A2 2 0 0111 16V6a1 1 0 00-.553-.894L9 2zm5 14.5a1 1 0 00-1-1V7a1 1 0 00-1-1H4a1 1 0 000 2h1a1 1 0 001 1v10a1 1 0 001 1h5a1 1 0 001-1z" clip-rule="evenodd" />
                                                    </svg>
                                                    Delete
                                                </Link>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-500">
                                                {{ chapter.description || 'No description available' }}
                                            </p>
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

    <!-- Delete Confirmation Modal -->
    <div
        v-if="showDeleteModal"
        class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black bg-opacity-50"
    >
        <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-2xl shadow-xl">
            <h3 class="text-lg font-medium text-gray-900">Delete Confirmation</h3>
            <p class="mt-2 text-sm text-gray-500">
                Are you sure you want to delete this {{ deleteType === 'module' ? 'module' : 'chapter' }}?
            </p>
            <div class="mt-4">
                <button
                    @click="executeDelete"
                    class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-red-500"
                >
                    Delete
                </button>
                <button
                    @click="cancelDelete"
                    class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-300 rounded-md hover:bg-gray-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-gray-500"
                >
                    Cancel
                </button>
            </div>
        </div>
    </div>
</template>