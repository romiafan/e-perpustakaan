<template>
    <Head title="My Reservations" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">My Reservations</h1>
                    <p class="text-muted-foreground">
                        Manage your current and past book reservations
                    </p>
                </div>
                <Button @click="goToCatalog" class="flex items-center gap-2">
                    <BookOpen class="h-4 w-4" />
                    Browse Books
                </Button>
            </div>

            <!-- Active Reservations -->
            <div v-if="activeReservations.length > 0">
                <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
                    <Calendar class="h-5 w-5" />
                    Active Reservations
                </h2>
                <div class="grid gap-4">
                    <Card v-for="reservation in activeReservations" :key="reservation.id" class="hover:shadow-lg transition-shadow">
                        <CardHeader>
                            <div class="flex items-start justify-between">
                                <div>
                                    <CardTitle class="text-lg">{{ reservation.book.title }}</CardTitle>
                                    <p class="text-sm text-muted-foreground">by {{ reservation.book.author }}</p>
                                    <p v-if="reservation.book.genre" class="text-xs text-muted-foreground">{{ reservation.book.genre }}</p>
                                </div>
                                <Badge variant="default" class="bg-green-100 text-green-800">
                                    {{ reservation.status }}
                                </Badge>
                            </div>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="grid gap-3 md:grid-cols-3">
                                <div>
                                    <Label class="text-xs font-medium text-muted-foreground">Reserved Date</Label>
                                    <p class="text-sm">{{ formatDate(reservation.reserved_at) }}</p>
                                </div>
                                <div>
                                    <Label class="text-xs font-medium text-muted-foreground">Expires On</Label>
                                    <p class="text-sm" :class="getExpiryColorClass(reservation.days_remaining)">
                                        {{ formatDate(reservation.expires_at) }}
                                    </p>
                                </div>
                                <div>
                                    <Label class="text-xs font-medium text-muted-foreground">Days Remaining</Label>
                                    <p class="text-sm font-medium" :class="getExpiryColorClass(reservation.days_remaining)">
                                        {{ reservation.days_remaining || 0 }} days
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Expiry Warning -->
                            <div v-if="reservation.days_remaining && reservation.days_remaining <= 2" 
                                 class="flex items-center gap-2 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                                <AlertTriangle class="h-4 w-4 text-yellow-600" />
                                <span class="text-sm text-yellow-800">
                                    {{ reservation.days_remaining === 1 ? 'Expires tomorrow!' : `Expires in ${reservation.days_remaining} days` }}
                                </span>
                            </div>

                            <div class="flex gap-2 pt-2">
                                <Button 
                                    variant="outline" 
                                    size="sm"
                                    @click="viewBook(reservation.book.id)"
                                >
                                    View Book Details
                                </Button>
                                <Button 
                                    variant="destructive" 
                                    size="sm"
                                    @click="cancelReservation(reservation.id)"
                                    :disabled="cancelling === reservation.id"
                                >
                                    <div v-if="cancelling === reservation.id" class="h-3 w-3 animate-spin rounded-full border-2 border-current border-t-transparent mr-2"></div>
                                    {{ cancelling === reservation.id ? 'Cancelling...' : 'Cancel Reservation' }}
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>

            <!-- No Active Reservations -->
            <div v-else class="text-center py-12">
                <Calendar class="h-12 w-12 mx-auto text-muted-foreground mb-4" />
                <h3 class="text-lg font-medium mb-2">No Active Reservations</h3>
                <p class="text-muted-foreground mb-4">
                    You don't have any active book reservations at the moment.
                </p>
                <Button @click="goToCatalog" class="flex items-center gap-2">
                    <BookOpen class="h-4 w-4" />
                    Browse Book Catalog
                </Button>
            </div>

            <!-- Reservation History -->
            <div v-if="historyReservations.length > 0" class="mt-12">
                <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
                    <History class="h-5 w-5" />
                    Reservation History
                </h2>
                <div class="grid gap-4">
                    <Card v-for="reservation in historyReservations" :key="reservation.id" class="opacity-75 hover:opacity-100 transition-opacity">
                        <CardHeader>
                            <div class="flex items-start justify-between">
                                <div>
                                    <CardTitle class="text-lg">{{ reservation.book.title }}</CardTitle>
                                    <p class="text-sm text-muted-foreground">by {{ reservation.book.author }}</p>
                                    <p v-if="reservation.book.genre" class="text-xs text-muted-foreground">{{ reservation.book.genre }}</p>
                                </div>
                                <Badge :variant="getStatusVariant(reservation.status)">
                                    {{ reservation.status }}
                                </Badge>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div class="grid gap-3 md:grid-cols-4">
                                <div>
                                    <Label class="text-xs font-medium text-muted-foreground">Reserved</Label>
                                    <p class="text-sm">{{ formatDate(reservation.reserved_at) }}</p>
                                </div>
                                <div>
                                    <Label class="text-xs font-medium text-muted-foreground">Expired</Label>
                                    <p class="text-sm">{{ formatDate(reservation.expires_at) }}</p>
                                </div>
                                <div v-if="reservation.collected_at">
                                    <Label class="text-xs font-medium text-muted-foreground">Collected</Label>
                                    <p class="text-sm">{{ formatDate(reservation.collected_at) }}</p>
                                </div>
                                <div>
                                    <Button 
                                        variant="outline" 
                                        size="sm"
                                        @click="viewBook(reservation.book.id)"
                                    >
                                        View Book
                                    </Button>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>

            <!-- No History -->
            <div v-if="activeReservations.length === 0 && historyReservations.length === 0" class="text-center py-12">
                <History class="h-12 w-12 mx-auto text-muted-foreground mb-4" />
                <h3 class="text-lg font-medium mb-2">No Reservation History</h3>
                <p class="text-muted-foreground">
                    Your reservation history will appear here once you start reserving books.
                </p>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type AppPageProps, type BreadcrumbItem, type UserReservationsResponse } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { AlertTriangle, BookOpen, Calendar, History } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface ReservationsPageProps extends AppPageProps {
    reservations: UserReservationsResponse;
}

const page = usePage<ReservationsPageProps>();
const cancelling = ref<number | null>(null);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'My Reservations', href: '/my-reservations' },
];

// Get reservations data or provide defaults
const reservationsData = computed(() => page.props.reservations || {
    active: [],
    history: []
});

const activeReservations = computed(() => reservationsData.value.active);
const historyReservations = computed(() => reservationsData.value.history);

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const getExpiryColorClass = (daysRemaining?: number) => {
    if (!daysRemaining) return 'text-red-600';
    if (daysRemaining <= 1) return 'text-red-600';
    if (daysRemaining <= 3) return 'text-yellow-600';
    return 'text-green-600';
};

const getStatusVariant = (status: string) => {
    switch (status) {
        case 'collected':
            return 'default';
        case 'expired':
            return 'destructive';
        case 'cancelled':
            return 'secondary';
        default:
            return 'outline';
    }
};

const goToCatalog = () => {
    router.visit('/catalog');
};

const viewBook = (bookId: number) => {
    router.visit(`/books/${bookId}/details`);
};

const cancelReservation = async (reservationId: number) => {
    if (cancelling.value) return;
    
    if (!confirm('Are you sure you want to cancel this reservation?')) {
        return;
    }
    
    cancelling.value = reservationId;
    
    router.patch(`/reservations/${reservationId}`, { status: 'cancelled' }, {
        onSuccess: () => {
            // Refresh the page to update the reservations
            router.reload();
        },
        onError: (errors) => {
            console.error('Cancellation failed:', errors);
        },
        onFinish: () => {
            cancelling.value = null;
        }
    });
};
</script>