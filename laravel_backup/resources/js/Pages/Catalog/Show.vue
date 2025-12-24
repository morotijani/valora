<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps({
    product: Object,
});

const form = useForm({
    product_id: props.product.id,
    quantity: 1,
});

const submit = () => {
    form.post(route('orders.store'));
};
</script>

<template>
    <Head :title="product.name" />

    <MainLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="lg:grid lg:grid-cols-2 lg:gap-x-8 lg:items-start text-white">
                    <!-- Image/Brand -->
                    <div class="flex flex-col-reverse">
                        <div class="w-full aspect-w-1 aspect-h-1 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-2xl overflow-hidden">
                             <span class="text-6xl font-extrabold tracking-widest">{{ product.brand }}</span>
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="mt-10 px-4 sm:px-0 sm:mt-16 lg:mt-0">
                        <h1 class="text-3xl font-extrabold tracking-tight text-white">{{ product.name }}</h1>

                        <div class="mt-3">
                            <h2 class="sr-only">Product information</h2>
                            <p class="text-3xl text-indigo-300">{{ product.currency }} {{ product.price }}</p>
                        </div>

                        <div class="mt-6">
                            <h3 class="sr-only">Description</h3>
                            <div class="text-base text-gray-300 space-y-6" v-html="product.description"></div>
                        </div>

                        <form @submit.prevent="submit" class="mt-6">
                            <div class="flex items-center space-x-4">
                                <label for="quantity" class="text-sm font-medium text-gray-300">Quantity</label>
                                <select v-model="form.quantity" id="quantity" name="quantity" class="rounded-md border border-gray-600 bg-gray-800 text-white py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                                    <option v-for="n in 10" :key="n" :value="n">{{ n }}</option>
                                </select>
                            </div>

                            <div class="mt-10 flex sm:flex-col1">
                                <button type="submit" 
                                    :disabled="form.processing || product.stock_quantity <= 0"
                                    class="max-w-xs flex-1 bg-indigo-600 border border-transparent rounded-full py-3 px-8 flex items-center justify-center text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200 shadow-lg glow"
                                >
                                    {{ product.stock_quantity > 0 ? 'Add to Order' : 'Out of Stock' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>

<style scoped>
.glow:hover {
    box-shadow: 0 0 15px rgba(99, 102, 241, 0.5);
}
</style>
