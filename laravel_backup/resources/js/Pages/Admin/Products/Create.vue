<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

const form = useForm({
    name: '',
    brand: '',
    country_code: '',
    description: '',
    face_value: 0,
    price: 0,
    currency: 'USD',
    is_active: true,
});

const submit = () => {
    form.post(route('admin.products.store'));
};
</script>

<template>
    <Head title="Create Product" />

    <MainLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1">
                        <div class="px-4 sm:px-0">
                            <h3 class="text-lg font-medium leading-6 text-white">Create Product</h3>
                            <p class="mt-1 text-sm text-gray-400">
                                Add a new gift card or voucher to the inventory.
                            </p>
                        </div>
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <form @submit.prevent="submit">
                            <div class="shadow sm:rounded-md sm:overflow-hidden">
                                <div class="px-4 py-5 bg-gray-800 space-y-6 sm:p-6 border border-gray-700 rounded-md">
                                    <div class="grid grid-cols-6 gap-6">
                                        <div class="col-span-6 sm:col-span-4">
                                            <label for="name" class="block text-sm font-medium text-gray-300">Product Name</label>
                                            <input type="text" v-model="form.name" id="name" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-600 bg-gray-700 text-white rounded-md">
                                            <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                                        </div>

                                        <div class="col-span-6 sm:col-span-3">
                                            <label for="brand" class="block text-sm font-medium text-gray-300">Brand</label>
                                            <input type="text" v-model="form.brand" id="brand" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-600 bg-gray-700 text-white rounded-md">
                                        </div>

                                        <div class="col-span-6 sm:col-span-3">
                                            <label for="country_code" class="block text-sm font-medium text-gray-300">Country Code (ISO 2)</label>
                                            <input type="text" v-model="form.country_code" id="country_code" maxlength="2" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-600 bg-gray-700 text-white rounded-md uppercase">
                                        </div>
                                        
                                        <div class="col-span-6 sm:col-span-2">
                                            <label for="currency" class="block text-sm font-medium text-gray-300">Currency</label>
                                            <input type="text" v-model="form.currency" id="currency" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-600 bg-gray-700 text-white rounded-md uppercase">
                                        </div>
                                        
                                        <div class="col-span-6 sm:col-span-2">
                                            <label for="face_value" class="block text-sm font-medium text-gray-300">Face Value</label>
                                            <input type="number" step="0.01" v-model="form.face_value" id="face_value" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-600 bg-gray-700 text-white rounded-md">
                                        </div>

                                        <div class="col-span-6 sm:col-span-2">
                                            <label for="price" class="block text-sm font-medium text-gray-300">Selling Price</label>
                                            <input type="number" step="0.01" v-model="form.price" id="price" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-600 bg-gray-700 text-white rounded-md">
                                        </div>

                                        <div class="col-span-6">
                                            <label for="description" class="block text-sm font-medium text-gray-300">Description</label>
                                            <textarea id="description" v-model="form.description" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border border-gray-600 bg-gray-700 text-white rounded-md"></textarea>
                                        </div>
                                        
                                         <div class="col-span-6">
                                            <div class="flex items-start">
                                                <div class="flex items-center h-5">
                                                    <input id="is_active" v-model="form.is_active" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-600 rounded bg-gray-700">
                                                </div>
                                                <div class="ml-3 text-sm">
                                                    <label for="is_active" class="font-medium text-gray-300">Active</label>
                                                    <p class="text-gray-500">Available for purchase immediately.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="px-4 py-3 bg-gray-800 text-right sm:px-6 border-t border-gray-700">
                                    <button type="submit" 
                                        :disabled="form.processing"
                                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                    >
                                        Create Product
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
