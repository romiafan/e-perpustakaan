/**
 * User type definitions
 */

export type UserRole = 'member' | 'librarian' | 'admin';

export interface User {
    id: number;
    name: string;
    email: string;
    role: UserRole;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

// Type alias for authenticated user (currently same as User)
export type AuthUser = User;
