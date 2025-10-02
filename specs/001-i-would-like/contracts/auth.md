# Authentication Endpoints

## POST /login

**Purpose**: Authenticate user and create session

### Request

```typescript
interface LoginRequest {
    email: string;
    password: string;
    remember?: boolean;
}
```

### Response (200 - Success)

```typescript
interface LoginResponse {
    user: {
        id: number;
        name: string;
        email: string;
        role: 'member' | 'librarian' | 'admin';
    };
    redirect: string; // Dashboard route
}
```

### Response (422 - Validation Error)

```typescript
interface ValidationError {
    message: string;
    errors: {
        email?: string[];
        password?: string[];
    };
}
```

## POST /register

**Purpose**: Create new user account

### Request

```typescript
interface RegisterRequest {
    name: string;
    email: string;
    password: string;
    password_confirmation: string;
}
```

### Response (201 - Success)

```typescript
interface RegisterResponse {
    user: {
        id: number;
        name: string;
        email: string;
        role: 'member';
    };
    message: string;
}
```

## POST /logout

**Purpose**: Destroy user session

### Request

```typescript
// No body required
```

### Response (200 - Success)

```typescript
interface LogoutResponse {
    message: string;
    redirect: string; // Login route
}
```
