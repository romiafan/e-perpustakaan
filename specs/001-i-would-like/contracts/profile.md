# User Profile Endpoints

## GET /profile

**Purpose**: Get current user profile and dashboard data

### Response (200 - Success)

```typescript
interface ProfileResponse {
    user: {
        id: number;
        name: string;
        email: string;
        role: 'member' | 'librarian' | 'admin';
        created_at: string;
    };
    stats: {
        active_reservations: number;
        total_borrowed: number;
        account_status: 'active' | 'suspended';
    };
    recent_activity: RecentActivity[];
}

interface RecentActivity {
    type:
        | 'reservation_created'
        | 'reservation_collected'
        | 'reservation_expired';
    book_title: string;
    date: string;
    description: string;
}
```

## PATCH /profile

**Purpose**: Update user profile information

### Request

```typescript
interface UpdateProfileRequest {
    name?: string;
    email?: string;
    current_password?: string; // Required if changing email
    password?: string; // New password
    password_confirmation?: string;
}
```

### Response (200 - Success)

```typescript
interface UpdateProfileResponse {
    user: {
        id: number;
        name: string;
        email: string;
        role: string;
    };
    message: string;
}
```

### Response (422 - Validation Error)

```typescript
interface ProfileValidationError {
    message: string;
    errors: {
        name?: string[];
        email?: string[];
        current_password?: string[];
        password?: string[];
    };
}
```
