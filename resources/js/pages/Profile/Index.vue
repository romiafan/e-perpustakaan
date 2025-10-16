<template>
    <Head title="My Profile" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">My Profile</h1>
                    <p class="text-muted-foreground">
                        Manage your account information and library statistics
                    </p>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Profile Information -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Information -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <User class="h-5 w-5" />
                                Profile Information
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <form @submit.prevent="updateProfile" class="space-y-4">
                                <div class="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <Label for="name">Full Name</Label>
                                        <Input 
                                            id="name"
                                            v-model="profileForm.name"
                                            required
                                            placeholder="Enter your full name"
                                        />
                                        <div v-if="errors.name" class="text-sm text-red-600 mt-1">
                                            {{ errors.name[0] }}
                                        </div>
                                    </div>
                                    <div>
                                        <Label for="email">Email Address</Label>
                                        <Input 
                                            id="email"
                                            v-model="profileForm.email"
                                            type="email"
                                            required
                                            placeholder="Enter your email address"
                                        />
                                        <div v-if="errors.email" class="text-sm text-red-600 mt-1">
                                            {{ errors.email[0] }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Password Change Section -->
                                <div class="border-t pt-4 mt-6">
                                    <h3 class="text-lg font-medium mb-4">Change Password</h3>
                                    <div class="space-y-4">
                                        <div>
                                            <Label for="current_password">Current Password</Label>
                                            <Input 
                                                id="current_password"
                                                v-model="profileForm.current_password"
                                                type="password"
                                                placeholder="Enter current password"
                                            />
                                            <div v-if="errors.current_password" class="text-sm text-red-600 mt-1">
                                                {{ errors.current_password[0] }}
                                            </div>
                                        </div>
                                        <div class="grid gap-4 md:grid-cols-2">
                                            <div>
                                                <Label for="password">New Password</Label>
                                                <Input 
                                                    id="password"
                                                    v-model="profileForm.password"
                                                    type="password"
                                                    placeholder="Enter new password"
                                                />
                                                <div v-if="errors.password" class="text-sm text-red-600 mt-1">
                                                    {{ errors.password[0] }}
                                                </div>
                                            </div>
                                            <div>
                                                <Label for="password_confirmation">Confirm New Password</Label>
                                                <Input 
                                                    id="password_confirmation"
                                                    v-model="profileForm.password_confirmation"
                                                    type="password"
                                                    placeholder="Confirm new password"
                                                />
                                                <div v-if="errors.password_confirmation" class="text-sm text-red-600 mt-1">
                                                    {{ errors.password_confirmation[0] }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex gap-2 pt-4">
                                    <Button 
                                        type="submit" 
                                        :disabled="updating"
                                        class="flex items-center gap-2"
                                    >
                                        <div v-if="updating" class="h-4 w-4 animate-spin rounded-full border-2 border-current border-t-transparent"></div>
                                        <Save class="h-4 w-4" v-else />
                                        {{ updating ? 'Updating...' : 'Update Profile' }}
                                    </Button>
                                    <Button 
                                        type="button" 
                                        variant="outline"
                                        @click="resetForm"
                                    >
                                        Reset Changes
                                    </Button>
                                </div>
                            </form>
                        </CardContent>
                    </Card>

                    <!-- Account Information -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <Shield class="h-5 w-5" />
                                Account Details
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <Label class="text-sm font-medium">Member Since</Label>
                                    <p class="text-sm text-muted-foreground">
                                        {{ formatDate(profile.user.created_at) }}
                                    </p>
                                </div>
                                <div>
                                    <Label class="text-sm font-medium">Account Role</Label>
                                    <Badge :variant="getRoleVariant(profile.user.role)" class="capitalize">
                                        {{ profile.user.role || 'member' }}
                                    </Badge>
                                </div>
                                <div>
                                    <Label class="text-sm font-medium">Email Verification</Label>
                                    <div class="flex items-center gap-2">
                                        <div class="h-2 w-2 rounded-full" 
                                             :class="profile.user.email_verified_at ? 'bg-green-500' : 'bg-red-500'"></div>
                                        <span class="text-sm">
                                            {{ profile.user.email_verified_at ? 'Verified' : 'Not Verified' }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <Label class="text-sm font-medium">Account Status</Label>
                                    <div class="flex items-center gap-2">
                                        <div class="h-2 w-2 rounded-full bg-green-500"></div>
                                        <span class="text-sm capitalize">{{ profile.stats.account_status }}</span>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Statistics Panel -->
                <div class="space-y-6">
                    <!-- Library Statistics -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <BarChart3 class="h-5 w-5" />
                                Library Statistics
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-muted-foreground">Active Reservations</span>
                                    <span class="text-sm font-medium">{{ profile.stats.active_reservations }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-muted-foreground">Total Borrowed</span>
                                    <span class="text-sm font-medium">{{ profile.stats.total_borrowed }}</span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Recent Activity -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <Clock class="h-5 w-5" />
                                Recent Activity
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div v-if="profile.recent_activity && profile.recent_activity.length > 0" class="space-y-3">
                                <div 
                                    v-for="activity in profile.recent_activity.slice(0, 5)" 
                                    :key="`${activity.type}-${activity.date}`"
                                    class="flex flex-col space-y-1 border-b border-muted pb-3 last:border-0"
                                >
                                    <p class="text-sm font-medium">{{ activity.book_title }}</p>
                                    <p class="text-xs text-muted-foreground">{{ activity.description }}</p>
                                    <p class="text-xs text-muted-foreground">{{ formatDate(activity.date) }}</p>
                                </div>
                            </div>
                            <div v-else class="text-center py-4">
                                <p class="text-sm text-muted-foreground">No recent activity</p>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Quick Actions -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Quick Actions</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-3">
                            <Button variant="outline" class="w-full justify-start" @click="goToReservations">
                                <BookOpen class="h-4 w-4 mr-2" />
                                View My Reservations
                            </Button>
                            <Button variant="outline" class="w-full justify-start" @click="goToCatalog">
                                <Search class="h-4 w-4 mr-2" />
                                Browse Book Catalog
                            </Button>
                            <Button variant="outline" class="w-full justify-start" @click="goToDashboard">
                                <Home class="h-4 w-4 mr-2" />
                                Back to Dashboard
                            </Button>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type AppPageProps, type BreadcrumbItem, type ProfileResponse } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { BarChart3, BookOpen, Clock, Home, Save, Search, Shield, User } from 'lucide-vue-next';
import { reactive, ref } from 'vue';

interface ProfilePageProps extends AppPageProps {
    profile: ProfileResponse;
    errors: Record<string, string[]>;
}

const page = usePage<ProfilePageProps>();
const updating = ref(false);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'My Profile', href: '/my-profile' },
];

// Get profile data from props
const profile = page.props.profile;
const errors = page.props.errors || {};

// Form data
const profileForm = reactive({
    name: profile.user.name,
    email: profile.user.email,
    current_password: '',
    password: '',
    password_confirmation: ''
});

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const getRoleVariant = (role?: string) => {
    switch (role) {
        case 'admin':
            return 'destructive';
        case 'librarian':
            return 'default';
        case 'member':
        default:
            return 'secondary';
    }
};

const updateProfile = () => {
    updating.value = true;
    
    // Only send fields that have values
    const data: any = {
        name: profileForm.name,
        email: profileForm.email
    };
    
    // Add password fields only if current password is provided
    if (profileForm.current_password) {
        data.current_password = profileForm.current_password;
        if (profileForm.password) {
            data.password = profileForm.password;
            data.password_confirmation = profileForm.password_confirmation;
        }
    }
    
    router.patch('/profile', data, {
        onSuccess: () => {
            // Clear password fields on success
            profileForm.current_password = '';
            profileForm.password = '';
            profileForm.password_confirmation = '';
        },
        onError: (errors) => {
            console.error('Profile update failed:', errors);
        },
        onFinish: () => {
            updating.value = false;
        }
    });
};

const resetForm = () => {
    profileForm.name = profile.user.name;
    profileForm.email = profile.user.email;
    profileForm.current_password = '';
    profileForm.password = '';
    profileForm.password_confirmation = '';
};

const goToDashboard = () => {
    router.visit('/dashboard');
};

const goToCatalog = () => {
    router.visit('/catalog');
};

const goToReservations = () => {
    router.visit('/my-reservations');
};
</script>