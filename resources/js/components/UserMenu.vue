<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button 
                :variant="buttonVariant" 
                :size="buttonSize"
                class="relative"
                :class="triggerClass"
            >
                <div class="flex items-center gap-2">
                    <Avatar :size="avatarSize">
                        <AvatarImage :src="user.avatar" :alt="user.name" />
                        <AvatarFallback>
                            {{ getUserInitials(user.name) }}
                        </AvatarFallback>
                    </Avatar>
                    <div v-if="showUserInfo" class="flex flex-col items-start">
                        <span class="text-sm font-medium">{{ user.name }}</span>
                        <span v-if="showEmail" class="text-xs text-muted-foreground">{{ user.email }}</span>
                        <Badge v-if="showRole && user.role" variant="secondary" class="text-xs mt-1">
                            {{ formatRole(user.role) }}
                        </Badge>
                    </div>
                    <ChevronDown v-if="showChevron" class="h-4 w-4" />
                </div>
            </Button>
        </DropdownMenuTrigger>
        
        <DropdownMenuContent :class="menuClass" :align="align" :side="side">
            <!-- User Info Header -->
            <DropdownMenuLabel class="p-0 font-normal">
                <div class="flex items-center gap-3 px-2 py-3 text-left text-sm">
                    <Avatar size="sm">
                        <AvatarImage :src="user.avatar" :alt="user.name" />
                        <AvatarFallback>
                            {{ getUserInitials(user.name) }}
                        </AvatarFallback>
                    </Avatar>
                    <div class="flex flex-col">
                        <span class="font-medium">{{ user.name }}</span>
                        <span class="text-xs text-muted-foreground">{{ user.email }}</span>
                        <Badge v-if="user.role" variant="secondary" class="text-xs w-fit mt-1">
                            {{ formatRole(user.role) }}
                        </Badge>
                    </div>
                </div>
            </DropdownMenuLabel>
            
            <DropdownMenuSeparator />
            
            <!-- Navigation Items -->
            <DropdownMenuGroup>
                <DropdownMenuItem v-if="showDashboard" as-child>
                    <Link href="/dashboard" class="flex items-center">
                        <Home class="mr-2 h-4 w-4" />
                        Dashboard
                    </Link>
                </DropdownMenuItem>
                
                <DropdownMenuItem v-if="showProfile" as-child>
                    <Link href="/profile" class="flex items-center">
                        <UserIcon class="mr-2 h-4 w-4" />
                        My Profile
                    </Link>
                </DropdownMenuItem>
                
                <DropdownMenuItem v-if="showReservations" as-child>
                    <Link href="/my-reservations" class="flex items-center">
                        <BookOpen class="mr-2 h-4 w-4" />
                        My Reservations
                    </Link>
                </DropdownMenuItem>
                
                <DropdownMenuItem v-if="showCatalog" as-child>
                    <Link href="/catalog" class="flex items-center">
                        <Search class="mr-2 h-4 w-4" />
                        Browse Catalog
                    </Link>
                </DropdownMenuItem>
            </DropdownMenuGroup>
            
            <!-- Settings -->
            <DropdownMenuSeparator v-if="showSettings" />
            <DropdownMenuGroup v-if="showSettings">
                <DropdownMenuItem as-child>
                    <Link href="/profile/edit" class="flex items-center">
                        <Settings class="mr-2 h-4 w-4" />
                        Account Settings
                    </Link>
                </DropdownMenuItem>
                
                <DropdownMenuItem v-if="showAppearance" as-child>
                    <Link href="/profile/appearance" class="flex items-center">
                        <Palette class="mr-2 h-4 w-4" />
                        Appearance
                    </Link>
                </DropdownMenuItem>
            </DropdownMenuGroup>
            
            <!-- Logout -->
            <DropdownMenuSeparator />
            <DropdownMenuItem as-child>
                <Link
                    href="/logout"
                    method="post"
                    as="button"
                    class="flex items-center w-full text-destructive focus:text-destructive"
                    @click="handleLogout"
                >
                    <LogOut class="mr-2 h-4 w-4" />
                    Sign Out
                </Link>
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>

<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import type { User } from '@/types';
import { Link, router } from '@inertiajs/vue3';
import {
    BookOpen,
    ChevronDown,
    Home,
    LogOut,
    Palette,
    Search,
    Settings,
    User as UserIcon,
} from 'lucide-vue-next';

interface Props {
    user: User;
    // Appearance options
    buttonVariant?: 'default' | 'outline' | 'ghost' | 'secondary';
    buttonSize?: 'sm' | 'default' | 'lg';
    avatarSize?: 'sm' | 'default' | 'lg';
    triggerClass?: string;
    menuClass?: string;
    align?: 'start' | 'center' | 'end';
    side?: 'top' | 'right' | 'bottom' | 'left';
    
    // Display options
    showUserInfo?: boolean;
    showEmail?: boolean;
    showRole?: boolean;
    showChevron?: boolean;
    
    // Menu items visibility
    showDashboard?: boolean;
    showProfile?: boolean;
    showReservations?: boolean;
    showCatalog?: boolean;
    showSettings?: boolean;
    showAppearance?: boolean;
}

interface Emits {
    (e: 'logout'): void;
}

const props = withDefaults(defineProps<Props>(), {
    buttonVariant: 'ghost',
    buttonSize: 'default',
    avatarSize: 'default',
    triggerClass: '',
    menuClass: 'w-56',
    align: 'end',
    side: 'bottom',
    showUserInfo: true,
    showEmail: false,
    showRole: false,
    showChevron: true,
    showDashboard: true,
    showProfile: true,
    showReservations: true,
    showCatalog: true,
    showSettings: true,
    showAppearance: false,
});

const emit = defineEmits<Emits>();

const getUserInitials = (name: string) => {
    return name
        .split(' ')
        .map(part => part.charAt(0))
        .join('')
        .toUpperCase()
        .slice(0, 2);
};

const formatRole = (role: string) => {
    return role.charAt(0).toUpperCase() + role.slice(1);
};

const handleLogout = () => {
    router.flushAll();
    emit('logout');
};
</script>