<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

defineProps({
    orders: Object,
});
</script>

<template>
    <Head title="My Orders" />

    <MainLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold text-white mb-6">Order History</h1>
                
                <div class="bg-gray-800 shadow overflow-hidden sm:rounded-md border border-gray-700">
                    <ul role="list" class="divide-y divide-gray-700">
                        <li v-for="order in orders.data" :key="order.id">
                            <Link :href="route('orders.show', order.uuid)" class="block hover:bg-gray-700 transition duration-150 ease-in-out">
                                <div class="px-4 py-4 sm:px-6">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-medium text-indigo-400 truncate">
                                            Order #{{ order.uuid.substring(0, 8) }}
                                        </p>
                                        <div class="ml-2 flex-shrink-0 flex">
                                            <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800" v-if="order.status === 'paid'">
                                                Paid
                                            </p>
                                            <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800" v-else-if="order.status === 'pending'">
                                                Pending
                                            </p>
                                            <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800" v-else>
                                                {{ order.status }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-2 sm:flex sm:justify-between">
                                        <div class="sm:flex">
                                            <p class="flex items-center text-sm text-gray-300">
                                                {{ order.currency }} {{ order.total_amount }}
                                            </p>
                                        </div>
                                        <div class="mt-2 flex items-center text-sm text-gray-400 sm:mt-0">
                                            <p>
                                                Placed on <time :datetime="order.created_at">{{ new Date(order.created_at).toLocaleDateString() }}</time>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </Link>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
