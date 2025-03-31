<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    chapter: Object
});

const form = useForm({
    title: props.chapter.title,
    content: props.chapter.content,
    order: props.chapter.order,
    pdf_notes: null,
    is_active: props.chapter.is_active
});

const submit = () => {
    form.put(route('chapters.update', props.chapter.id), {
        method: 'put',
        forceFormData: true
    });
};

const handleFileUpload = (e) => {
    form.pdf_notes = e.target.files[0];
};
</script>

<template>
    <Head title="Edit Chapter" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Edit Chapter
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit" enctype="multipart/form-data">
                            <div class="mb-4">
                                <InputLabel for="title" value="Title" />
                                <TextInput
                                    id="title"
                                    type="text"
                                    class="block w-full mt-1"
                                    v-model="form.title"
                                    required
                                    autofocus
                                />
                                <InputError :message="form.errors.title" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <InputLabel for="content" value="Content" />
                                <TextArea
                                    id="content"
                                    class="block w-full mt-1"
                                    v-model="form.content"
                                    rows="5"
                                />
                                <InputError :message="form.errors.content" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <InputLabel for="order" value="Order" />
                                <TextInput
                                    id="order"
                                    type="number"
                                    class="block w-full mt-1"
                                    v-model="form.order"
                                />
                                <InputError :message="form.errors.order" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <InputLabel for="pdf_notes" value="PDF Notes" />
                                <input
                                    id="pdf_notes"
                                    type="file"
                                    class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    @input="handleFileUpload"
                                    accept="application/pdf"
                                />
                                <p class="mt-1 text-sm text-gray-500">Upload PDF notes for this chapter (max 10MB)</p>
                                <InputError :message="form.errors.pdf_notes" class="mt-2" />
                                
                                <div v-if="chapter.pdf_notes" class="mt-2">
                                    <p class="text-sm font-medium text-gray-700">Current PDF:</p>
                                    <a :href="`/storage/${chapter.pdf_notes}`" target="_blank" class="text-sm text-blue-600 hover:underline">
                                        View current PDF
                                    </a>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="flex items-center">
                                    <Checkbox name="is_active" v-model:checked="form.is_active" />
                                    <span class="ml-2 text-sm text-gray-600">Active</span>
                                </label>
                                <InputError :message="form.errors.is_active" class="mt-2" />
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <PrimaryButton class="ml-4" :disabled="form.processing">
                                    Update Chapter
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>