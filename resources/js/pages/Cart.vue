<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { ShoppingCart, Package, CreditCard, Trash2, Plus, Minus } from 'lucide-vue-next';
import Swal from 'sweetalert2';

const page = usePage();
const items = computed(() => page.props.items || []);
const total = computed(() => page.props.total || 0);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Cart',
        href: '/cart',
    },
];

function checkout() {
    router.post('/checkout');
}

function updateQuantity(itemId: number, newQuantity: number) {
    if (newQuantity < 1) return;
    router.patch(
        `/cart/${itemId}`,
        { quantity: newQuantity },
        {
            preserveScroll: true,
            onSuccess: (page) => {
                const msg = page.props.flash?.success;
                if (msg) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: msg,
                        timer: 1200,
                        showConfirmButton: false,
                    });
                }
            },
            onError: (errors) => {
                const msg = errors?.error || 'Failed to update cart.';
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: msg,
                    timer: 2000,
                    showConfirmButton: false,
                });
            },
        }
    );
}

function removeItem(itemId: number) {
    Swal.fire({
        title: 'Remove item?',
        text: 'Are you sure you want to remove this item from your cart?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, remove it',
        cancelButtonText: 'Cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(`/cart/${itemId}`, {
                preserveScroll: true,
                onSuccess: (page) => {
                    const msg = page.props.flash?.success || 'Item removed from cart.';
                    Swal.fire({
                        icon: 'success',
                        title: 'Removed',
                        text: msg,
                        timer: 1200,
                        showConfirmButton: false,
                    });
                },
                onError: (errors) => {
                    const msg = errors?.error || 'Failed to remove item.';
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: msg,
                        timer: 2000,
                        showConfirmButton: false,
                    });
                },
            });
        }
    });
}
</script>

<template>
    <Head title="Shopping Cart" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto px-4 py-8 max-w-7xl">
            <!-- Header Section -->
            <div class="flex items-center gap-3 mb-8">
                <div class="p-3 bg-primary/10 rounded-full">
                    <ShoppingCart class="h-8 w-8 text-primary" />
                </div>
                <div>
                    <h1 class="text-4xl font-bold tracking-tight">Shopping Cart</h1>
                    <p class="text-muted-foreground mt-1">
                        {{ items.length }} {{ items.length === 1 ? 'item' : 'items' }} in your cart
                    </p>
                </div>
            </div>

            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Cart Items Section -->
                <div class="lg:col-span-2">
                    <Card v-if="items.length === 0" class="border-dashed">
                        <CardContent class="flex flex-col items-center justify-center py-16">
                            <div class="p-4 bg-muted rounded-full mb-4">
                                <Package class="h-12 w-12 text-muted-foreground" />
                            </div>
                            <h3 class="text-xl font-semibold mb-2">Your cart is empty</h3>
                            <p class="text-muted-foreground text-center mb-6">
                                Start adding some products to your cart and they will appear here.
                            </p>
                            <Button @click="router.visit('dashboard')" class="gap-2">
                                <ShoppingCart class="h-4 w-4" />
                                Continue Shopping
                            </Button>
                        </CardContent>
                    </Card>

                    <Card v-else>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <Package class="h-5 w-5" />
                                Cart Items
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-6">
                            <div
                                v-for="(item, index) in items"
                                :key="item.id"
                                class="group"
                            >
                                <div class="flex items-center gap-4 p-4 rounded-lg border bg-card hover:bg-accent/50 transition-colors">
                                    <!-- Product Image Placeholder -->
                                    <div class="w-20 h-20 bg-muted rounded-lg flex items-center justify-center flex-shrink-0">
                                        <Package class="h-8 w-8 text-muted-foreground" />
                                    </div>

                                    <!-- Product Details -->
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-lg mb-1 truncate">
                                            {{ item.product.name }}
                                        </h3>
                                        <p class="text-muted-foreground text-sm mb-2">
                                            Unit Price: <span class="font-medium">${{ item.product.price.toFixed(2) }}</span>
                                        </p>

                                        <!-- Quantity Controls -->
                                        <div class="flex items-center gap-2">
                                            <Button
                                                variant="outline"
                                                size="sm"
                                                @click="updateQuantity(item.id, item.quantity - 1)"
                                                :disabled="item.quantity <= 1"
                                                class="h-8 w-8 p-0"
                                            >
                                                <Minus class="h-3 w-3" />
                                            </Button>
                                            <div variant="secondary" class="px-3 py-1 font-medium">
                                                {{ item.quantity }}
                                            </div>
                                            <Button
                                                variant="outline"
                                                size="sm"
                                                @click="updateQuantity(item.id, item.quantity + 1)"
                                                class="h-8 w-8 p-0"
                                            >
                                                <Plus class="h-3 w-3" />
                                            </Button>
                                        </div>
                                    </div>

                                    <!-- Subtotal and Remove -->
                                    <div class="text-right flex-shrink-0">
                                        <div class="text-xl font-bold mb-2">
                                            ${{ (item.product.price * item.quantity).toFixed(2) }}
                                        </div>
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            @click="removeItem(item.id)"
                                            class="text-destructive hover:text-destructive hover:bg-destructive/10 opacity-0 group-hover:opacity-100 transition-opacity"
                                        >
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </div>

                                <Separator v-if="index < items.length - 1" class="my-4" />
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Order Summary Section -->
                <div v-if="items.length > 0" class="lg:col-span-1">
                    <div class="sticky top-6">
                        <Card>
                            <CardHeader>
                                <CardTitle class="flex items-center gap-2">
                                    <CreditCard class="h-5 w-5" />
                                    Order Summary
                                </CardTitle>
                            </CardHeader>
                            <CardContent class="space-y-4">
                                <!-- Order Details -->
                                <div class="space-y-3">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-muted-foreground">Subtotal</span>
                                        <span class="font-medium">${{ total.toFixed(2) }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-muted-foreground">Shipping</span>
                                        <span class="font-medium text-green-600">Free</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-muted-foreground">Tax</span>
                                        <span class="font-medium">Calculated at checkout</span>
                                    </div>
                                </div>

                                <Separator />

                                <!-- Total -->
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-semibold">Total</span>
                                    <span class="text-2xl font-bold text-primary">
                                        ${{ total.toFixed(2) }}
                                    </span>
                                </div>

                                <!-- Checkout Button -->
                                <Button
                                    @click="checkout"
                                    class="w-full gap-2 h-12 text-base font-semibold"
                                    size="lg"
                                >
                                    <CreditCard class="h-5 w-5" />
                                    Proceed to Checkout
                                </Button>

                                <!-- Continue Shopping -->
                                <Button
                                    variant="outline"
                                    @click="router.visit('/dashboard')"
                                    class="w-full gap-2"
                                >
                                    <ShoppingCart class="h-4 w-4" />
                                    Continue Shopping
                                </Button>

                                <!-- Trust Badges -->
                                <div class="pt-4 space-y-2">
                                    <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                        <span>Secure checkout</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                        <span>Free shipping on orders over $50</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                        <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                                        <span>30-day return policy</span>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
