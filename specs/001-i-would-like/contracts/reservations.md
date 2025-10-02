# Reservation Endpoints

## POST /reservations

**Purpose**: Create new book reservation

### Request

```typescript
interface CreateReservationRequest {
    book_id: number;
}
```

### Response (201 - Success)

```typescript
interface ReservationResponse {
    reservation: {
        id: number;
        book: {
            id: number;
            title: string;
            author: string;
        };
        status: 'active';
        reserved_at: string; // ISO datetime
        expires_at: string; // ISO datetime (7 days from reserved_at)
    };
    message: string;
}
```

### Response (422 - Business Rule Violation)

```typescript
interface ReservationError {
    message: string;
    errors: {
        book_id?: string[]; // "Book is not available"
        user?: string[]; // "You already have an active reservation"
    };
}
```

## GET /reservations

**Purpose**: Get user's reservations

### Response (200 - Success)

```typescript
interface UserReservationsResponse {
    active: Reservation[];
    history: Reservation[];
}

interface Reservation {
    id: number;
    book: {
        id: number;
        title: string;
        author: string;
        genre: string;
    };
    status: 'active' | 'collected' | 'expired' | 'cancelled';
    reserved_at: string;
    expires_at: string;
    collected_at?: string;
    days_remaining?: number; // Only for active reservations
}
```

## PATCH /reservations/{id}

**Purpose**: Update reservation status (cancel)

### Request

```typescript
interface UpdateReservationRequest {
    status: 'cancelled';
}
```

### Response (200 - Success)

```typescript
interface UpdateReservationResponse {
    reservation: Reservation;
    message: string;
}
```

### Response (403 - Forbidden)

```typescript
interface ForbiddenError {
    message: string; // "Cannot modify this reservation"
}
```

## DELETE /reservations/{id}

**Purpose**: Cancel reservation (alias for PATCH with status=cancelled)

### Response (200 - Success)

```typescript
interface CancelReservationResponse {
    message: string;
}
```
