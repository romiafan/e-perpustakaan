<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type AppPageProps, type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { BookOpen, History, User, Calendar } from 'lucide-vue-next';

const page = usePage<AppPageProps>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Welcome Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">
                        Welcome back, {{ page.props.auth.user.name }}!
                    </h1>
                    <p class="text-muted-foreground">
                        Manage your book reservations and explore our catalog.
                    </p>
                </div>
                <Link href="/catalog">
                    <Button size="lg" class="flex items-center gap-2">
                        <BookOpen class="h-4 w-4" />
                        Browse Books
                    </Button>
                </Link>
            </div>

            <!-- Stats Cards -->
            <div class="grid gap-4 md:grid-cols-3">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">
                            Active Reservations
                        </CardTitle>
                        <Calendar class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">0</div>
                        <p class="text-xs text-muted-foreground">
                            Currently reserved books
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">
                            Total Borrowed
                        </CardTitle>
                        <BookOpen class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">0</div>
                        <p class="text-xs text-muted-foreground">
                            Books borrowed all time
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">
                            Account Status
                        </CardTitle>
                        <User class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold capitalize">Active</div>
                        <p class="text-xs text-muted-foreground">
                            Current account standing
                        </p>
                    </CardContent>
                </Card>
            </div>

            <!-- Quick Actions -->
            <div class="grid gap-4 md:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <BookOpen class="h-5 w-5" />
                            Quick Actions
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-3">
                        <Link href="/catalog" class="block">
                            <Button variant="outline" class="w-full justify-start">
                                Browse Book Catalog
                            </Button>
                        </Link>
                        <Link href="/my-reservations" class="block">
                            <Button variant="outline" class="w-full justify-start">
                                View My Reservations
                            </Button>
                        </Link>
                        <Link href="/my-profile" class="block">
                            <Button variant="outline" class="w-full justify-start">
                                Manage Profile
                            </Button>
                        </Link>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <History class="h-5 w-5" />
                            Recent Activity
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-center text-muted-foreground py-4">
                            <p>No recent activity</p>
                            <p class="text-xs mt-1">Start browsing books to see activity here</p>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
