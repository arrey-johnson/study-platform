<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    student: Object
});

const form = useForm({
    name: props.student.name,
    email: props.student.email,
    password: '',
    password_confirmation: ''
});

const submit = () => {
    form.put(route('students.update', props.student.id));
};
</script>

<template>
    <Head title="Edit Student" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Student: {{ student.name }}</h2>
                <div class="flex space-x-2">
                    <Link 
                        :href="route('students.show', student.id)" 
                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md transition-colors"
                    >
                        View Details
                    </Link>
                    <Link 
                        :href="route('students.index')" 
                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md transition-colors"
                    >
                        Back to List
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit">
                            <div class="mb-6">
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                <input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                    required
                                />
                                <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
                            </div>

                            <div class="mb-6">
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                    required
                                />
                                <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</div>
                            </div>

                            <div class="mb-6">
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                    Password <span class="text-gray-500 text-xs">(leave blank to keep current password)</span>
                                </label>
                                <input
                                    id="password"
                                    v-model="form.password"
                                    type="password"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                />
                                <div v-if="form.errors.password" class="text-red-500 text-sm mt-1">{{ form.errors.password }}</div>
                            </div>

                            <div class="mb-6">
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                                    Confirm Password
                                </label>
                                <input
                                    id="password_confirmation"
                                    v-model="form.password_confirmation"
                                    type="password"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                />
                            </div>

                            <div class="flex justify-between">
                                <Link
                                    :href="route('students.destroy', student.id)"
                                    method="delete"
                                    as="button"
                                    type="button"
                                    class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-md transition-colors"
                                    v-if="$page.props.auth.user.role?.name === 'admin'"
                                    onclick="return confirm('Are you sure you want to delete this student? This action cannot be undone.')"
                                >
                                    Delete Student
                                </Link>
                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-md transition-colors"
                                    :disabled="form.processing"
                                >
                                    Update Student
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
