<template>
    <Card>
        <CardHeader>
            <CardTitle>Search Books</CardTitle>
            <CardDescription v-if="description">{{ description }}</CardDescription>
        </CardHeader>
        <CardContent>
            <form @submit.prevent="handleSearch" class="space-y-4">
                <div class="grid gap-4 md:grid-cols-4">
                    <div class="md:col-span-2">
                        <Label for="search">Search by title or author</Label>
                        <Input 
                            id="search"
                            v-model="searchQuery"
                            placeholder="Enter book title or author name..."
                            class="w-full"
                            @input="$emit('update:search', searchQuery)"
                        />
                    </div>
                    <div>
                        <Label for="genre">Genre</Label>
                        <select 
                            id="genre"
                            v-model="selectedGenre"
                            @change="$emit('update:genre', selectedGenre)"
                            class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                        >
                            <option value="">All Genres</option>
                            <option v-for="genre in availableGenres" :key="genre" :value="genre">
                                {{ genre }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <Label for="year">Publication Year</Label>
                        <select 
                            id="year"
                            v-model="selectedYear"
                            @change="$emit('update:year', selectedYear)"
                            class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                        >
                            <option value="">All Years</option>
                            <option v-for="year in availableYears" :key="year" :value="year">
                                {{ year }}
                            </option>
                        </select>
                    </div>
                </div>
                
                <!-- Advanced Filters (Optional) -->
                <div v-if="showAdvanced" class="grid gap-4 md:grid-cols-3">
                    <div>
                        <Label for="sort">Sort by</Label>
                        <select 
                            id="sort"
                            v-model="sortBy"
                            @change="$emit('update:sort', sortBy)"
                            class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                        >
                            <option value="title">Title</option>
                            <option value="author">Author</option>
                            <option value="year">Publication Year</option>
                            <option value="genre">Genre</option>
                        </select>
                    </div>
                    <div>
                        <Label for="direction">Order</Label>
                        <select 
                            id="direction"
                            v-model="sortDirection"
                            @change="$emit('update:direction', sortDirection)"
                            class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                        >
                            <option value="asc">Ascending</option>
                            <option value="desc">Descending</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <div class="flex items-center space-x-2">
                            <Checkbox
                                id="available-only"
                                v-model:checked="availableOnly"
                                @update:checked="$emit('update:available-only', availableOnly)"
                            />
                            <Label for="available-only" class="text-sm font-normal">
                                Available books only
                            </Label>
                        </div>
                    </div>
                </div>

                <div class="flex gap-2">
                    <Button 
                        type="submit" 
                        class="flex items-center gap-2"
                        :disabled="loading"
                    >
                        <div v-if="loading" class="h-4 w-4 animate-spin rounded-full border-2 border-current border-t-transparent"></div>
                        <Search v-else class="h-4 w-4" />
                        Search Books
                    </Button>
                    <Button 
                        type="button" 
                        variant="outline" 
                        @click="handleClear"
                        :disabled="loading"
                    >
                        Clear
                    </Button>
                    <Button
                        v-if="!showAdvanced"
                        type="button"
                        variant="ghost"
                        @click="showAdvanced = true"
                    >
                        Advanced Filters
                    </Button>
                    <Button
                        v-else
                        type="button"
                        variant="ghost"
                        @click="showAdvanced = false"
                    >
                        Hide Advanced
                    </Button>
                </div>
            </form>

            <!-- Results Summary -->
            <div v-if="resultsCount !== null" class="mt-4 pt-4 border-t">
                <p class="text-sm text-muted-foreground">
                    {{ resultsCount === 0 ? 'No books found' : `Found ${resultsCount} book${resultsCount === 1 ? '' : 's'}` }}
                    <span v-if="hasActiveFilters" class="ml-1">
                        matching your search criteria
                    </span>
                </p>
                <div v-if="hasActiveFilters" class="flex flex-wrap gap-2 mt-2">
                    <Badge v-if="searchQuery" variant="secondary" class="text-xs">
                        Search: {{ searchQuery }}
                        <Button
                            variant="ghost"
                            size="sm"
                            class="h-auto p-0 ml-2"
                            @click="clearSearch"
                        >
                            ×
                        </Button>
                    </Badge>
                    <Badge v-if="selectedGenre" variant="secondary" class="text-xs">
                        Genre: {{ selectedGenre }}
                        <Button
                            variant="ghost"
                            size="sm"
                            class="h-auto p-0 ml-2"
                            @click="clearGenre"
                        >
                            ×
                        </Button>
                    </Badge>
                    <Badge v-if="selectedYear" variant="secondary" class="text-xs">
                        Year: {{ selectedYear }}
                        <Button
                            variant="ghost"
                            size="sm"
                            class="h-auto p-0 ml-2"
                            @click="clearYear"
                        >
                            ×
                        </Button>
                    </Badge>
                </div>
            </div>
        </CardContent>
    </Card>
</template>

<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Search } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

interface Props {
    search?: string;
    genre?: string;
    year?: string | number;
    sort?: string;
    direction?: string;
    availableGenres?: string[];
    availableYears?: number[];
    loading?: boolean;
    resultsCount?: number | null;
    description?: string;
    availableOnly?: boolean;
}

interface Emits {
    (e: 'search', filters: {
        search?: string;
        genre?: string;
        year?: string | number;
        sort?: string;
        direction?: string;
        availableOnly?: boolean;
    }): void;
    (e: 'clear'): void;
    (e: 'update:search', value: string): void;
    (e: 'update:genre', value: string): void;
    (e: 'update:year', value: string | number): void;
    (e: 'update:sort', value: string): void;
    (e: 'update:direction', value: string): void;
    (e: 'update:available-only', value: boolean): void;
}

const props = withDefaults(defineProps<Props>(), {
    search: '',
    genre: '',
    year: '',
    sort: 'title',
    direction: 'asc',
    availableGenres: () => [],
    availableYears: () => [],
    loading: false,
    resultsCount: null,
    description: '',
    availableOnly: false
});

const emit = defineEmits<Emits>();

const searchQuery = ref(props.search);
const selectedGenre = ref(props.genre);
const selectedYear = ref(props.year);
const sortBy = ref(props.sort);
const sortDirection = ref(props.direction);
const availableOnly = ref(props.availableOnly);
const showAdvanced = ref(false);

// Watch for prop changes to sync with parent
watch(() => props.search, (newVal) => searchQuery.value = newVal);
watch(() => props.genre, (newVal) => selectedGenre.value = newVal);
watch(() => props.year, (newVal) => selectedYear.value = newVal);
watch(() => props.sort, (newVal) => sortBy.value = newVal);
watch(() => props.direction, (newVal) => sortDirection.value = newVal);
watch(() => props.availableOnly, (newVal) => availableOnly.value = newVal);

const hasActiveFilters = computed(() => {
    return !!(searchQuery.value || selectedGenre.value || selectedYear.value);
});

const handleSearch = () => {
    emit('search', {
        search: searchQuery.value || undefined,
        genre: selectedGenre.value || undefined,
        year: selectedYear.value || undefined,
        sort: sortBy.value,
        direction: sortDirection.value,
        availableOnly: availableOnly.value
    });
};

const handleClear = () => {
    searchQuery.value = '';
    selectedGenre.value = '';
    selectedYear.value = '';
    sortBy.value = 'title';
    sortDirection.value = 'asc';
    availableOnly.value = false;
    emit('clear');
};

const clearSearch = () => {
    searchQuery.value = '';
    emit('update:search', '');
    handleSearch();
};

const clearGenre = () => {
    selectedGenre.value = '';
    emit('update:genre', '');
    handleSearch();
};

const clearYear = () => {
    selectedYear.value = '';
    emit('update:year', '');
    handleSearch();
};
</script>