<template>
    <Head :title="`${book.title} - Book Details`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Flash Messages -->
            <div v-if="page.props.flash?.success" class="rounded-lg bg-green-50 p-4 dark:bg-green-900/20">
                <p class="text-sm text-green-800 dark:text-green-200">{{ page.props.flash.success }}</p>
            </div>
            <div v-if="page.props.flash?.error" class="rounded-lg bg-red-50 p-4 dark:bg-red-900/20">
                <p class="text-sm text-red-800 dark:text-red-200">{{ page.props.flash.error }}</p>
            </div>

            <!-- Header with back button -->
            <div class="flex items-center gap-4">
                <Button variant="outline" @click="goBack">
                    <ArrowLeft class="h-4 w-4 mr-2" />
                    Back to Catalog
                </Button>
            </div>

            <!-- Book Details -->
            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Main book information -->
                <div class="lg:col-span-2 space-y-6">
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-2xl">{{ book.title }}</CardTitle>
                            <p class="text-lg text-muted-foreground">by {{ book.author }}</p>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <Label class="text-sm font-medium">Genre</Label>
                                    <p class="text-sm text-muted-foreground">{{ book.genre }}</p>
                                </div>
                                <div>
                                    <Label class="text-sm font-medium">Publication Year</Label>
                                    <p class="text-sm text-muted-foreground">{{ book.publication_year }}</p>
                                </div>
                                <div>
                                    <Label class="text-sm font-medium">ISBN</Label>
                                    <p class="text-sm text-muted-foreground">{{ book.isbn }}</p>
                                </div>
                                <div>
                                    <Label class="text-sm font-medium">Availability</Label>
                                    <p class="text-sm" :class="book.is_available ? 'text-green-600' : 'text-red-600'">
                                        {{ book.available_quantity }} of {{ book.stock_quantity }} available
                                    </p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Synopsis -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Synopsis</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p class="text-sm leading-relaxed whitespace-pre-line">{{ book.synopsis }}</p>
                        </CardContent>
                    </Card>

                    <!-- Current Reservations Info (if any) -->
                    <Card v-if="book.active_reservations_count && book.active_reservations_count > 0">
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <Users class="h-5 w-5" />
                                Current Reservations
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p class="text-sm text-muted-foreground">
                                {{ book.active_reservations_count }}
                                {{ book.active_reservations_count === 1 ? 'person has' : 'people have' }}
                                currently reserved this book.
                            </p>
                        </CardContent>
                    </Card>
                </div>

                <!-- Reservation Panel -->
                <div class="space-y-6">
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <Calendar class="h-5 w-5" />
                                Reserve This Book
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <!-- Availability Status -->
                            <div class="flex items-center gap-2 p-3 rounded-md"
                                 :class="book.is_available ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200'">
                                <div class="h-2 w-2 rounded-full"
                                     :class="book.is_available ? 'bg-green-500' : 'bg-red-500'"></div>
                                <span class="text-sm font-medium">
                                    {{ book.is_available ? 'Available for reservation' : 'Currently unavailable' }}
                                </span>
                            </div>

                            <!-- Reservation Rules -->
                            <div class="text-xs text-muted-foreground space-y-1">
                                <p>• Reservations are valid for 7 days</p>
                                <p>• Only one active reservation per user</p>
                                <p>• Collect your book within the reservation period</p>
                            </div>

                            <!-- Reserve Button -->
                            <div class="space-y-3">
                                <Button
                                    v-if="book.is_available && book.can_reserve !== false"
                                    class="w-full"
                                    :disabled="reserving"
                                    @click="reserveBook"
                                >
                                    <div v-if="reserving" class="h-4 w-4 animate-spin rounded-full border-2 border-current border-t-transparent mr-2"></div>
                                    <Calendar class="h-4 w-4 mr-2" v-else />
                                    {{ reserving ? 'Reserving...' : 'Reserve Now' }}
                                </Button>

                                <Button
                                    v-else-if="!book.is_available"
                                    variant="secondary"
                                    class="w-full"
                                    disabled
                                >
                                    <XCircle class="h-4 w-4 mr-2" />
                                    Currently Unavailable
                                </Button>

                                <Button
                                    v-else
                                    variant="secondary"
                                    class="w-full"
                                    disabled
                                >
                                    <AlertCircle class="h-4 w-4 mr-2" />
                                    Cannot Reserve
                                </Button>

                                <p v-if="!book.can_reserve && book.is_available" class="text-xs text-muted-foreground">
                                    You may already have an active reservation or reached your limit.
                                </p>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Quick Actions -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Quick Actions</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-3">
                            <Button variant="outline" class="w-full justify-start" @click="goToCatalog">
                                <Search class="h-4 w-4 mr-2" />
                                Browse More Books
                            </Button>
                            <Button variant="outline" class="w-full justify-start" @click="goToReservations">
                                <BookOpen class="h-4 w-4 mr-2" />
                                My Reservations
                            </Button>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type AppPageProps, type BreadcrumbItem, type Book } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { AlertCircle, ArrowLeft, BookOpen, Calendar, Search, Users, XCircle } from 'lucide-vue-next';
import { ref } from 'vue';

interface BookDetailPageProps extends AppPageProps {
    book: Book & {
        active_reservations_count?: number;
        can_reserve?: boolean;
    };
    bookId: number;
}

const page = usePage<BookDetailPageProps>();
const reserving = ref(false);

const book = page.props.book;
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Book Catalog', href: '/catalog' },
    { title: book.title, href: `/books/${book.id}/details` },
];

const goBack = () => {
    window.history.back();
};

const goToCatalog = () => {
    router.visit('/catalog');
};

const goToReservations = () => {
    router.visit('/my-reservations');
};

const reserveBook = async () => {
    if (reserving.value) return;

    reserving.value = true;

    router.post('/reservations', { book_id: book.id }, {
        onSuccess: () => {
            // Refresh the page to update availability
            router.reload();
        },
        onError: (errors) => {
            console.error('Reservation failed:', errors);
        },
        onFinish: () => {
            reserving.value = false;
        }
    });
};
</script>
