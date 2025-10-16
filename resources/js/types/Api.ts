/**
 * API Response type definitions
 */

import type { Book, BookDetail } from './Book';
import type { Reservation } from './Reservation';
import type { User } from './User';

/**
 * Pagination metadata
 */
export interface PaginationMeta {
    current_page: number;
    from: number;
    last_page: number;
    per_page: number;
    to: number;
    total: number;
}

/**
 * API Response wrappers
 */
export interface ApiResponse<T> {
    data: T;
    message?: string;
}

export interface PaginatedResponse<T> {
    data: T[];
    meta: PaginationMeta;
}

/**
 * Authentication responses
 */
export interface LoginResponse {
    user: User;
    redirect: string;
}

export interface RegisterResponse {
    user: User;
    message: string;
}

export interface LogoutResponse {
    message: string;
    redirect: string;
}

/**
 * Book responses
 */
export interface BookCatalogResponse extends PaginatedResponse<Book> {
    filters: {
        genres: string[];
        years: number[];
    };
}

export interface BookDetailResponse {
    book: BookDetail;
}

/**
 * Reservation responses
 */
export interface ReservationResponse {
    reservation: Reservation;
    message: string;
}

export interface UserReservationsResponse {
    active: Reservation[];
    history: Reservation[];
}

export interface UpdateReservationResponse {
    reservation: Reservation;
    message: string;
}

export interface CancelReservationResponse {
    message: string;
}

/**
 * Profile responses
 */
export interface ProfileResponse {
    user: User;
    stats: {
        active_reservations: number;
        total_reservations: number;
        completed_reservations: number;
    };
}

export interface UpdateProfileResponse {
    user: User;
    message: string;
}

/**
 * Error responses
 */
export interface ValidationError {
    message: string;
    errors: Record<string, string[]>;
}

export interface ErrorResponse {
    message: string;
    errors?: Record<string, string[]>;
}
