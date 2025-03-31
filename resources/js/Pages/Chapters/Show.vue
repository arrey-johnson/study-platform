<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, Head, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const props = defineProps({
    chapter: Object,
    isCompleted: Boolean,
    courseProgress: Number
});

const showPdf = ref(false);
const showCompletionForm = ref(false);
const timer = ref(0);
const timerInterval = ref(null);

// Create forms for chapter completion
const completeForm = useForm({
    time_spent: 0,
    comprehension_rating: 3,
    notes: ''
});

const incompleteForm = useForm({});

const startTimer = () => {
    if (timerInterval.value) return;
    
    timerInterval.value = setInterval(() => {
        timer.value++;
    }, 60000); // Update every minute
};

const stopTimer = () => {
    if (timerInterval.value) {
        clearInterval(timerInterval.value);
        timerInterval.value = null;
    }
};

onMounted(() => {
    // Start timer when page loads
    startTimer();
    
    // Clean up when component is unmounted
    return () => {
        stopTimer();
        document.removeEventListener('keydown', preventKeyboardShortcuts);
    };
});

const toggleCompletionForm = () => {
    showCompletionForm.value = !showCompletionForm.value;
    completeForm.time_spent = timer.value;
};

const markAsCompleted = () => {
    // Set the time spent
    completeForm.time_spent = timer.value;
    
    // Submit the form
    completeForm.post(route('chapters.complete', props.chapter.id), {
        onSuccess: () => {
            showCompletionForm.value = false;
        }
    });
};

const markAsIncomplete = () => {
    incompleteForm.post(route('chapters.incomplete', props.chapter.id));
};

const togglePdfVisibility = () => {
    showPdf.value = !showPdf.value;
    
    // Add event listeners when PDF is shown
    if (showPdf.value) {
        setTimeout(() => {
            addPdfProtection();
        }, 500);
    }
};

// Function to add protection against downloading
const addPdfProtection = () => {
    // Prevent keyboard shortcuts
    document.addEventListener('keydown', preventKeyboardShortcuts);
    
    // Prevent right-click on PDF container
    const pdfContainer = document.querySelector('.pdf-container');
    if (pdfContainer) {
        pdfContainer.addEventListener('contextmenu', (e) => {
            e.preventDefault();
            return false;
        });
    }
    
    // Try to access iframe content to prevent right-click there too
    try {
        const iframe = document.querySelector('.pdf-frame');
        if (iframe && iframe.contentDocument) {
            iframe.contentDocument.addEventListener('contextmenu', (e) => {
                e.preventDefault();
                return false;
            });
        }
    } catch (error) {
        console.log('Cross-origin restrictions prevented iframe access');
    }
};

// Prevent keyboard shortcuts that could be used to download
const preventKeyboardShortcuts = (e) => {
    // Prevent Ctrl+S, Ctrl+P, Ctrl+Shift+S
    if ((e.ctrlKey || e.metaKey) && 
        (e.key === 's' || e.key === 'p' || 
         (e.shiftKey && e.key === 'S'))) {
        e.preventDefault();
        return false;
    }
};
</script>

<template>
    <Head :title="chapter.title" />
    
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ chapter.title }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ chapter.module.title }} - {{ chapter.module.course.title }}
                    </p>
                </div>
                <Link :href="route('modules.show', chapter.module.id)" class="text-sm text-blue-600 hover:underline">
                    Back to Module
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Course Progress Bar -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Your Progress</h3>
                        <div class="w-full bg-gray-200 rounded-full h-4 mb-2">
                            <div class="bg-blue-600 h-4 rounded-full" :style="{ width: `${courseProgress}%` }"></div>
                        </div>
                        <div class="flex justify-between text-sm text-gray-500">
                            <span>{{ courseProgress }}% Complete</span>
                            <span>{{ isCompleted ? 'Chapter Completed' : 'Chapter In Progress' }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <!-- Chapter Content -->
                        <div class="prose max-w-none" v-html="chapter.content"></div>
                        
                        <!-- PDF Display Section -->
                        <div v-if="chapter.has_pdf" class="mt-8 border-t pt-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Chapter PDF</h3>
                                <button 
                                    @click="togglePdfVisibility" 
                                    class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 text-sm font-medium transition-colors"
                                >
                                    {{ showPdf ? 'Hide PDF' : 'View PDF' }}
                                </button>
                            </div>
                            
                            <div v-if="showPdf" class="pdf-container">
                                <iframe 
                                    :src="route('chapters.pdf', chapter.id)" 
                                    class="pdf-frame"
                                    frameborder="0"
                                ></iframe>
                                <div class="pdf-overlay">
                                    <div class="pdf-warning">
                                        <p>This PDF is for viewing only. Downloading is not permitted.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Chapter Completion Button -->
                        <div class="mt-8 border-t pt-6">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">Chapter Completion</h3>
                                    <p class="text-sm text-gray-500">Mark this chapter as completed when you're done.</p>
                                    <p v-if="timer > 0" class="text-xs text-gray-500 mt-1">Time spent: {{ timer }} minutes</p>
                                </div>
                                
                                <div v-if="!isCompleted">
                                    <button 
                                        @click="toggleCompletionForm" 
                                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium transition-colors"
                                    >
                                        Mark as Completed
                                    </button>
                                </div>
                                <div v-else>
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="text-green-700 font-medium">Completed</span>
                                        <button 
                                            @click="markAsIncomplete" 
                                            class="ml-3 text-xs text-gray-500 hover:text-gray-700 underline"
                                        >
                                            Mark as Incomplete
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Chapter Completion Form -->
                            <div v-if="showCompletionForm" class="mt-6 bg-gray-50 p-4 rounded-lg">
                                <h4 class="font-medium text-gray-900 mb-3">Complete Your Learning</h4>
                                
                                <div class="space-y-4">
                                    <!-- Comprehension Rating -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            How well did you understand this chapter?
                                        </label>
                                        <div class="flex items-center">
                                            <span class="text-sm text-gray-500 mr-2">Not at all</span>
                                            <div class="flex space-x-2">
                                                <template v-for="rating in 5" :key="rating">
                                                    <button 
                                                        type="button"
                                                        @click="completeForm.comprehension_rating = rating"
                                                        :class="[
                                                            'w-8 h-8 rounded-full flex items-center justify-center',
                                                            completeForm.comprehension_rating >= rating 
                                                                ? 'bg-blue-600 text-white' 
                                                                : 'bg-gray-200 text-gray-600'
                                                        ]"
                                                    >
                                                        {{ rating }}
                                                    </button>
                                                </template>
                                            </div>
                                            <span class="text-sm text-gray-500 ml-2">Very well</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Notes -->
                                    <div>
                                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                                            Notes (optional)
                                        </label>
                                        <textarea
                                            id="notes"
                                            v-model="completeForm.notes"
                                            rows="3"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            placeholder="Write any notes or key takeaways from this chapter..."
                                        ></textarea>
                                    </div>
                                    
                                    <!-- Time Spent -->
                                    <div>
                                        <label for="time_spent" class="block text-sm font-medium text-gray-700 mb-1">
                                            Time spent (minutes)
                                        </label>
                                        <input
                                            id="time_spent"
                                            type="number"
                                            v-model="completeForm.time_spent"
                                            min="1"
                                            class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        />
                                        <p class="text-xs text-gray-500 mt-1">
                                            We've tracked {{ timer }} minutes. You can adjust if needed.
                                        </p>
                                    </div>
                                    
                                    <!-- Submit Buttons -->
                                    <div class="flex justify-end space-x-3">
                                        <button 
                                            type="button" 
                                            @click="showCompletionForm = false"
                                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 text-sm font-medium"
                                        >
                                            Cancel
                                        </button>
                                        <button 
                                            type="button" 
                                            @click="markAsCompleted"
                                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium"
                                            :disabled="completeForm.processing"
                                        >
                                            {{ completeForm.processing ? 'Submitting...' : 'Complete Chapter' }}
                                        </button>
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

<style scoped>
.pdf-container {
    position: relative;
    overflow: hidden;
}

.pdf-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 10;
    pointer-events: none;
}

.pdf-frame {
    z-index: 5;
}

/* Disable text selection */
.pdf-container {
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

/* Prevent context menu on right-click */
:deep(.pdf-container) {
    pointer-events: auto;
}

:deep(.pdf-container *) {
    pointer-events: auto;
}
</style>