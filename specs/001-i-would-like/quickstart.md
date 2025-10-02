# Quickstart Guide: Library Management System

## System Overview

A modern library management system enabling members to search books, make reservations, and manage their profiles. Features role-based access with Members, Librarians, and Admins.

## Prerequisites

- PHP 8.2+
- Node.js 18+
- MySQL 8.0+
- pnpm package manager
- Composer

## Quick Setup

### 1. Environment Setup

```bash
# Clone repository (assumed to be done)
cd e-perpustakaan

# Install PHP dependencies
composer install

# Install JavaScript dependencies with pnpm
pnpm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 2. Database Configuration

```bash
# Edit .env file with your MySQL credentials
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=e_perpustakaan
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Create database
mysql -u root -p -e "CREATE DATABASE e_perpustakaan"

# Run migrations
php artisan migrate

# Seed sample data
php artisan db:seed
```

### 3. Start Development Server

```bash
# Start all services (Laravel + Vite + Queue + Logs)
composer run dev

# Or start individual services:
# Laravel server: php artisan serve
# Vite dev server: pnpm run dev
# Queue worker: php artisan queue:work
# Log monitoring: php artisan pail
```

## User Journey Testing

### Member Registration & Login

1. **Navigate to**: http://localhost:8000/register
2. **Create account**:
    - Name: "John Doe"
    - Email: "john@example.com"
    - Password: "password123"
    - Confirm password: "password123"
3. **Verify**: Redirected to dashboard with welcome message
4. **Test login**: Logout and login with same credentials

### Book Search & Reservation

1. **Navigate to**: Books catalog from dashboard
2. **Search for book**:
    - Search term: "JavaScript"
    - Filter by genre: "Technology"
    - Sort by: "Title"
3. **Verify**: Results show matching books with availability
4. **Make reservation**:
    - Click on available book
    - Click "Reserve" button
    - Verify success message and updated availability
5. **Check profile**: Verify reservation appears in "My Reservations"

### Reservation Management

1. **View active reservations** in profile
2. **Check expiry date** (should be 7 days from reservation)
3. **Cancel reservation**:
    - Click "Cancel" on active reservation
    - Verify status changes to "cancelled"
    - Verify book becomes available again

### Role-Based Access (Admin/Librarian Testing)

1. **Create admin user** via seeder or manual database entry
2. **Login as admin**: Access admin panel
3. **Manage books**: Add, edit, delete books
4. **Manage users**: View user accounts and roles
5. **View reports**: Access system statistics

## Key Features to Test

### Search Functionality

- **Title search**: "Clean Code"
- **Author search**: "Robert Martin"
- **ISBN search**: "9780132350884"
- **Genre filter**: "Programming"
- **Year filter**: "2008"
- **Combined search**: Title + Genre + Year

### Business Rules Validation

- **Single reservation limit**: Try to make second reservation
- **Unavailable books**: Try to reserve book with 0 availability
- **Expired reservations**: Test automatic expiry (mock date or wait)
- **Concurrent reservations**: Simulate multiple users reserving last copy

### UI/UX Testing

- **Responsive design**: Test on mobile, tablet, desktop
- **Interactive elements**: Hover states, loading indicators
- **Form validation**: Test invalid inputs
- **Navigation**: Breadcrumbs, back buttons, menu items
- **Accessibility**: Keyboard navigation, screen reader compatibility

### Performance Testing

- **Page load times**: Should be <200ms
- **Search response**: Should be <100ms
- **Large catalog**: Test with 1000+ books
- **Concurrent users**: Test with multiple simultaneous sessions

## Sample Test Data

### Users

```
Admin: admin@library.com / password
Librarian: librarian@library.com / password
Member: member@library.com / password
```

### Books (created by seeder)

```
- "Clean Code" by Robert Martin (ISBN: 9780132350884)
- "The Pragmatic Programmer" by Andrew Hunt (ISBN: 9780201616224)
- "Design Patterns" by Gang of Four (ISBN: 9780201633612)
- "JavaScript: The Good Parts" by Douglas Crockford (ISBN: 9780596517748)
```

## Expected Test Results

### Successful Scenarios

✅ User registration completes without errors
✅ Login redirects to appropriate dashboard based on role
✅ Book search returns relevant results
✅ Reservation creation updates availability count
✅ Profile shows accurate reservation history
✅ Admin can manage books and users
✅ Automatic reservation expiry works (7 days)

### Error Handling

✅ Invalid login shows error message
✅ Duplicate reservation attempt blocked
✅ Unavailable book reservation prevented
✅ Form validation displays helpful messages
✅ Session expiry redirects to login

### Performance Benchmarks

✅ Homepage loads in <200ms
✅ Book search responds in <100ms
✅ Reservation creation completes in <500ms
✅ Profile page loads in <150ms

## Troubleshooting

### Common Issues

1. **Vite not loading**: Check pnpm install completed
2. **Database errors**: Verify MySQL connection and migrations
3. **Permission errors**: Check file permissions on storage/logs
4. **Queue not processing**: Ensure queue worker is running
5. **Session issues**: Clear browser cache and Laravel cache

### Debug Commands

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Check logs
php artisan pail

# Run tests
composer run test

# Check database status
php artisan migrate:status
```
