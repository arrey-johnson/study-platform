<script setup>
import { ref, onMounted, watch } from 'vue';

const props = defineProps({
    pdfUrl: String,
    title: String
});

const pdfContainer = ref(null);
const isLoading = ref(true);
const errorMessage = ref(null);
const currentPage = ref(1);
const totalPages = ref(0);
const showPdf = ref(true);

onMounted(() => {
    if (props.pdfUrl) {
        loadPdf();
    }
});

// Watch for changes to pdfUrl prop
watch(() => props.pdfUrl, (newUrl) => {
    if (newUrl) {
        loadPdf();
    }
});

const loadPdf = async () => {
    isLoading.value = true;
    errorMessage.value = null;
    
    try {
        // Create iframe for PDF viewing
        const iframe = document.createElement('iframe');
        iframe.style.width = '100%';
        iframe.style.height = '800px';
        iframe.style.border = 'none';
        
        // Set direct URL to the PDF
        iframe.src = `/storage/${props.pdfUrl}`;
        
        // Handle iframe load event
        iframe.onload = () => {
            isLoading.value = false;
        };
        
        // Set timeout to handle cases where onload might not fire
        setTimeout(() => {
            isLoading.value = false;
        }, 5000);
        
        // Clear container and append iframe
        if (pdfContainer.value) {
            pdfContainer.value.innerHTML = '';
            pdfContainer.value.appendChild(iframe);
        }
    } catch (error) {
        console.error('Error loading PDF:', error);
        errorMessage.value = 'Error loading PDF. Please try again later.';
        isLoading.value = false;
    }
};

// Toggle PDF visibility
const togglePdf = () => {
    showPdf.value = !showPdf.value;
};

// Navigation functions - simplified to just reload with page parameter
const goToPage = (page) => {
    if (page < 1) page = 1;
    currentPage.value = page;
    
    try {
        const iframe = pdfContainer.value.querySelector('iframe');
        if (iframe) {
            const currentSrc = iframe.src;
            const baseUrl = currentSrc.split('#')[0];
            iframe.src = `${baseUrl}#page=${page}`;
        }
    } catch (error) {
        console.error('Error navigating to page:', error);
    }
};

const nextPage = () => {
    goToPage(currentPage.value + 1);
};

const prevPage = () => {
    goToPage(currentPage.value - 1);
};
</script>

<template>
    <div class="secure-pdf-viewer">
        <h3 class="text-lg font-medium text-gray-900 mb-2" v-if="title">{{ title }}</h3>
        
        <div class="flex justify-between items-center mb-4">
            <button 
                @click="togglePdf" 
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition"
            >
                {{ showPdf ? 'Hide PDF' : 'Show PDF' }}
            </button>
            
            <div v-if="showPdf" class="flex items-center space-x-2">
                <button @click="prevPage" class="px-3 py-1 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200">
                    Previous
                </button>
                <div class="px-3 py-1 border border-gray-300 bg-white rounded-md">
                    <span class="text-sm">Page {{ currentPage }}</span>
                </div>
                <button @click="nextPage" class="px-3 py-1 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200">
                    Next
                </button>
                <input 
                    type="number" 
                    v-model="currentPage" 
                    min="1"
                    class="w-16 px-2 py-1 border border-gray-300 rounded-md text-sm"
                    @keyup.enter="goToPage(currentPage)"
                >
                <button @click="goToPage(currentPage)" class="px-2 py-1 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">
                    Go
                </button>
            </div>
        </div>
        
        <div v-if="showPdf">
            <div v-if="isLoading" class="flex justify-center items-center py-10 border border-gray-200 rounded-lg">
                <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="ml-3 text-sm text-gray-600">Loading PDF...</span>
            </div>
            
            <div v-else-if="errorMessage" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md">
                {{ errorMessage }}
            </div>
            
            <div v-else ref="pdfContainer" class="pdf-container border border-gray-200 rounded-lg overflow-hidden"></div>
            
            <div class="mt-3 text-xs text-gray-500 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                This content is protected and cannot be downloaded or copied.
            </div>
        </div>
    </div>
</template>

<style scoped>
.pdf-container {
    position: relative;
    min-height: 500px;
    height: 800px;
}

/* Disable text selection */
.secure-pdf-viewer {
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

/* Make sure iframe content can't be selected */
iframe {
    pointer-events: auto;
}
</style>
