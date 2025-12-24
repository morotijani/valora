<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen bg-gray-900 selection:bg-indigo-500 selection:text-white">
        <nav class="bg-gray-800/50 backdrop-blur-lg border-b border-white/10 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="shrink-0 flex items-center">
                            <Link :href="route('home')">
                                <span class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-400 to-cyan-400">
                                    VALORA
                                </span>
                            </Link>
                        </div>
                         <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                             <Link :href="route('home')" :active="route().current('home')" class="text-gray-300 hover:text-white inline-flex items-center px-1 pt-1 border-b-2 border-transparent hover:border-indigo-400 text-sm font-medium transition-colors">
                                Catalog
                             </Link>
                         </div>
                    </div>
                    <div class="flex items-center">
                        <div class="ml-3 relative">
                            <!-- User Dropdown Stub -->
                             <div class="text-gray-300 text-sm">
                                <template v-if="$page.props.auth.user">
                                   <Link :href="route('orders.index')" class="hover:text-white">My Orders</Link>
                                </template>
                                <template v-else>
                                    <Link :href="route('login')" class="hover:text-white">Log in</Link>
                                    <span class="mx-2">/</span>
                                    <Link :href="route('register')" class="hover:text-white">Sign up</Link>
                                </template>
                             </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <main>
            <slot />
        </main>
        
        <footer class="bg-gray-900 border-t border-white/10 mt-12">
            <div class="max-w-7xl mx-auto py-12 px-4 overflow-hidden sm:px-6 lg:px-8">
                 <p class="mt-8 text-center text-base text-gray-400">
                    &copy; 2024 Valora Inc. All rights reserved.
                </p>
            </div>
        </footer>
    </div>
</template>
