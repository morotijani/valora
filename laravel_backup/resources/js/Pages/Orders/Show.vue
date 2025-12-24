<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { ref } from 'vue';
import axios from 'axios';

const props = defineProps({
    order: Object,
    stripeKey: String,
});

const revealedCodes = ref({});
const revealing = ref({});

const revealCode = async (codeId) => {
    revealing.value[codeId] = true;
    try {
        const response = await axios.post(route('voucher.reveal', codeId));
        revealedCodes.value[codeId] = response.data.code;
    } catch (error) {
        alert('Failed to reveal code. Please refresh and try again.');
    } finally {
        revealing.value[codeId] = false;
    }
};

const copyToClipboard = (text) => {
    navigator.clipboard.writeText(text);
    // Could add toast notification here
};
</script>

<template>
    <Head :title="`Order #${order.uuid.substring(0,8)}`" />

    <MainLayout>
        <div class="py-12">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-gray-800/50 backdrop-blur border border-white/10 shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-white">Order Details</h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-400">UUID: {{ order.uuid }}</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-sm font-bold" 
                            :class="{
                                'bg-green-500/20 text-green-400': order.status === 'paid',
                                'bg-yellow-500/20 text-yellow-400': order.status === 'pending'
                            }">
                            {{ order.status.toUpperCase() }}
                        </span>
                    </div>
                    
                    <div class="border-t border-gray-700 px-4 py-5 sm:p-0">
                        <dl class="sm:divide-y sm:divide-gray-700">
                            <!-- Payment Section (Mock) -->
                            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6" v-if="order.status === 'pending'">
                                <dt class="text-sm font-medium text-gray-400">Payment Required</dt>
                                <dd class="mt-1 text-sm text-white sm:mt-0 sm:col-span-2">
                                    <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">
                                        Pay {{ order.currency }} {{ order.total_amount }} with Flutterwave
                                    </button>
                                </dd>
                            </div>

                            <!-- Purchased Items (Voucher Codes) -->
                            <div class="py-4 sm:py-5 sm:px-6">
                                <h4 class="text-md font-bold text-white mb-4">Your Items</h4>
                                <ul class="space-y-4">
                                     <li v-for="code in order.voucher_codes" :key="code.id" class="bg-gray-900/50 p-4 rounded-lg border border-gray-700">
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-300">Secret Code</span>
                                            
                                            <div v-if="code.status === 'sold'">
                                                <div v-if="revealedCodes[code.id]" class="flex items-center space-x-2">
                                                    <code class="bg-black/50 px-3 py-1 rounded text-cyan-400 font-mono text-lg tracking-wider">
                                                        {{ revealedCodes[code.id] }}
                                                    </code>
                                                    <button @click="copyToClipboard(revealedCodes[code.id])" class="text-gray-400 hover:text-white">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                        </svg>
                                                    </button>
                                                </div>
                                                <button v-else @click="revealCode(code.id)" 
                                                    :disabled="revealing[code.id]"
                                                    class="text-indigo-400 hover:text-indigo-300 font-medium flex items-center"
                                                >
                                                    <span v-if="revealing[code.id]">Revealing...</span>
                                                    <span v-else>Click to Reveal</span>
                                                </button>
                                            </div>
                                            <div v-else class="text-gray-500 italic">
                                                Available after payment
                                            </div>
                                        </div>
                                     </li>
                                </ul>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
