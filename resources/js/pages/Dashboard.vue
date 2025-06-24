<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { formatDate } from '@/lib/utils';
import { usePage } from '@inertiajs/vue3'
import Swal from 'sweetalert2'

import { defineProps, onMounted } from 'vue';

interface Product {
    id: number;
    name: string;
    description: string;
    price: number;
    stock: number;
    image: string;
    created_at: string;
}

const props = defineProps({
    products: {
        type: Array as () => Product[],
        required: true,
    },
})

const page = usePage()

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const addToCart = (productId: number) => {
    router.post('/cart/add', {
        product_id: productId,
        quantity: 1
    }, {
        preserveScroll: true,
        onSuccess: (page) => {
            const msg = page.props.flash?.success || 'Product added to cart!';
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: msg,
                timer: 2000,
                showConfirmButton: false,
            });
        },
        onError: (errors) => {
            const msg = page.props.flash?.error || 'Failed to add product to cart.';
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: msg,
                timer: 2500,
                showConfirmButton: false,
            });
        }
    })
}



onMounted(() => {
    console.log('Dashboard mounted', props);
});
</script>
<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div
                    v-for="product in props.products"
                    :key="product.id"
                    class="group relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-white dark:bg-gray-800 shadow-sm hover:shadow-md transition-all duration-200 cursor-pointer"
                >
                    <!-- Product Image -->
                    <div class="aspect-video relative overflow-hidden bg-gray-100 dark:bg-gray-700">
                        <img
                            v-if="product.image"
                            :src="product.image"
                            :alt="product.name"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                        >
                        <div
                            v-else
                            class="w-full h-full flex items-center justify-center text-gray-400 dark:text-gray-500"
                        >
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>

                        <!-- Stock Badge -->
                        <div class="absolute top-2 right-2">
                            <span
                                v-if="product.stock > 0"
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200"
                            >
                                {{ product.stock }} in stock
                            </span>
                            <span
                                v-else
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200"
                            >
                                Out of stock
                            </span>
                        </div>
                    </div>

                    <!-- Product Content -->
                    <div class="p-4 space-y-3">
                        <!-- Product Name & Price -->
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="font-semibold text-gray-900 dark:text-white text-lg line-clamp-1 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                {{ product.name }}
                            </h3>
                            <span class="text-xl font-bold text-green-600 dark:text-green-400 whitespace-nowrap">
                                ${{ product.price.toFixed(2) }}
                            </span>
                        </div>

                        <!-- Product Description -->
                        <p class="text-gray-600 dark:text-gray-300 text-sm line-clamp-2 leading-relaxed">
                            {{ product.description }}
                        </p>

                        <!-- Product Meta Info -->
                        <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-gray-700">
                            <span
                                v-if="product.created_at"
                                class="text-xs text-gray-500 dark:text-gray-400"
                            >
                                Added {{ formatDate(product.created_at) }}
                            </span>
                            <div class="flex items-center gap-2">
                                <!-- Action Buttons -->
                                <button
                                    @click="addToCart(product.id)"
                                    v-if="product.stock > 0"
                                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium text-white bg-primary hover:bg-primary dark:bg-primary dark:hover:bg-primary transition-colors cursor pointer"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m1.6 8L5 3H3m4 10v6a1 1 0 001 1h10a1 1 0 001-1v-6M9 19v2m6-2v2"></path>
                                    </svg>
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
