<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

defineProps({
    stats: Object,
});
</script>

<template>
    <Head title="Admin Dashboard" />

    <MainLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold text-white mb-8">Admin Dashboard</h1>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                    <div class="bg-gray-800 overflow-hidden shadow rounded-lg border border-gray-700">
                        <div class="px-4 py-5 sm:p-6">
                            <dt class="text-sm font-medium text-gray-400 truncate">Total Sales</dt>
                            <dd class="mt-1 text-3xl font-semibold text-white">{{ stats.total_sales }}</dd>
                        </div>
                    </div>
                    <div class="bg-gray-800 overflow-hidden shadow rounded-lg border border-gray-700">
                        <div class="px-4 py-5 sm:p-6">
                            <dt class="text-sm font-medium text-gray-400 truncate">Total Orders</dt>
                            <dd class="mt-1 text-3xl font-semibold text-white">{{ stats.total_orders }}</dd>
                        </div>
                    </div>
                    <div class="bg-gray-800 overflow-hidden shadow rounded-lg border border-gray-700">
                        <div class="px-4 py-5 sm:p-6">
                            <dt class="text-sm font-medium text-gray-400 truncate">Total Products</dt>
                            <dd class="mt-1 text-3xl font-semibold text-white">{{ stats.total_products }}</dd>
                        </div>
                    </div>
                     <div class="bg-gray-800 overflow-hidden shadow rounded-lg border border-gray-700">
                        <div class="px-4 py-5 sm:p-6">
                            <dt class="text-sm font-medium text-gray-400 truncate">Total Users</dt>
                            <dd class="mt-1 text-3xl font-semibold text-white">{{ stats.total_users }}</dd>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-gray-800 shadow rounded-lg p-6 border border-gray-700">
                    <h2 class="text-lg font-medium text-white mb-4">Quick Actions</h2>
                    <div class="flex space-x-4">
                        <Link :href="route('admin.products.create')" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            Add New Product
                        </Link>
                         <Link :href="route('admin.products.index')" class="inline-flex items-center px-4 py-2 border border-gray-600 text-sm font-medium rounded-md text-gray-300 bg-gray-700 hover:bg-gray-600">
                            Manage Products
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
