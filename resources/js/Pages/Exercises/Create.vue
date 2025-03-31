<template>
    <Head title="Create Exercise" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create Exercise for {{ chapter.title }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="mb-6">
                            <p class="text-gray-600">
                                <span class="font-medium">Course:</span> {{ course.title }}
                            </p>
                            <p class="text-gray-600">
                                <span class="font-medium">Module:</span> {{ module.title }}
                            </p>
                            <p class="text-gray-600">
                                <span class="font-medium">Chapter:</span> {{ chapter.title }}
                            </p>
                        </div>

                        <form @submit.prevent="submit">
                            <div class="space-y-6">
                                <!-- Title -->
                                <div>
                                    <InputLabel for="title" value="Title" />
                                    <TextInput
                                        id="title"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.title"
                                        required
                                    />
                                    <InputError :message="form.errors.title" class="mt-2" />
                                </div>

                                <!-- Description -->
                                <div>
                                    <InputLabel for="description" value="Description" />
                                    <TextArea
                                        id="description"
                                        class="mt-1 block w-full"
                                        v-model="form.description"
                                        required
                                    />
                                    <InputError :message="form.errors.description" class="mt-2" />
                                </div>

                                <!-- Exercise Type -->
                                <div>
                                    <InputLabel for="type" value="Exercise Type" />
                                    <select
                                        id="type"
                                        v-model="form.type"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        required
                                    >
                                        <option value="" disabled>Select exercise type</option>
                                        <option value="multiple_choice">Multiple Choice</option>
                                        <option value="true_false">True/False</option>
                                        <option value="written">Written Answer</option>
                                        <option value="file_upload">File Upload</option>
                                    </select>
                                    <InputError :message="form.errors.type" class="mt-2" />
                                </div>

                                <!-- Multiple Choice Options -->
                                <div v-if="form.type === 'multiple_choice'" class="space-y-4">
                                    <div class="flex justify-between items-center">
                                        <InputLabel value="Options" />
                                        <button
                                            type="button"
                                            @click="addOption"
                                            class="px-3 py-1 text-sm bg-blue-500 text-white rounded hover:bg-blue-600"
                                        >
                                            Add Option
                                        </button>
                                    </div>
                                    <div v-for="(option, index) in form.options" :key="index" class="flex items-center space-x-2">
                                        <TextInput
                                            v-model="form.options[index]"
                                            class="flex-grow"
                                            placeholder="Option text"
                                        />
                                        <input
                                            type="checkbox"
                                            :id="'correct-' + index"
                                            v-model="form.correct_answers"
                                            :value="index"
                                            class="ml-2 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                        />
                                        <label :for="'correct-' + index" class="text-sm text-gray-600">Correct</label>
                                        <button
                                            type="button"
                                            @click="removeOption(index)"
                                            class="px-2 py-1 text-sm bg-red-500 text-white rounded hover:bg-red-600"
                                        >
                                            Remove
                                        </button>
                                    </div>
                                    <InputError :message="form.errors.options" class="mt-2" />
                                    <InputError :message="form.errors.correct_answers" class="mt-2" />
                                </div>

                                <!-- True/False Options -->
                                <div v-if="form.type === 'true_false'" class="space-y-4">
                                    <InputLabel value="Correct Answer" />
                                    <div class="flex items-center space-x-4">
                                        <div class="flex items-center">
                                            <input
                                                type="radio"
                                                id="true-option"
                                                name="true-false"
                                                @change="setTrueFalseAnswer(true)"
                                                :checked="isTrueSelected"
                                                class="mr-2 h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                            />
                                            <label for="true-option">True</label>
                                        </div>
                                        <div class="flex items-center">
                                            <input
                                                type="radio"
                                                id="false-option"
                                                name="true-false"
                                                @change="setTrueFalseAnswer(false)"
                                                :checked="!isTrueSelected"
                                                class="mr-2 h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                            />
                                            <label for="false-option">False</label>
                                        </div>
                                    </div>
                                    <InputError :message="form.errors.correct_answers" class="mt-2" />
                                </div>

                                <!-- Points -->
                                <div>
                                    <InputLabel for="points" value="Points" />
                                    <TextInput
                                        id="points"
                                        type="number"
                                        class="mt-1 block w-full"
                                        v-model="form.points"
                                        min="0"
                                        required
                                    />
                                    <InputError :message="form.errors.points" class="mt-2" />
                                </div>

                                <!-- Deadline -->
                                <div>
                                    <InputLabel for="deadline" value="Deadline (Optional)" />
                                    <TextInput
                                        id="deadline"
                                        type="datetime-local"
                                        class="mt-1 block w-full"
                                        v-model="form.deadline"
                                    />
                                    <InputError :message="form.errors.deadline" class="mt-2" />
                                </div>

                                <!-- Active Status -->
                                <div class="flex items-center">
                                    <Checkbox id="is_active" v-model:checked="form.is_active" />
                                    <InputLabel for="is_active" value="Make exercise active" class="ml-2" />
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end space-x-4">
                                <Link
                                    :href="route('courses.modules.chapters.exercises.index', { course: course.id, module: module.id, chapter: chapter.id })"
                                    class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
                                >
                                    Cancel
                                </Link>
                                <PrimaryButton :disabled="form.processing">
                                    Create Exercise
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';
import Checkbox from '@/Components/Checkbox.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    course: Object,
    module: Object,
    chapter: Object
});

const form = useForm({
    title: '',
    description: '',
    type: '',
    options: [],
    correct_answers: [],
    points: 0,
    deadline: '',
    is_active: true
});

const isTrueSelected = computed(() => form.correct_answers[0] === true);

function addOption() {
    form.options.push('');
}

function removeOption(index) {
    form.options.splice(index, 1);
    
    // Remove corresponding correct answer if it exists
    const correctIndex = form.correct_answers.indexOf(index);
    if (correctIndex !== -1) {
        form.correct_answers.splice(correctIndex, 1);
    }
    
    // Adjust remaining correct answers indices
    form.correct_answers = form.correct_answers.map(i => i > index ? i - 1 : i);
}

function setTrueFalseAnswer(answer) {
    form.correct_answers = [answer];
}

function submit() {
    form.post(route('courses.modules.chapters.exercises.store', {
        course: props.course.id,
        module: props.module.id,
        chapter: props.chapter.id
    }));
}
</script>