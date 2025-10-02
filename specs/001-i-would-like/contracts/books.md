# Book Catalog Endpoints

## GET /books

**Purpose**: Search and browse book catalog

### Request Parameters

```typescript
interface BookSearchParams {
    search?: string; // Search in title, author
    genre?: string; // Filter by genre
    year?: number; // Filter by publication year
    isbn?: string; // Search by ISBN
    page?: number; // Pagination
    per_page?: number; // Results per page (max 50)
    sort?: 'title' | 'author' | 'year'; // Sort order
    direction?: 'asc' | 'desc';
}
```

### Response (200 - Success)

```typescript
interface BookCatalogResponse {
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
        genres: string[]; // Available genres for filtering
        years: number[]; // Available publication years
    };
}

interface Book {
    id: number;
    title: string;
    author: string;
    isbn: string;
    genre: string;
    publication_year: number;
    synopsis: string;
    stock_quantity: number;
    available_quantity: number;
    is_available: boolean; // available_quantity > 0
}
```

## GET /books/{id}

**Purpose**: Get detailed book information

### Response (200 - Success)

```typescript
interface BookDetailResponse {
    book: Book & {
        active_reservations_count: number;
        can_reserve: boolean; // User-specific availability
    };
}
```

### Response (404 - Not Found)

```typescript
interface NotFoundError {
    message: string;
}
```
