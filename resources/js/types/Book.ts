/**
 * Book type definitions
 */

export interface Book {
    id: number;
    title: string;
    author: string;
    isbn: string;
    genre: string;
    publication_year: number;
    synopsis: string;
    stock_quantity: number;
    available_quantity: number;
    is_available: boolean;
    created_at: string;
    updated_at: string;
}

export interface BookDetail extends Book {
    active_reservations_count: number;
    can_reserve: boolean;
}

export interface BookSearchParams {
    search?: string;
    genre?: string;
    year?: number;
    isbn?: string;
    page?: number;
    per_page?: number;
    sort?: 'title' | 'author' | 'year';
    direction?: 'asc' | 'desc';
}
