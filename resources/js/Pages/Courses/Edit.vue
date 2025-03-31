<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Checkbox from '@/Components/Checkbox.vue';

const props = defineProps({
    course: Object
});

const form = useForm({
    title: props.course.title,
    description: props.course.description,
    is_active: props.course.is_active
});

const submit = () => {
    form.put(route('courses.update', props.course.id));
};
</script>

<template>
    <Head title="Edit Course" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Edit Course
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit">
                            <div class="space-y-6">
                                <div>
                                    <InputLabel for="title" value="Title" />
                                    <TextInput
                                        id="title"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.title"
                                        required
                                    />
                                    <div v-if="form.errors.title" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.title }}
                                    </div>
                                </div>

                                <div>
                                    <InputLabel for="description" value="Description" />
                                    <TextArea
                                        id="description"
                                        class="mt-1 block w-full"
                                        v-model="form.description"
                                        rows="4"
                                    />
                                    <div v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.description }}
                                    </div>
                                </div>

                                <div class="flex items-center">
                                    <Checkbox
                                        id="is_active"
                                        v-model:checked="form.is_active"
                                    />
                                    <InputLabel for="is_active" value="Active" class="ml-2" />
                                </div>

                                <div class="flex items-center justify-end mt-4">
                                    <PrimaryButton
                                        class="ml-4"
                                        :class="{ 'opacity-25': form.processing }"
                                        :disabled="form.processing"
                                    >
                                        Update Course
                                    </PrimaryButton>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template> 