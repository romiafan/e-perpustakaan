<template>
    <Head title="Book Catalog" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Flash Messages -->
            <div v-if="page.props.flash?.success" class="rounded-lg bg-green-50 p-4 dark:bg-green-900/20">
                <p class="text-sm text-green-800 dark:text-green-200">{{ page.props.flash.success }}</p>
            </div>
            <div v-if="page.props.flash?.error" class="rounded-lg bg-red-50 p-4 dark:bg-red-900/20">
                <p class="text-sm text-red-800 dark:text-red-200">{{ page.props.flash.error }}</p>
            </div>

            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Book Catalog</h1>
                    <p class="text-muted-foreground">
                        Discover and reserve books from our collection
                    </p>
                </div>
            </div>

            <!-- Search and Filters -->
            <Card>
                <CardHeader>
                    <CardTitle>Search Books</CardTitle>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="search" class="space-y-4">
                        <div class="grid gap-4 md:grid-cols-4">
                            <div class="md:col-span-2">
                                <Label for="search">Search by title or author</Label>
                                <Input
                                    id="search"
                                    v-model="searchForm.search"
                                    placeholder="Enter book title or author name..."
                                    class="w-full"
                                />
                            </div>
                            <div>
                                <Label for="genre">Genre</Label>
                                <select
                                    id="genre"
                                    v-model="searchForm.genre"
                                    class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                                >
                                    <option value="">All Genres</option>
                                    <option v-for="genre in filters.genres" :key="genre" :value="genre">
                                        {{ genre }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <Label for="year">Publication Year</Label>
                                <select
                                    id="year"
                                    v-model="searchForm.year"
                                    class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                                >
                                    <option value="">All Years</option>
                                    <option v-for="year in filters.years" :key="year" :value="year">
                                        {{ year }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <Button type="submit" class="flex items-center gap-2">
                                <Search class="h-4 w-4" />
                                Search Books
                            </Button>
                            <Button type="button" variant="outline" @click="clearSearch">
                                Clear
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>

            <!-- Results -->
            <div v-if="loading" class="flex justify-center py-8">
                <div class="text-muted-foreground">Loading books...</div>
            </div>

            <div v-else-if="books.length === 0" class="text-center py-8">
                <BookOpen class="h-12 w-12 mx-auto text-muted-foreground mb-4" />
                <h3 class="text-lg font-medium mb-2">No books found</h3>
                <p class="text-muted-foreground">
                    Try adjusting your search criteria or browse all available books.
                </p>
            </div>

            <div v-else class="space-y-6">
                <!-- Results Summary -->
                <div class="flex items-center justify-between">
                    <p class="text-sm text-muted-foreground">
                        Showing {{ meta.from }} to {{ meta.to }} of {{ meta.total }} books
                    </p>
                    <div class="flex items-center gap-2">
                        <Label for="sort">Sort by:</Label>
                        <select
                            id="sort"
                            v-model="sortBy"
                            @change="handleSortChange"
                            class="rounded-md border border-input bg-background px-3 py-1 text-sm"
                        >
                            <option value="title">Title</option>
                            <option value="author">Author</option>
                            <option value="year">Publication Year</option>
                        </select>
                    </div>
                </div>

                <!-- Book Grid -->
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <Card
                        v-for="book in books"
                        :key="book.id"
                        class="hover:shadow-lg transition-shadow cursor-pointer"
                        @click="viewBook(book.id)"
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
                                    @click.stop="viewBook(book.id)"
                                >
                                    View Details
                                </Button>
                                <Button
                                    v-if="book.is_available && book.can_reserve !== false"
                                    size="sm"
                                    class="flex-1"
                                    @click.stop="reserveBook(book.id)"
                                    :disabled="reserving === book.id"
                                >
                                    <div v-if="reserving === book.id" class="h-4 w-4 animate-spin rounded-full border-2 border-current border-t-transparent"></div>
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
                </div>

                <!-- Pagination -->
                <div v-if="meta.last_page > 1" class="flex justify-center space-x-2">
                    <Button
                        variant="outline"
                        :disabled="meta.current_page === 1"
                        @click="changePage(meta.current_page - 1)"
                    >
                        Previous
                    </Button>
                    <div class="flex space-x-1">
                        <Button
                            v-for="page in visiblePages"
                            :key="page"
                            :variant="page === meta.current_page ? 'default' : 'outline'"
                            size="sm"
                            @click="changePage(page)"
                        >
                            {{ page }}
                        </Button>
                    </div>
                    <Button
                        variant="outline"
                        :disabled="meta.current_page === meta.last_page"
                        @click="changePage(meta.current_page + 1)"
                    >
                        Next
                    </Button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type AppPageProps, type BreadcrumbItem, type Book, type BookCatalogResponse } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { BookOpen, Search } from 'lucide-vue-next';
import { computed, reactive, ref } from 'vue';

interface CatalogPageProps extends AppPageProps {
    books: BookCatalogResponse;
}

const page = usePage<CatalogPageProps>();
const loading = ref(false);
const reserving = ref<number | null>(null);
const sortBy = ref('title');

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Book Catalog', href: '/catalog' },
];

const searchForm = reactive({
    search: '',
    genre: '',
    year: '',
    sort: 'title',
    direction: 'asc'
});

// Get data from props or provide defaults
const catalogData = computed(() => page.props.books || {
    data: [],
    meta: {
        current_page: 1,
        from: 0,
        last_page: 1,
        per_page: 12,
        to: 0,
        total: 0
    },
    filters: {
        genres: [],
        years: []
    }
});

const books = computed(() => catalogData.value.data);
const meta = computed(() => catalogData.value.meta);
const filters = computed(() => catalogData.value.filters);

const visiblePages = computed(() => {
    const current = meta.value.current_page;
    const last = meta.value.last_page;
    const delta = 2;
    const range = [];

    for (let i = Math.max(1, current - delta); i <= Math.min(last, current + delta); i++) {
        range.push(i);
    }

    return range;
});

const search = () => {
    loading.value = true;
    router.get('/catalog', {
        search: searchForm.search || undefined,
        genre: searchForm.genre || undefined,
        year: searchForm.year || undefined,
        sort: searchForm.sort,
        direction: searchForm.direction
    }, {
        preserveState: true,
        onFinish: () => {
            loading.value = false;
        }
    });
};

const clearSearch = () => {
    Object.assign(searchForm, {
        search: '',
        genre: '',
        year: '',
        sort: 'title',
        direction: 'asc'
    });
    search();
};

const handleSortChange = () => {
    searchForm.sort = sortBy.value;
    search();
};

const changePage = (page: number) => {
    loading.value = true;
    router.get('/catalog', {
        ...searchForm,
        page
    }, {
        preserveState: true,
        onFinish: () => {
            loading.value = false;
        }
    });
};

const viewBook = (bookId: number) => {
    router.visit(`/books/${bookId}/details`);
};

const reserveBook = async (bookId: number) => {
    if (reserving.value) return;

    reserving.value = bookId;

    router.post('/reservations', { book_id: bookId }, {
        onSuccess: () => {
            // Don't call search() here as it clears flash messages
            // The page will be reloaded automatically by Inertia with updated data
        },
        onError: (errors) => {
            console.error('Reservation failed:', errors);
        },
        onFinish: () => {
            reserving.value = null;
        }
    });
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
