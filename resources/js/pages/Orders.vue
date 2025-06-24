<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { Package, CreditCard, ListOrdered } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Orders',
        href: '/orders',
    },
];

const page = usePage();
const orders = computed(() => page.props.orders || []);
</script>
<template>
    <Head title="Orders" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto px-4 py-8 max-w-7xl">
            <!-- Header Section -->
            <div class="flex items-center gap-3 mb-8">
                <div class="p-3 bg-primary/10 rounded-full">
                    <ListOrdered class="h-8 w-8 text-primary" />
                </div>
                <div>
                    <h1 class="text-4xl font-bold tracking-tight">Your Orders</h1>
                    <p class="text-muted-foreground mt-1">
                        {{ orders.length }} {{ orders.length === 1 ? 'order' : 'orders' }} placed
                    </p>
                </div>
            </div>

            <Card v-if="orders.length === 0" class="border-dashed">
                <CardContent class="flex flex-col items-center justify-center py-16">
                    <div class="p-4 bg-muted rounded-full mb-4">
                        <Package class="h-12 w-12 text-muted-foreground" />
                    </div>
                    <h3 class="text-xl font-semibold mb-2">No orders yet</h3>
                    <p class="text-muted-foreground text-center mb-6">
                        You haven't placed any orders yet. Start shopping now!
                    </p>
                </CardContent>
            </Card>

            <div v-else class="space-y-8">
                <Card v-for="order in orders" :key="order.id">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <CreditCard class="h-5 w-5" />
                            Order #{{ order.id }}
                        </CardTitle>
                        <div class="text-sm text-muted-foreground mt-1">
                            Placed on {{ new Date(order.created_at).toLocaleDateString() }}
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in order.order_items" :key="item.id" class="border-b last:border-b-0 hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap font-semibold text-gray-700">
                                            {{ item.product?.name || 'Product' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                            ${{ item.price.toFixed(2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                            {{ item.quantity }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-800 font-semibold">
                                            ${{ (item.price * item.quantity).toFixed(2) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <Separator class="my-4" />
                        <div class="flex justify-end">
                            <span class="text-lg font-semibold">Total: ${{ order.total_amount.toFixed(2) }}</span>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
</style>
