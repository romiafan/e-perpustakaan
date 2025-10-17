import { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon;
    isActive?: boolean;
}

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
    flash?: {
        success?: string;
        error?: string;
    };
};

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    role?: 'member' | 'librarian' | 'admin';
}

// Library Domain Types
export interface Book {
    id: number;
    title: string;
    author: string;
    isbn: string;
    genre: string;
    publication_year: number;
    synopsis: string;
    cover_image?: string;
    stock_quantity: number;
    available_quantity: number;
    is_available: boolean;
    active_reservations_count?: number;
    can_reserve?: boolean;
}

export interface Reservation {
    id: number;
    book: {
        id: number;
        title: string;
        author: string;
        genre?: string;
    };
    status: 'active' | 'collected' | 'expired' | 'cancelled';
    reserved_at: string;
    expires_at: string;
    collected_at?: string;
    days_remaining?: number;
}

// API Response Types
export interface BookCatalogResponse {
    data: Book[];
    meta: {
        current_page: number;
        from: number;
        last_page: number;
        per_page: number;
        to: number;
        total: number;
    };
    filters: {
        genres: string[];
        years: number[];
    };
}

export interface UserReservationsResponse {
    active: Reservation[];
    history: Reservation[];
}

export interface ProfileResponse {
    user: User;
    stats: {
        active_reservations: number;
        total_borrowed: number;
        account_status: 'active' | 'suspended';
    };
    recent_activity: RecentActivity[];
}

export interface RecentActivity {
    type:
        | 'reservation_created'
        | 'reservation_collected'
        | 'reservation_expired';
    book_title: string;
    date: string;
    description: string;
}

export type BreadcrumbItemType = BreadcrumbItem;
