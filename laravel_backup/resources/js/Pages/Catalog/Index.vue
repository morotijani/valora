<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

defineProps({
    products: Array,
});
</script>

<template>
    <Head title="Catalog" />

    <MainLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h1 class="text-4xl font-extrabold text-white sm:text-5xl">
                        <span class="block">Premium Gift Cards</span>
                        <span class="block text-indigo-400">Instant Delivery. Secure.</span>
                    </h1>
                    <p class="mt-4 text-xl text-gray-300">
                        Top mobile top-ups, gaming vouchers, and prepaid cards.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-y-10 sm:grid-cols-2 gap-x-6 lg:grid-cols-3 xl:grid-cols-4 xl:gap-x-8">
                    <Link 
                        v-for="product in products" 
                        :key="product.id" 
                        :href="route('products.show', product.slug)"
                        class="group relative bg-white/10 backdrop-blur-md rounded-2xl p-4 hover:bg-white/20 transition-all duration-300 border border-white/10"
                    >
                        <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-lg xl:aspect-w-7 xl:aspect-h-8">
                            <div class="h-48 w-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-4xl font-bold text-white shadow-lg group-hover:scale-105 transition-transform duration-300">
                                {{ product.brand }}
                            </div>
                        </div>
                        <div class="mt-4 flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-bold text-white">
                                    {{ product.name }}
                                </h3>
                                <p class="mt-1 text-sm text-gray-300">{{ product.brand }}</p>
                            </div>
                            <p class="text-lg font-medium text-indigo-300">{{ product.currency }} {{ product.price }}</p>
                        </div>
                        <div class="mt-2">
                             <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800" v-if="product.stock_quantity > 0">
                                In Stock
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800" v-else>
                                Sold Out
                            </span>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
