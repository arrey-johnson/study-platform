<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import Modal from '@/Components/Modal.vue';
import axios from 'axios';

const props = defineProps({
    categories: Array
});

const form = useForm({
    title: '',
    description: '',
    category_id: '',
    is_active: true
});

const categoryForm = useForm({
    name: '',
    description: ''
});

const showNewCategoryModal = ref(false);
const localCategories = ref([...props.categories]);
const categoryError = ref('');

const submit = () => {
    form.post(route('courses.store'));
};

const createCategory = async () => {
    try {
        categoryError.value = '';
        const response = await axios.post(route('categories.store'), {
            name: categoryForm.name,
            description: categoryForm.description
        });
        
        // Add the new category to the local list
        localCategories.value.push(response.data);
        // Select the new category
        form.category_id = response.data.id;
        // Close the modal and reset the form
        showNewCategoryModal.value = false;
        categoryForm.reset();
    } catch (error) {
        if (error.response?.data?.errors?.name) {
            categoryError.value = error.response.data.errors.name[0];
        } else {
            categoryError.value = 'An error occurred while creating the category.';
        }
    }
};
</script>

<template>
    <Head title="Create Course" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Create Course
                </h2>
                <Link :href="route('courses.index')" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-md text-gray-700 transition-colors">
                    Back to Courses
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit">
                            <div class="space-y-6">
                                <div>
                                    <InputLabel for="title" value="Course Title" />
                                    <TextInput
                                        id="title"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.title"
                                        placeholder="Enter course title"
                                        required
                                    />
                                    <InputError :message="form.errors.title" />
                                </div>

                                <div>
                                    <div class="flex justify-between items-center">
                                        <InputLabel for="category" value="Category" />
                                        <SecondaryButton
                                            type="button"
                                            @click="showNewCategoryModal = true"
                                            class="ml-2"
                                        >
                                            Add New Category
                                        </SecondaryButton>
                                    </div>
                                    <select
                                        id="category"
                                        v-model="form.category_id"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        required
                                    >
                                        <option value="" disabled>Select a category</option>
                                        <option v-for="category in localCategories" :key="category.id" :value="category.id">
                                            {{ category.name }}
                                        </option>
                                    </select>
                                    <InputError :message="form.errors.category_id" />
                                </div>

                                <div>
                                    <InputLabel for="description" value="Description" />
                                    <TextArea
                                        id="description"
                                        class="mt-1 block w-full"
                                        v-model="form.description"
                                        placeholder="Provide a detailed description of the course"
                                        rows="6"
                                    />
                                    <InputError :message="form.errors.description" />
                                </div>

                                <div class="flex items-center">
                                    <Checkbox
                                        id="is_active"
                                        v-model:checked="form.is_active"
                                    />
                                    <InputLabel for="is_active" value="Make course active and available to students" class="ml-2" />
                                </div>

                                <div class="flex items-center justify-end mt-8 space-x-4">
                                    <Link :href="route('courses.index')" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-md text-gray-700 transition-colors">
                                        Cancel
                                    </Link>
                                    <PrimaryButton :disabled="form.processing">
                                        Create Course
                                    </PrimaryButton>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- New Category Modal -->
        <Modal :show="showNewCategoryModal" @close="showNewCategoryModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Create New Category
                </h2>

                <form @submit.prevent="createCategory" class="mt-6">
                    <div class="space-y-6">
                        <div>
                            <InputLabel for="category_name" value="Category Name" />
                            <TextInput
                                id="category_name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="categoryForm.name"
                                required
                                autofocus
                            />
                            <InputError :message="categoryError" />
                        </div>

                        <div>
                            <InputLabel for="category_description" value="Description (Optional)" />
                            <TextArea
                                id="category_description"
                                class="mt-1 block w-full"
                                v-model="categoryForm.description"
                                rows="3"
                            />
                        </div>

                        <div class="flex justify-end mt-6 space-x-3">
                            <SecondaryButton type="button" @click="showNewCategoryModal = false">
                                Cancel
                            </SecondaryButton>
                            <PrimaryButton :disabled="categoryForm.processing">
                                Create Category
                            </PrimaryButton>
                        </div>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>