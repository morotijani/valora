<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

defineProps({
    products: Object,
});
</script>

<template>
    <Head title="Manage Products" />

    <MainLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-white">Products</h1>
                    <Link :href="route('admin.products.create')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md font-medium text-sm">
                        Add Product
                    </Link>
                </div>

                <div class="bg-gray-800 shadow overflow-hidden sm:rounded-lg border border-gray-700">
                    <table class="min-w-full divide-y divide-gray-700 text-white">
                        <thead class="bg-gray-900">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Brand</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Price</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Stock</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-800 divide-y divide-gray-700">
                            <tr v-for="product in products.data" :key="product.id">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-white">{{ product.name }}</div>
                                    <div class="text-sm text-gray-400">{{ product.uuid }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-300">{{ product.brand }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-300">{{ product.currency }} {{ product.price }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                    {{ product.stock_quantity }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" :class="product.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                        {{ product.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <Link :href="route('products.show', product.slug)" class="text-indigo-400 hover:text-indigo-300 mr-4">View</Link>
                                    <!-- Edit link stub -->
                                    <a href="#" class="text-gray-500 hover:text-gray-400 cursor-not-allowed">Edit</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                 <!-- Pagination stub -->
                 <div class="bg-gray-800 px-4 py-3 flex items-center justify-between border-t border-gray-700 sm:px-6" v-if="products.links">
                    <!-- Simple pagination buttons would go here -->
                 </div>
            </div>
        </div>
    </MainLayout>
</template>
