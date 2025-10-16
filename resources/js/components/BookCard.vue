<template>
    <Card 
        class="hover:shadow-lg transition-shadow cursor-pointer"
        @click="$emit('view', book.id)"
    >
        <CardHeader>
            <CardTitle class="line-clamp-2">{{ book.title }}</CardTitle>
            <p class="text-sm text-muted-foreground">by {{ book.author }}</p>
        </CardHeader>
        <CardContent class="space-y-3">
            <div class="flex justify-between text-sm">
                <span class="text-muted-foreground">Genre:</span>
                <span>{{ book.genre }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-muted-foreground">Year:</span>
                <span>{{ book.publication_year }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-muted-foreground">Available:</span>
                <span :class="book.is_available ? 'text-green-600' : 'text-red-600'">
                    {{ book.available_quantity }} / {{ book.stock_quantity }}
                </span>
            </div>
            <p class="text-sm text-muted-foreground line-clamp-3">
                {{ book.synopsis }}
            </p>
            <div class="flex gap-2 pt-2">
                <Button 
                    variant="outline" 
                    size="sm" 
                    class="flex-1"
                    @click.stop="$emit('view', book.id)"
                >
                    View Details
                </Button>
                <Button 
                    v-if="book.is_available && book.can_reserve !== false"
                    size="sm" 
                    class="flex-1"
                    @click.stop="handleReserve"
                    :disabled="reserving"
                >
                    <div v-if="reserving" class="h-4 w-4 animate-spin rounded-full border-2 border-current border-t-transparent"></div>
                    <span v-else>Reserve</span>
                </Button>
                <Button 
                    v-else
                    size="sm" 
                    variant="secondary"
                    class="flex-1"
                    disabled
                >
                    {{ book.is_available ? 'Cannot Reserve' : 'Unavailable' }}
                </Button>
            </div>
        </CardContent>
    </Card>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { Book } from '@/types';

interface Props {
    book: Book;
    reserving?: boolean;
}

interface Emits {
    (e: 'view', bookId: number): void;
    (e: 'reserve', bookId: number): void;
}

const props = defineProps<Props>();
const emit = defineEmits<Emits>();

const handleReserve = () => {
    emit('reserve', props.book.id);
};
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>