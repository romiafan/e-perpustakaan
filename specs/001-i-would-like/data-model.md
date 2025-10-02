# Data Model: Library Management System

## Core Entities

### User Entity

```typescript
interface User {
    id: number;
    email: string;
    name: string;
    password_hash: string;
    role: 'member' | 'librarian' | 'admin';
    email_verified_at: DateTime | null;
    created_at: DateTime;
    updated_at: DateTime;
}
```

**Relationships**:

- hasMany(Reservation)
- hasMany(BorrowingHistory)

**Validation Rules**:

- email: unique, valid email format
- role: enum('member', 'librarian', 'admin')
- name: required, max 255 characters

**Business Rules**:

- Default role: 'member'
- Email verification required for reservations
- Soft deletes to preserve reservation history

### Book Entity

```typescript
interface Book {
    id: number;
    title: string;
    author: string;
    isbn: string;
    genre: string;
    publication_year: number;
    synopsis: text;
    stock_quantity: number;
    available_quantity: number;
    created_at: DateTime;
    updated_at: DateTime;
}
```

**Relationships**:

- hasMany(Reservation)
- hasMany(BorrowingHistory)

**Validation Rules**:

- title: required, max 500 characters
- author: required, max 255 characters
- isbn: unique, format validation (10 or 13 digits)
- genre: required, max 100 characters
- publication_year: integer, range 1000-current year
- stock_quantity: integer, min 0
- available_quantity: integer, min 0, max stock_quantity

**Business Rules**:

- available_quantity = stock_quantity - active_reservations_count
- ISBN must be unique across all books
- Cannot delete books with active reservations

### Reservation Entity

```typescript
interface Reservation {
    id: number;
    user_id: number;
    book_id: number;
    status: 'active' | 'collected' | 'expired' | 'cancelled';
    reserved_at: DateTime;
    expires_at: DateTime;
    collected_at: DateTime | null;
    created_at: DateTime;
    updated_at: DateTime;
}
```

**Relationships**:

- belongsTo(User)
- belongsTo(Book)

**Validation Rules**:

- user_id: exists in users table
- book_id: exists in books table
- status: enum('active', 'collected', 'expired', 'cancelled')
- expires_at: 7 days from reserved_at

**Business Rules**:

- Only one active reservation per user
- Automatically expires after 7 days
- Cannot reserve unavailable books
- Updates book.available_quantity on status change

## Database Schema

### Migration: Create Users Table

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('member', 'librarian', 'admin') DEFAULT 'member',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_role ON users(role);
```

### Migration: Create Books Table

```sql
CREATE TABLE books (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(500) NOT NULL,
    author VARCHAR(255) NOT NULL,
    isbn VARCHAR(13) NOT NULL UNIQUE,
    genre VARCHAR(100) NOT NULL,
    publication_year YEAR NOT NULL,
    synopsis TEXT,
    stock_quantity INT UNSIGNED DEFAULT 0,
    available_quantity INT UNSIGNED DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

CREATE FULLTEXT INDEX idx_books_search ON books(title, author);
CREATE INDEX idx_books_genre ON books(genre);
CREATE INDEX idx_books_year ON books(publication_year);
CREATE INDEX idx_books_isbn ON books(isbn);
CREATE INDEX idx_books_availability ON books(available_quantity);
```

### Migration: Create Reservations Table

```sql
CREATE TABLE reservations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    book_id BIGINT UNSIGNED NOT NULL,
    status ENUM('active', 'collected', 'expired', 'cancelled') DEFAULT 'active',
    reserved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    collected_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
);

CREATE INDEX idx_reservations_user_status ON reservations(user_id, status);
CREATE INDEX idx_reservations_book_status ON reservations(book_id, status);
CREATE INDEX idx_reservations_expires_at ON reservations(expires_at);
CREATE UNIQUE INDEX idx_user_active_reservation ON reservations(user_id, status)
    WHERE status = 'active';
```

## Calculated Fields & Derived Data

### Book Availability Calculation

```php
// Real-time calculation in Book model
public function getAvailableQuantityAttribute(): int
{
    return $this->stock_quantity - $this->activeReservations()->count();
}
```

### User Active Reservation Check

```php
// Business rule enforcement
public function canMakeReservation(): bool
{
    return $this->reservations()
        ->where('status', 'active')
        ->doesntExist();
}
```

### Reservation Expiry Logic

```php
// Automatic expiry via scheduled job
public function markExpiredReservations(): int
{
    return Reservation::where('status', 'active')
        ->where('expires_at', '<', now())
        ->update(['status' => 'expired']);
}
```

## Data Integrity Constraints

### Business Rules Enforced at Database Level

1. **Single Active Reservation**: Unique index on (user_id, status) where status='active'
2. **Book Availability**: Check constraint ensuring available_quantity <= stock_quantity
3. **Reservation Expiry**: Expires_at must be 7 days from reserved_at
4. **ISBN Uniqueness**: Unique constraint on books.isbn
5. **Valid Enums**: Status and role fields use MySQL ENUM constraints

### Application-Level Validations

1. **Reservation Limit**: Validate user has no active reservations before creating new
2. **Book Availability**: Check available_quantity > 0 before reservation
3. **Role Permissions**: Middleware checks for user role authorization
4. **Data Formats**: ISBN format validation, email validation
5. **Date Logic**: Expiry date calculation and validation
