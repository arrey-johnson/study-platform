<template>
    <Head title="Edit Exercise" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Edit Exercise
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
                                        required
                                    />
                                    <div v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.description }}
                                    </div>
                                </div>

                                <div>
                                    <InputLabel for="type" value="Type" />
                                    <select
                                        id="type"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                                        v-model="form.type"
                                        required
                                    >
                                        <option value="multiple_choice">Multiple Choice</option>
                                        <option value="true_false">True/False</option>
                                        <option value="short_answer">Short Answer</option>
                                        <option value="essay">Essay</option>
                                    </select>
                                    <div v-if="form.errors.type" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.type }}
                                    </div>
                                </div>

                                <div>
                                    <InputLabel for="content" value="Content" />
                                    <TextArea
                                        id="content"
                                        class="mt-1 block w-full"
                                        v-model="form.content"
                                        required
                                        rows="10"
                                    />
                                    <div v-if="form.errors.content" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.content }}
                                    </div>
                                </div>

                                <div>
                                    <InputLabel for="order" value="Order" />
                                    <TextInput
                                        id="order"
                                        type="number"
                                        class="mt-1 block w-full"
                                        v-model="form.order"
                                        required
                                    />
                                    <div v-if="form.errors.order" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.order }}
                                    </div>
                                </div>

                                <div class="flex items-center justify-end">
                                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                        Update Exercise
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

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    exercise: Object
});

const form = useForm({
    title: props.exercise.title,
    description: props.exercise.description,
    type: props.exercise.type,
    content: props.exercise.content,
    order: props.exercise.order
});

const submit = () => {
    form.put(route('exercises.update', props.exercise.id));
};
</script> 