<template>
    <div class="space-y-6">
        <!-- Active Reservations -->
        <div v-if="activeReservations.length > 0">
            <h2 class="text-lg font-semibold mb-4">Active Reservations</h2>
            <div class="space-y-3">
                <Card
                    v-for="reservation in activeReservations"
                    :key="reservation.id"
                    class="hover:shadow-md transition-shadow"
                >
                    <CardContent class="p-4">
                        <div class="flex items-start justify-between">
                            <div class="space-y-2 flex-1">
                                <h3 class="font-medium">{{ reservation.book.title }}</h3>
                                <p class="text-sm text-muted-foreground">
                                    by {{ reservation.book.author }}
                                </p>
                                <div class="flex items-center gap-4 text-sm">
                                    <div class="flex items-center gap-1">
                                        <Calendar class="h-4 w-4" />
                                        <span>Reserved {{ formatDate(reservation.reserved_at) }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <Clock class="h-4 w-4" />
                                        <span>Expires {{ formatDate(reservation.expires_at) }}</span>
                                    </div>
                                </div>
                                <div v-if="reservation.days_remaining !== undefined">
                                    <Badge 
                                        :variant="getDaysRemainingVariant(reservation.days_remaining)"
                                        class="text-xs"
                                    >
                                        {{ getDaysRemainingText(reservation.days_remaining) }}
                                    </Badge>
                                </div>
                            </div>
                            <div class="flex flex-col gap-2 ml-4">
                                <Button
                                    size="sm"
                                    variant="outline"
                                    @click="$emit('view-book', reservation.book.id)"
                                >
                                    View Book
                                </Button>
                                <Button
                                    size="sm"
                                    variant="destructive"
                                    @click="$emit('cancel-reservation', reservation.id)"
                                    :disabled="cancelling === reservation.id"
                                >
                                    <div v-if="cancelling === reservation.id" class="h-4 w-4 animate-spin rounded-full border-2 border-current border-t-transparent"></div>
                                    <span v-else>Cancel</span>
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>

        <!-- History -->
        <div v-if="historyReservations.length > 0">
            <h2 class="text-lg font-semibold mb-4">Reservation History</h2>
            <div class="space-y-3">
                <Card
                    v-for="reservation in historyReservations"
                    :key="reservation.id"
                    class="opacity-75"
                >
                    <CardContent class="p-4">
                        <div class="flex items-start justify-between">
                            <div class="space-y-2 flex-1">
                                <h3 class="font-medium">{{ reservation.book.title }}</h3>
                                <p class="text-sm text-muted-foreground">
                                    by {{ reservation.book.author }}
                                </p>
                                <div class="flex items-center gap-4 text-sm">
                                    <div class="flex items-center gap-1">
                                        <Calendar class="h-4 w-4" />
                                        <span>Reserved {{ formatDate(reservation.reserved_at) }}</span>
                                    </div>
                                    <div v-if="reservation.collected_at" class="flex items-center gap-1">
                                        <CheckCircle class="h-4 w-4" />
                                        <span>Collected {{ formatDate(reservation.collected_at) }}</span>
                                    </div>
                                </div>
                                <Badge :variant="getStatusVariant(reservation.status)" class="text-xs">
                                    {{ getStatusText(reservation.status) }}
                                </Badge>
                            </div>
                            <div class="ml-4">
                                <Button
                                    size="sm"
                                    variant="outline"
                                    @click="$emit('view-book', reservation.book.id)"
                                >
                                    View Book
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>

        <!-- Empty State -->
        <div v-if="activeReservations.length === 0 && historyReservations.length === 0" class="text-center py-8">
            <BookOpen class="h-12 w-12 mx-auto text-muted-foreground mb-4" />
            <h3 class="text-lg font-medium mb-2">No reservations yet</h3>
            <p class="text-muted-foreground mb-4">
                Start exploring our book catalog to make your first reservation.
            </p>
            <Button @click="$emit('browse-catalog')">
                Browse Catalog
            </Button>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import type { Reservation } from '@/types';
import { BookOpen, Calendar, CheckCircle, Clock } from 'lucide-vue-next';

interface Props {
    activeReservations: Reservation[];
    historyReservations: Reservation[];
    cancelling?: number | null;
}

interface Emits {
    (e: 'view-book', bookId: number): void;
    (e: 'cancel-reservation', reservationId: number): void;
    (e: 'browse-catalog'): void;
}

defineProps<Props>();
defineEmits<Emits>();

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const getDaysRemainingVariant = (days: number) => {
    if (days <= 1) return 'destructive';
    if (days <= 3) return 'secondary';
    return 'default';
};

const getDaysRemainingText = (days: number) => {
    if (days < 0) return 'Expired';
    if (days === 0) return 'Expires today';
    if (days === 1) return '1 day remaining';
    return `${days} days remaining`;
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

const getStatusText = (status: string) => {
    switch (status) {
        case 'collected':
            return 'Collected';
        case 'expired':
            return 'Expired';
        case 'cancelled':
            return 'Cancelled';
        case 'active':
            return 'Active';
        default:
            return status.charAt(0).toUpperCase() + status.slice(1);
    }
};
</script>