/**
 * Reservation type definitions
 */

import type { Book } from './Book';

export type ReservationStatus =
    | 'active'
    | 'collected'
    | 'expired'
    | 'cancelled';

export interface Reservation {
    id: number;
    book: Pick<Book, 'id' | 'title' | 'author' | 'genre'>;
    status: ReservationStatus;
    reserved_at: string;
    expires_at: string;
    collected_at?: string;
    days_remaining?: number;
}

export interface CreateReservationRequest {
    book_id: number;
}

export interface UpdateReservationRequest {
    status: 'cancelled';
}
